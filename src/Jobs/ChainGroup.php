<?php


namespace Bwrice\LaravelJobChainGroups\Jobs;

use Bwrice\LaravelJobChainGroups\Bus\PendingGroupDispatch;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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

    public function __call($method, $arguments)
    {
        $this->pendingGroupDispatches->each(function (PendingGroupDispatch $pendingGroupDispatch) use ($method, $arguments) {
            $pendingGroupDispatch->$method(...$arguments);
        });
    }

}
