<?php


namespace Bwrice\LaravelJobChainGroups\traits;


use Bwrice\LaravelJobChainGroups\jobs\AsyncChainedJob;

trait AsyncChainable
{
    public static function dispatchAsync()
    {
        return AsyncChainedJob::dispatch(new static(...func_get_args()));
    }
}
