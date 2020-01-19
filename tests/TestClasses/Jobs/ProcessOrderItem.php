<?php


namespace Bwrice\LaravelJobChainGroups\Tests\TestClasses\Jobs;


use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\OrderItem;
use Illuminate\Support\Facades\Date;

class ProcessOrderItem
{

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
