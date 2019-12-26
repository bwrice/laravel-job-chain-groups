<?php


namespace Bwrice\LaravelJobChainGroups\Tests;


use Bwrice\LaravelJobChainGroups\Jobs\AsyncChainedJob;
use Bwrice\LaravelJobChainGroups\Models\ChainGroupMember;
use Illuminate\Support\Str;

class AsyncChainedJobTest extends TestCase
{
    /** @var string */
    public $groupMemberUuid;

    /** @var string */
    public $groupUuid;

    /** @var ChainGroupMember */
    public $chainGroupMember;

    public $decoratedJob;

    public function setUp(): void
    {
        parent::setUp();

        $this->groupMemberUuid = (string) Str::uuid();
        $this->groupUuid = (string) Str::uuid();
        $this->chainGroupMember = ChainGroupMember::query()->create([
            'uuid' => $this->groupMemberUuid,
            'group_uuid' => $this->groupUuid
        ]);

        $this->decoratedJob = new class {

            public function handle() {

            }
        };
    }

    /**
    * @test
    */
    public function it_will_set_the_processed_at_column_on_the_chain_group_member()
    {
        AsyncChainedJob::dispatchNow($this->groupMemberUuid, $this->decoratedJob);

        $chainGroupMember = ChainGroupMember::query()->find($this->groupMemberUuid);
        $this->assertNotNull($chainGroupMember);
        $this->assertNotNull($chainGroupMember->processed_at);
    }
}
