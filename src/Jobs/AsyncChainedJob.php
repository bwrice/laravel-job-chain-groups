<?php


namespace Bwrice\LaravelJobChainGroups\Jobs;

use Bwrice\LaravelJobChainGroups\Models\ChainGroup;
use Bwrice\LaravelJobChainGroups\Models\ChainGroupMember;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Queue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class AsyncChainedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    use Queueable {
        dispatchNextJobInChain as illuminateDispatchNextJobInChain;
    }

    /**
     * @var int
     */
    protected $chainGroupMemberID;
    /**
     * @var int
     */
    protected $chainGroupID;
    /**
     * @var mixed
     */
    protected $decoratedJob;

    public function __construct(int $chainGroupMemberID, int $chainGroupID, $decoratedJob)
    {
        $this->chainGroupMemberID = $chainGroupMemberID;
        $this->chainGroupID = $chainGroupID;
        $this->decoratedJob = $decoratedJob;
    }

    public function handle(Container $container)
    {
        /** @var ChainGroupMember $chainGroupMember */
        $chainGroupMember = ChainGroupMember::query()->findOrFail($this->chainGroupMemberID);
        $container->call([$this->decoratedJob, 'handle']);

        $chainGroupMember->processed_at = Date::now();
        $chainGroupMember->save();
    }

    public function dispatchNextJobInChain()
    {
        /** @var ChainGroup $chainGroup */
        $chainGroup = ChainGroup::query()->findOrFail($this->chainGroupID);
        $unprocessedMembersCount = $chainGroup->chainGroupMembers()
            ->where('processed_at', '=', null)
            ->count();
        /*
         * If there are no more un-processed chain group members, this is the last, or, concurrently, one of the lasts
         * async-chain-jobs for that group to be processed
         */
        if ($unprocessedMembersCount === 0) {
            /*
             * To prevent any race conditions, that could potentially dispatch the next job in the chain twice,
             * we'll re-query for the chain-group using pessimistic locking and update the processed_at time if null
             */
            DB::transaction(function() use ($chainGroup) {

                /** @var ChainGroup $chainGroup */
                $chainGroup = ChainGroup::query()
                    ->where('id', '=', $chainGroup->id)
                    ->lockForUpdate()
                    ->first();

                if (is_null($chainGroup->processed_at)) {

                    $chainGroup->processed_at = now();
                    $chainGroup->save();
                    $this->illuminateDispatchNextJobInChain();
                }
            }, 5);
        }
    }

    /**
     * @return mixed
     */
    public function getDecoratedJob()
    {
        return $this->decoratedJob;
    }


    public function __call($method, $arguments)
    {
        return $this->decoratedJob->$method(...$arguments);
    }

    /**
     * @return int
     */
    public function getChainGroupMemberID(): int
    {
        return $this->chainGroupMemberID;
    }

    /**
     * Get the display name for the given job.
     *
     * @return string
     *
     * @see Queue
     */
    public function displayName()
    {
        if (is_object($this->decoratedJob)) {
            return method_exists($this->decoratedJob, 'displayName')
                ? $this->decoratedJob->displayName() : get_class($this->decoratedJob);
        }
        return is_string($this->decoratedJob) ? explode('@', $this->job)[0] : null;
    }
}
