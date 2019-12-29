<?php


namespace Bwrice\LaravelJobChainGroups\Jobs;

use Bwrice\LaravelJobChainGroups\Bus\PendingGroupDispatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Queue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Ramsey\Uuid\UuidInterface;

class ChainGroup
{

    /**
     * @var string
     */
    protected $groupUuid;
    /**
     * @var Collection
     */
    protected $pendingGroupDispatches;

    protected function __construct(string $groupUuid, Collection $pendingGroupDispatches)
    {
        $this->groupUuid = $groupUuid;
        $this->pendingGroupDispatches = $pendingGroupDispatches;
    }

    /**
     * @param array $asyncJobs
     * @param $nextJob
     * @return static
     */
    public static function create(array $asyncJobs, $nextJob)
    {
        $groupUuid = Str::uuid();
        $pendingGroupDispatches = collect($asyncJobs)->map(function ($asyncJob) use ($nextJob, $groupUuid) {
            $groupMemberUuid = Str::uuid();
            $asyncChainedJob = new AsyncChainedJob($groupMemberUuid, $groupUuid, $asyncJob);
            return (new PendingGroupDispatch($groupMemberUuid, $groupUuid, $asyncChainedJob))->chain((array) $nextJob);
        });
        return new static($groupUuid, $pendingGroupDispatches);
    }

}
