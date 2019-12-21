<?php


namespace Bwrice\LaravelJobChainGroups\Tests;


use Bwrice\LaravelJobChainGroups\jobs\AsyncChainedJob;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Jobs\PreProcessOrder;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\Order;
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
        $order = Order::create();

        Queue::fake();

        PreProcessOrder::dispatchAsync($order);

        Queue::assertPushed(AsyncChainedJob::class, function (AsyncChainedJob $chainedJob) use ($order) {
            return $chainedJob->getJob()->order->id === $order->id;
        });
    }
}
