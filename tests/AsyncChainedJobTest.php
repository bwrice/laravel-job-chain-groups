<?php


namespace Bwrice\LaravelJobChainGroups\Tests;


use Bwrice\LaravelJobChainGroups\Jobs\AsyncChainedJob;
use Bwrice\LaravelJobChainGroups\Models\ChainGroup;
use Bwrice\LaravelJobChainGroups\Models\ChainGroupMember;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Jobs\ProcessOrderItem;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Jobs\ShipOrder;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\Order;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\OrderItem;
use Illuminate\Container\Container;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

class AsyncChainedJobTest extends TestCase
{
    use DatabaseTransactions;

    /** @var ChainGroup */
    public $chainGroup;

    /** @var ChainGroupMember */
    public $chainGroupMember;

    /** @var ProcessOrderItem */
    public $decoratedJob;

    /** @var ShipOrder */
    public $nextJob;

    /** @var Order */
    public $order;

    /** @var OrderItem */
    public $orderItem;

    public function setUp(): void
    {
        parent::setUp();

        $this->chainGroup = ChainGroup::query()->create();
        $this->chainGroupMember = ChainGroupMember::query()->create([
            'chain_group_id' => $this->chainGroup->id
        ]);

        $this->order = Order::query()->create();

        $this->orderItem = OrderItem::query()->create([
            'order_id' => Order::query()->create()->id
        ]);

        $this->decoratedJob = new ProcessOrderItem($this->orderItem);
        $this->nextJob = new ShipOrder($this->order);
    }

    /**
    * @test
    */
    public function it_will_push_to_the_queue()
    {
        Queue::fake();

        AsyncChainedJob::dispatch($this->chainGroupMember->id, $this->chainGroup->id, $this->decoratedJob);

        Queue::assertPushed(AsyncChainedJob::class, function (AsyncChainedJob $job) {
            return $job->getChainGroupMemberID() === $this->chainGroupMember->id;
        });
    }

    /**
    * @test
    */
    public function it_will_set_the_processed_at_column_on_the_chain_group_member()
    {
        $asyncChainedJob = new AsyncChainedJob($this->chainGroupMember->id, $this->chainGroup->id, $this->decoratedJob);

        app(Container::class)->call([$asyncChainedJob, 'handle']);

        $chainGroupMember = $this->chainGroupMember->fresh();
        $this->assertNotNull($chainGroupMember->processed_at);
    }

    /**
    * @test
    */
    public function it_will_dispatch_the_next_job_if_all_members_of_the_group_are_processed()
    {
        $this->chainGroupMember->processed_at = Date::now();
        $this->chainGroupMember->save();

        Queue::fake();

        $asyncChainedJob = (new AsyncChainedJob($this->chainGroupMember->id, $this->chainGroup->id, $this->decoratedJob))->chain([
            $this->nextJob
        ]);

        $asyncChainedJob->dispatchNextJobInChain();

        $unprocessedCount = $this->chainGroup->chainGroupMembers()
            ->whereNull('processed_at')
            ->count();

        $this->assertEquals(0, $unprocessedCount);

        Queue::assertPushed(ShipOrder::class, function (ShipOrder $job) {
            return $job->order->id === $this->order->id;
        });
    }

    /**
    * @test
    */
    public function it_will_NOT_dispatch_the_next_job_if_there_are_members_of_the_group_unprocessed()
    {
        /** @var ChainGroupMember $unProcessedMember */
        $unProcessedMember = ChainGroupMember::query()->create([
            'chain_group_id' => $this->chainGroup->id
        ]);

        $this->assertNull($unProcessedMember->processed_at);

        Queue::fake();

        $this->chainGroupMember->processed_at = Date::now();
        $this->chainGroupMember->save();

        $asyncChainedJob = (new AsyncChainedJob($this->chainGroupMember->id, $this->chainGroup->id, $this->decoratedJob))->chain([
            $this->nextJob
        ]);

        $asyncChainedJob->dispatchNextJobInChain();

        $unprocessedCount = $this->chainGroup->chainGroupMembers()
            ->whereNull('processed_at')
            ->count();

        $this->assertEquals(1, $unprocessedCount);

        Queue::assertNotPushed(ShipOrder::class, function (ShipOrder $job) {
            return $job->order->id === $this->order->id;
        });
    }
}
