<?php


namespace Bwrice\LaravelJobChainGroups\traits;


use Bwrice\LaravelJobChainGroups\jobs\AsyncChainedJob;
use Bwrice\LaravelJobChainGroups\models\ChainGroupMember;

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
