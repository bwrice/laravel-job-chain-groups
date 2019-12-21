<?php


namespace Bwrice\LaravelJobChainGroups\jobs;

use Bwrice\LaravelJobChainGroups\models\ChainGroupMember;
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
    protected $job;

    /** @var int */
    protected $jobID;

    /** @var string */
    protected $groupUuid;

    public function __construct(int $jobID, string $groupUuid, $job)
    {
        $this->jobID = $jobID;
        $this->groupUuid = $groupUuid;
        $this->job = $job;
    }

    public function handle(Container $container)
    {
        $container->call([$this->job, 'handle']);
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
    public function getJob()
    {
        return $this->job;
    }
}
