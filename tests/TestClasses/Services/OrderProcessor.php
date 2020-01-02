<?php


namespace Bwrice\LaravelJobChainGroups\Tests\TestClasses\Services;


use Bwrice\LaravelJobChainGroups\Jobs\ChainGroup;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Jobs\ProcessOrderItem;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Jobs\ShipOrder;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\Order;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\OrderItem;

class OrderProcessor
{
    public function execute(Order $order)
    {
        ChainGroup::create($this->getProcessItemJobs($order)->toArray(), [
            new ShipOrder($order)
        ]);
    }

    protected function getProcessItemJobs(Order $order)
    {
        return $order->orderItems->map(function (OrderItem $orderItem) {
            return new ProcessOrderItem($orderItem);
        });
    }
}
