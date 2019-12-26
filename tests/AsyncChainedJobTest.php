<?php


namespace Bwrice\LaravelJobChainGroups\Tests;


use Bwrice\LaravelJobChainGroups\Jobs\AsyncChainedJob;
use Bwrice\LaravelJobChainGroups\Models\ChainGroupMember;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Jobs\ProcessOrderItem;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Jobs\ShipOrder;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\Order;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\OrderItem;
use Illuminate\Container\Container;
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
    public function it_will_set_the_processed_at_column_on_the_chain_group_member()
    {
        $asyncChainedJob = new AsyncChainedJob($this->groupMemberUuid, $this->decoratedJob, $this->nextJob);

        app(Container::class)->call([$asyncChainedJob, 'handle']);

        $chainGroupMember = ChainGroupMember::query()->find($this->groupMemberUuid);
        $this->assertNotNull($chainGroupMember);
        $this->assertNotNull($chainGroupMember->processed_at);
    }
}
