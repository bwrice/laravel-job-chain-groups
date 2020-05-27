<?php


namespace Bwrice\LaravelJobChainGroups\Jobs;

use Bwrice\LaravelJobChainGroups\Bus\PendingChainGroupMemberDispatch;
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
     * @var array
     */
    protected $jobs;
    /**
     * @var array
     */
    protected $chain;

    /** @var Collection */
    protected $methodCalls;

    public function __construct($jobs, array $chain)
    {
        if (is_array($jobs)) {
            $this->jobs = collect($jobs);
        } elseif ($jobs instanceof Collection) {
            $this->jobs = $jobs;
        } else {
            throw new \InvalidArgumentException("jobs must be an array or instance of " . Collection::class);
        }
        $this->groupUuid = Str::uuid();
        $this->methodCalls = collect();
        $this->chain = $chain;
    }

    public static function create($jobs, array $chain)
    {
        return new static($jobs, $chain);
    }

    public function dispatch()
    {
        $this->jobs->map(function ($job) {
            $groupMemberUuid = Str::uuid();
            $asyncChainedJob = new AsyncChainedJob($groupMemberUuid, $this->groupUuid, $job);
            return (new PendingChainGroupMemberDispatch($groupMemberUuid, $this->groupUuid, $asyncChainedJob))->chain($this->chain);
        })->each(function (PendingChainGroupMemberDispatch $dispatch) {
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
     * @return string
     */
    public function getGroupUuid(): string
    {
        return $this->groupUuid;
    }

    /**
     * @return array
     */
    public function getJobs(): array
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
     * @return ChainGroup
     */
    public function setJobs($jobs): ChainGroup
    {
        if ($jobs instanceof Collection) {
            $this->jobs = $jobs;
        } else {
            $this->jobs = collect($jobs);
        }
        return $this;
    }
}
