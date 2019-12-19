<?php


namespace Bwrice\LaravelJobChainGroups\jobs;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Ramsey\Uuid\UuidInterface;

class ChainGroup
{
    /**
     * @var UuidInterface
     */
    protected $uuid;
    /**
     * @var Collection
     */
    protected $groupMembers;

    public function __construct($groupMembers = [])
    {
        $this->uuid = Str::uuid();
        $this->setGroupMembers($groupMembers);
        $this->setUuids();
    }

    protected function setUuids()
    {
        $this->groupMembers->each(function (AsyncChainedJob $chainGroupMemberJob) {
            $chainGroupMemberJob->setGroupUuid($this->uuid);
        });
    }

    public static function create($groupMembers = [])
    {
        return new static($groupMembers);
    }

    public function setGroupMembers($groupMembers = [])
    {
        if (is_array($groupMembers)) {
            $this->groupMembers = collect($groupMembers);
        }
        if ($groupMembers instanceof Collection) {
            $this->groupMembers = $groupMembers;
        }
        throw new \InvalidArgumentException("groupMembers must be an array or instance of " . Collection::class);
    }

}
