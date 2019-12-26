<?php


namespace Bwrice\LaravelJobChainGroups\Tests;


use Bwrice\LaravelJobChainGroups\Jobs\AsyncChainedJob;
use Bwrice\LaravelJobChainGroups\Models\ChainGroupMember;
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
    /** @var OrderItem */
    protected $orderItem;

    public function setUp(): void
    {
        parent::setUp();

        $this->orderItem = OrderItem::create([
            'order_id' => Order::create()->id
        ]);

    }

    /**
    * @test
    */
    public function it_will_queue_correctly()
    {
        Queue::fake();

        ProcessOrderItem::dispatchAsync(Str::uuid(), $this->orderItem);

        Queue::assertPushed(AsyncChainedJob::class, function (AsyncChainedJob $job) {
            return $job->getDecoratedJob()->orderItem->id === $this->orderItem->id;
        });
    }

    /**
    * @test
    */
    public function it_will_create_a_chain_group_member_in_the_db()
    {
        Queue::fake();

        $groupUuid = Str::uuid();
        ProcessOrderItem::dispatchAsync($groupUuid, $this->orderItem);

        $chainGroupMember = ChainGroupMember::query()->where('group_uuid', '=', $groupUuid)->first();
        $this->assertNotNull($chainGroupMember);
    }
}
