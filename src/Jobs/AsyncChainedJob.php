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

    /** @var mixed */
    protected $decoratedJob;

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
     * @return mixed
     */
    public function getDecoratedJob()
    {
        return $this->decoratedJob;
    }
    
    /**
     * @return string
     */
    public function getGroupMemberUuid(): string
    {
        return $this->groupMemberUuid;
    }

    public function __call($method, $arguments)
    {
        return $this->decoratedJob->$method(...$arguments);
    }
}
