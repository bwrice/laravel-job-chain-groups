<?php


namespace Bwrice\LaravelJobChainGroups\Jobs;

use Bwrice\LaravelJobChainGroups\Models\ChainGroupMember;
use Illuminate\Bus\Dispatcher;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class AsyncChainedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var string */
    protected $groupMemberUuid;

    /** @var mixed */
    protected $decoratedJob;

    /** @var mixed */
    protected $nextJob;

    public function __construct(string $groupMemberUuid, $decoratedJob, $nextJob)
    {
        $this->groupMemberUuid = $groupMemberUuid;
        $this->decoratedJob = $decoratedJob;
        $this->nextJob = $nextJob;
    }

    public function handle(Container $container)
    {
        /** @var ChainGroupMember $chainGroupMember */
        $chainGroupMember = ChainGroupMember::query()->findOrFail($this->groupMemberUuid);
        $container->call([$this->decoratedJob, 'handle']);

        $chainGroupMember->processed_at = Date::now();
        $chainGroupMember->save();

        if (ChainGroupMember::unprocessedForGroup($chainGroupMember->group_uuid)->count() > 0) {
            app(Dispatcher::class)->dispatch($this->nextJob);
        }
    }

    /**
     * @return mixed
     */
    public function getDecoratedJob()
    {
        return $this->decoratedJob;
    }

    /**
     * @return mixed
     */
    public function getNextJob()
    {
        return $this->nextJob;
    }

    /**
     * @return string
     */
    public function getGroupMemberUuid(): string
    {
        return $this->groupMemberUuid;
    }
}
