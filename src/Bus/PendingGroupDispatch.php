<?php


namespace Bwrice\LaravelJobChainGroups\Bus;


use Bwrice\LaravelJobChainGroups\Models\ChainGroupMember;
use Illuminate\Foundation\Bus\PendingDispatch;

class PendingGroupDispatch extends PendingDispatch
{
    /**
     * @var string
     */
    public $groupUuid;

    public function __construct(string $groupUuid, $job)
    {
        parent::__construct($job);
        $this->groupUuid = $groupUuid;
    }

    public function __destruct()
    {
        $chainGroupMember = ChainGroupMember::query()->create([
            'group_uuid' => $this->groupUuid
        ]);
        parent::__destruct();
    }
}
