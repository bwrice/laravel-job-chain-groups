<?php


namespace Bwrice\LaravelJobChainGroups\Traits;


use Bwrice\LaravelJobChainGroups\Bus\PendingGroupDispatch;
use Bwrice\LaravelJobChainGroups\Jobs\AsyncChainedJob;
use Bwrice\LaravelJobChainGroups\Models\ChainGroupMember;
use Illuminate\Support\Str;

trait AsyncChainable
{
    public static function dispatchAsync(string $groupUuid, ...$jobArgs)
    {
        $jobUuid = Str::uuid();
        $asyncChainedJob = new AsyncChainedJob($jobUuid, new static(...$jobArgs));
        return new PendingGroupDispatch($groupUuid, $groupUuid, $asyncChainedJob);
    }
}
