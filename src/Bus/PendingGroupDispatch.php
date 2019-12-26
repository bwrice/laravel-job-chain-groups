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
    protected $jobUuid;

    public function __construct(string $jobUuid, string $groupUuid, $job)
    {
        parent::__construct($job);
        $this->jobUuid = $jobUuid;
        $this->groupUuid = $groupUuid;
    }

    public function __destruct()
    {
        ChainGroupMember::query()->create([
            'uuid' => $this->jobUuid,
            'group_uuid' => $this->groupUuid
        ]);
        parent::__destruct();
    }
}
