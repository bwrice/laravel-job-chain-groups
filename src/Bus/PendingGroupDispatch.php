<?php


namespace Bwrice\LaravelJobChainGroups\Bus;


use Bwrice\LaravelJobChainGroups\Models\ChainGroupMember;
use Illuminate\Foundation\Bus\PendingDispatch;

class PendingGroupDispatch extends PendingDispatch
{
    /**
     * @var string
     */
    protected $groupUuid;
    /**
     * @var string
     */
    protected $groupMemberUuid;

    public function __construct(string $groupMemberUuid, string $groupUuid, $job)
    {
        parent::__construct($job);
        $this->groupMemberUuid = $groupMemberUuid;
        $this->groupUuid = $groupUuid;
    }

    public function __destruct()
    {
        ChainGroupMember::query()->create([
            'uuid' => $this->groupMemberUuid,
            'group_uuid' => $this->groupUuid
        ]);
        parent::__destruct();
    }
}
