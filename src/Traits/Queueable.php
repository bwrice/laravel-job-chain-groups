<?php


namespace Bwrice\LaravelJobChainGroups\Traits;

use Bwrice\LaravelJobChainGroups\Models\ChainGroupMember;
use Illuminate\Bus\Queueable as IlluminateQueueable;

trait Queueable
{
    /** @var string */
    public $groupUuid;

    use IlluminateQueueable {
        dispatchNextJobInChain as illuminateDispatchNextJobInChain;
    }

    public function dispatchNextJobInChain()
    {
        if (ChainGroupMember::unprocessedForGroup($this->groupUuid)->count() === 0) {
            $this->illuminateDispatchNextJobInChain();
        }
    }
}
