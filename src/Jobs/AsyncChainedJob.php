<?php


namespace Bwrice\LaravelJobChainGroups\Jobs;

use Bwrice\LaravelJobChainGroups\Models\ChainGroupMember;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class AsyncChainedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var mixed */
    public $decoratedJob;

    /** @var string */
    public $jobUuid;

    /** @var string */
    public $groupUuid = '';

    public function __construct(string $jobUuid, $decoratedJob)
    {
        $this->jobUuid = $jobUuid;
        $this->decoratedJob = $decoratedJob;
    }

    public function handle(Container $container)
    {
        $container->call([$this->decoratedJob, 'handle']);
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
