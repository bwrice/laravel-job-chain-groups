<?php


namespace Bwrice\LaravelJobChainGroups\Tests;


use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\Order;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\OrderItem;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Services\OrderProcessor;

class OrderProcessorTest extends TestCase
{
    /** @var Order */
    protected $order;

    /** @var OrderItem */
    protected $itemOne;

    /** @var OrderItem */
    protected $itemTwo;

    /** @var OrderItem */
    protected $itemThree;

    /** @var OrderProcessor */
    protected $orderProcessor;

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

        $this->orderProcessor = app(OrderProcessor::class);
    }

    /**
    * @test
    */
    public function it_will_process_the_order_items()
    {
        $this->orderProcessor->execute($this->order);

        $this->order->fresh()->orderItems->each(function (OrderItem $item) {
            $this->assertNotNull($item->processed_at);
        });
    }

    /**
    * @test
    */
    public function it_will_ship_the_order()
    {
        $this->orderProcessor->execute($this->order);

        $this->assertNotNull($this->order->fresh()->shipped_at);
    }
}
