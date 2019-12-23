<?php


namespace Bwrice\LaravelJobChainGroups\Traits;


use Bwrice\LaravelJobChainGroups\Jobs\AsyncChainedJob;
use Bwrice\LaravelJobChainGroups\Models\ChainGroupMember;

trait AsyncChainable
{
    public static function dispatchAsync(string $groupUuid, ...$jobArgs)
    {
        $chainGroupMember = ChainGroupMember::query()->create([
            'group_uuid' => $groupUuid
        ]);
        return AsyncChainedJob::dispatch($chainGroupMember->id, $groupUuid, new static(...$jobArgs));
    }
}
