<?php

namespace Bwrice\LaravelJobChainGroups;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Bwrice\LaravelJobChainGroups\Skeleton\SkeletonClass
 */
class LaravelJobChainGroupsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-job-chain-groups';
    }
}
