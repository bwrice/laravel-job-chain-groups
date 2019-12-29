<?php


namespace Bwrice\LaravelJobChainGroups\Jobs;

use Bwrice\LaravelJobChainGroups\Models\ChainGroupMember;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Date;

class AsyncChainedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var string */
    protected $groupMemberUuid;

    /** @var string */
    protected $groupUuid;

    /** @var mixed */
    protected $decoratedJob;

    public function __construct(string $groupMemberUuid, string $groupUuid, $decoratedJob)
    {
        $this->groupMemberUuid = $groupMemberUuid;
        $this->groupUuid = $groupUuid;
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
     * @return string
     */
    public function getGroupMemberUuid(): string
    {
        return $this->groupMemberUuid;
    }

    /**
     * @return string
     */
    public function getGroupUuid(): string
    {
        return $this->groupUuid;
    }

    /**
     * @return mixed
     */
    public function getDecoratedJob()
    {
        return $this->decoratedJob;
    }


    public function __call($method, $arguments)
    {
        return $this->decoratedJob->$method(...$arguments);
    }
}
