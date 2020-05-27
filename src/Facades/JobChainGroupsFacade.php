<?php

namespace Bwrice\LaravelJobChainGroups;

use Illuminate\Support\Facades\Facade;

class JobChainGroupsFacade extends Facade
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
