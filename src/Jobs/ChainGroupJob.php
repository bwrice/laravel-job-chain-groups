<?php


namespace Bwrice\LaravelJobChainGroups\Jobs;

use Bwrice\LaravelJobChainGroups\Models\ChainGroup;
use Bwrice\LaravelJobChainGroups\Models\ChainGroupMember;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class ChainGroup
 * @package Bwrice\LaravelJobChainGroups\Jobs
 *
 * @mixin PendingDispatch
 */
class ChainGroupJob
{
    /**
     * @var Collection
     */
    protected $jobs;
    /**
     * @var array
     */
    protected $chain;

    /** @var Collection */
    protected $methodCalls;

    public function __construct(Collection $jobs, array $chain)
    {
        $this->jobs = $jobs;
        $this->chain = $chain;
        $this->methodCalls = collect();
    }

    public function dispatch()
    {
        /*
         * Build the initial chain-group job that will house all the sub jobs
         */
        /** @var ChainGroup $chainGroup */
        $chainGroup = ChainGroup::query()->create();
        $this->jobs->map(function ($job) use ($chainGroup) {

            /*
             * Map jobs into PendingDispatches of AsyncChainedJobs
             */
            /** @var ChainGroupMember $chainGroupMember */
            $chainGroupMember = ChainGroupMember::query()->create(['chain_group_id' => $chainGroup->id]);
            $asyncChainedJob = new AsyncChainedJob($chainGroupMember->id, $chainGroup->id, $job);

            return (new PendingDispatch($asyncChainedJob))->chain($this->chain);

        })->each(function (PendingDispatch $dispatch) {
            /*
             * Chain any subsequent method calls from this class to each pending dispatch
             */
            $this->methodCalls->each(function ($methodCall) use ($dispatch) {
                $dispatch->{$methodCall['method']}(...$methodCall['arguments']);
            });
        });
    }

    public function push($job)
    {
        $this->jobs->push($job);
        return $this;
    }

    public function merge($jobs)
    {
        $this->jobs = $this->jobs->merge($jobs);
        return $this;
    }

    public function __call($method, $arguments)
    {
        $this->methodCalls->push([
            'method' => $method,
            'arguments' => $arguments
        ]);
        return $this;
    }

    /**
     * @return Collection
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * @return array
     */
    public function getChain(): array
    {
        return $this->chain;
    }

    /**
     * @param array $jobs
     * @return ChainGroupJob
     */
    public function setJobs($jobs): ChainGroupJob
    {
        if ($jobs instanceof Collection) {
            $this->jobs = $jobs;
        } else {
            $this->jobs = collect($jobs);
        }
        return $this;
    }
}
