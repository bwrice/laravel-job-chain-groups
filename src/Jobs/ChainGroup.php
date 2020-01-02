<?php


namespace Bwrice\LaravelJobChainGroups\Jobs;

use Bwrice\LaravelJobChainGroups\Bus\PendingGroupDispatch;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class ChainGroup
 * @package Bwrice\LaravelJobChainGroups\Jobs
 *
 * @mixin PendingDispatch
 */
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
     * @param $chain
     * @return static
     */
    public static function create(array $asyncJobs, array $chain)
    {
        $groupUuid = Str::uuid();
        $pendingGroupDispatches = collect($asyncJobs)->map(function ($asyncJob) use ($chain, $groupUuid) {
            $groupMemberUuid = Str::uuid();
            $asyncChainedJob = new AsyncChainedJob($groupMemberUuid, $groupUuid, $asyncJob);
            return (new PendingGroupDispatch($groupMemberUuid, $groupUuid, $asyncChainedJob))->chain($chain);
        });
        return new static($groupUuid, $pendingGroupDispatches);
    }

    public function __call($method, $arguments)
    {
        $this->pendingGroupDispatches->each(function (PendingGroupDispatch $pendingGroupDispatch) use ($method, $arguments) {
            $pendingGroupDispatch->$method(...$arguments);
        });
        
        return $this;
    }

}
