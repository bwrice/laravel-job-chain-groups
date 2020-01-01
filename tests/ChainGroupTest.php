<?php


namespace Bwrice\LaravelJobChainGroups\Tests;


use Bwrice\LaravelJobChainGroups\Jobs\AsyncChainedJob;
use Bwrice\LaravelJobChainGroups\Jobs\ChainGroup;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Jobs\ProcessOrderItem;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Jobs\ShipOrder;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\Order;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\OrderItem;
use Illuminate\Support\Facades\Queue;

class ChainGroupTest extends TestCase
{
    /** @var Order */
    protected $order;

    /** @var OrderItem */
    protected $itemOne;

    /** @var OrderItem */
    protected $itemTwo;

    /** @var OrderItem */
    protected $itemThree;

    public function setUp(): void
    {
        parent::setUp();

        $this->order = Order::query()->create();
        $this->itemOne = OrderItem::query()->create([
            'order_id' => $this->order->id
        ]);
        $this->itemTwo = OrderItem::query()->create([
            'order_id' => $this->order->id
        ]);
        $this->itemThree = OrderItem::query()->create([
            'order_id' => $this->order->id
        ]);
    }

    /**
     * @test
     */
    public function it_will_dispatch_the_async_jobs()
    {
        Queue::fake();

        ChainGroup::create([
            new ProcessOrderItem($this->itemOne),
            new ProcessOrderItem($this->itemTwo),
            new ProcessOrderItem($this->itemThree)
        ], [
            new ShipOrder($this->order)
        ]);

        Queue::assertPushed(AsyncChainedJob::class, 3);

        foreach([
            $this->itemOne,
            $this->itemTwo,
            $this->itemThree
                ] as $item) {
            Queue::assertPushed(AsyncChainedJob::class, function (AsyncChainedJob $asyncChainedJob) use ($item) {
                return $asyncChainedJob->getDecoratedJob()->orderItem->id === $item->id;
            });
        }
    }

    /**
    * @test
    */
    public function it_will_chain_a_single_job()
    {
        Queue::fake();

        $shipOrderJob = new ShipOrder($this->order);
        ChainGroup::create([
            new ProcessOrderItem($this->itemOne),
            new ProcessOrderItem($this->itemTwo),
            new ProcessOrderItem($this->itemThree)
        ], [
            $shipOrderJob
        ]);

        foreach([
                    $this->itemOne,
                    $this->itemTwo,
                    $this->itemThree
                ] as $item) {
            Queue::assertPushedWithChain(AsyncChainedJob::class, [
                $shipOrderJob
            ], function (AsyncChainedJob $asyncChainedJob) use ($item) {
                return $asyncChainedJob->getDecoratedJob()->orderItem->id === $item->id;
            });
        }
    }
}