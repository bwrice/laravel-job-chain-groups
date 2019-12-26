<?php


namespace Bwrice\LaravelJobChainGroups\Tests;


use Bwrice\LaravelJobChainGroups\Jobs\AsyncChainedJob;
use Bwrice\LaravelJobChainGroups\Models\ChainGroupMember;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Jobs\ProcessOrderItem;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Jobs\ShipOrder;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\Order;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\OrderItem;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

class AsyncChainedJobTest extends TestCase
{
    /** @var string */
    public $groupMemberUuid;

    /** @var string */
    public $groupUuid;

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

        $this->groupMemberUuid = (string) Str::uuid();
        $this->groupUuid = (string) Str::uuid();
        $this->chainGroupMember = ChainGroupMember::query()->create([
            'uuid' => $this->groupMemberUuid,
            'group_uuid' => $this->groupUuid
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

        AsyncChainedJob::dispatch($this->groupMemberUuid, $this->decoratedJob, $this->nextJob);

        Queue::assertPushed(AsyncChainedJob::class, function (AsyncChainedJob $job) {
            return $job->getGroupMemberUuid() === $this->groupMemberUuid;
        });
    }

    /**
    * @test
    */
    public function it_will_set_the_processed_at_column_on_the_chain_group_member()
    {
        $asyncChainedJob = new AsyncChainedJob($this->groupMemberUuid, $this->decoratedJob, $this->nextJob);

        app(Container::class)->call([$asyncChainedJob, 'handle']);

        $chainGroupMember = ChainGroupMember::query()->find($this->groupMemberUuid);
        $this->assertNotNull($chainGroupMember);
        $this->assertNotNull($chainGroupMember->processed_at);
    }

    /**
    * @test
    */
    public function it_will_dispatch_the_next_job_if_all_members_of_the_group_are_processed()
    {
        Queue::fake();

        $asyncChainedJob = new AsyncChainedJob($this->groupMemberUuid, $this->decoratedJob, $this->nextJob);

        app(Container::class)->call([$asyncChainedJob, 'handle']);

        $unprocessedCount = ChainGroupMember::query()
            ->where('group_uuid', $this->groupUuid)
            ->whereNull('processed_at')->count();

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
        ChainGroupMember::query()->create([
            'uuid' => Str::uuid(),
            'group_uuid' => $this->groupUuid
        ]);

        Queue::fake();

        $asyncChainedJob = new AsyncChainedJob($this->groupMemberUuid, $this->decoratedJob, $this->nextJob);

        app(Container::class)->call([$asyncChainedJob, 'handle']);

        $unprocessedCount = ChainGroupMember::query()
            ->where('group_uuid', $this->groupUuid)
            ->whereNull('processed_at')->count();

        $this->assertEquals(1, $unprocessedCount);

        Queue::assertNotPushed(ShipOrder::class, function (ShipOrder $job) {
            return $job->order->id === $this->order->id;
        });
    }
}
