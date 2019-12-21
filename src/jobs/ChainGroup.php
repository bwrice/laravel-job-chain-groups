<?php


namespace Bwrice\LaravelJobChainGroups\jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Ramsey\Uuid\UuidInterface;

class ChainGroup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $this->validateGroupMembers();
    }

    public function handle()
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

    protected function validateGroupMembers()
    {
        $this->groupMembers->each(function ($groupMember) {
            if (! $groupMember instanceof AsyncChainedJob) {
                throw new \InvalidArgumentException("group member must be in instance of " . AsyncChainedJob::class);
            }
        });
    }

}
