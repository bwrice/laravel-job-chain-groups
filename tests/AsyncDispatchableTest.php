<?php


namespace Bwrice\LaravelJobChainGroups\Tests;


use Bwrice\LaravelJobChainGroups\jobs\AsyncChainedJob;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Jobs\ProcessOrderItem;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\Order;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\OrderItem;
use Illuminate\Bus\Dispatcher;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

class AsyncDispatchableTest extends TestCase
{
    /**
    * @test
    */
    public function it_will_queue_correctly()
    {
        $orderItem = OrderItem::create([
            'order_id' => Order::create()->id
        ]);

        Queue::fake();

        ProcessOrderItem::dispatchAsync(Str::uuid(), $orderItem);

        Queue::assertPushed(AsyncChainedJob::class, function (AsyncChainedJob $chainedJob) use ($orderItem) {
            return $chainedJob->getJob()->orderItem->id === $orderItem->id;
        });
    }
}
