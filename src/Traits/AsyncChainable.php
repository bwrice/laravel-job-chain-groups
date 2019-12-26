<?php


namespace Bwrice\LaravelJobChainGroups\Traits;


use Bwrice\LaravelJobChainGroups\Bus\PendingGroupDispatch;
use Bwrice\LaravelJobChainGroups\Jobs\AsyncChainedJob;
use Bwrice\LaravelJobChainGroups\Models\ChainGroupMember;

trait AsyncChainable
{
    public static function dispatchAsync(string $groupUuid, ...$jobArgs)
    {
        return new PendingGroupDispatch($groupUuid, new static(...$jobArgs));
    }
}
