<?php


namespace Bwrice\LaravelJobChainGroups\Tests\TestClasses\Jobs;


use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\OrderItem;
use Bwrice\LaravelJobChainGroups\Traits\AsyncChainable;
use Illuminate\Support\Facades\Date;

class ProcessOrderItem
{
    use AsyncChainable;

    /** @var OrderItem */
    public  $orderItem;

    public function __construct(OrderItem $orderItem)
    {
        $this->orderItem = $orderItem;
    }

    public function handle()
    {
        $this->orderItem->processed_at = Date::now();
        $this->orderItem->save();
    }
}
