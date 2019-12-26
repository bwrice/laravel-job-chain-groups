<?php


namespace Bwrice\LaravelJobChainGroups\Jobs;

use Bwrice\LaravelJobChainGroups\Models\ChainGroupMember;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class AsyncChainedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var mixed */
    public $decoratedJob;

    /** @var string */
    public $groupMemberUuid;

    /** @var string */
    public $groupUuid = '';

    public function __construct(string $groupMemberUuid, $decoratedJob)
    {
        $this->groupMemberUuid = $groupMemberUuid;
        $this->decoratedJob = $decoratedJob;
    }

    public function handle(Container $container)
    {
        /** @var ChainGroupMember $chainGroupMember */
        $chainGroupMember = ChainGroupMember::query()->findOrFail($this->groupMemberUuid);
        $container->call([$this->decoratedJob, 'handle']);

        $chainGroupMember->processed_at = Date::now();
        $chainGroupMember->save();
    }

    /**
     * @param string $groupUuid
     * @return AsyncChainedJob
     */
    public function setGroupUuid(string $groupUuid): AsyncChainedJob
    {
        $this->groupUuid = $groupUuid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDecoratedJob()
    {
        return $this->decoratedJob;
    }
}
