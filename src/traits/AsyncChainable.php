<?php


namespace Bwrice\LaravelJobChainGroups\traits;


use Bwrice\LaravelJobChainGroups\jobs\AsyncChainedJob;

trait AsyncChainable
{
    public static function dispatchAsync(string $groupUuid, ...$jobArgs)
    {
        return AsyncChainedJob::dispatch($groupUuid, new static(...$jobArgs));
    }
}
