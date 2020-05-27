<?php

namespace Bwrice\LaravelJobChainGroups\Facades;

use Bwrice\LaravelJobChainGroups\Jobs\ChainGroupJob;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * Class JobChainGroups
 * @package Bwrice\LaravelJobChainGroups\Facades
 *
 * @method static ChainGroupJob create(array|Collection $jobs, array $chain)
 *
 * @see \Bwrice\LaravelJobChainGroups\Services\JobChainGroups
 */
class JobChainGroups extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'job-chain-groups';
    }
}
