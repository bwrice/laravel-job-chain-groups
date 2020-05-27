<?php

namespace Bwrice\LaravelJobChainGroups\Services;

use Bwrice\LaravelJobChainGroups\Jobs\ChainGroupJob;
use Illuminate\Support\Collection;

class JobChainGroups
{
    /**
     * @param $jobs
     * @param array $chain
     * @return ChainGroupJob
     */
    public static function create($jobs, array $chain)
    {
        if (is_array($jobs)) {
            $jobs = collect($jobs);
        } elseif (! $jobs instanceof Collection) {
            throw new \InvalidArgumentException("jobs must be an array or instance of " . Collection::class);
        }
        return new ChainGroupJob($jobs, $chain);
    }
}
