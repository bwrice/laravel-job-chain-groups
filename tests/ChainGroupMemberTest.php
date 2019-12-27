<?php


namespace Bwrice\LaravelJobChainGroups\Tests;


use Bwrice\LaravelJobChainGroups\Models\ChainGroupMember;
use Illuminate\Support\Str;

class ChainGroupMemberTest extends TestCase
{
    /**
    * @test
    */
    public function it_can_access_the_database()
    {
        $chainGroupMember = new ChainGroupMember();
        $chainGroupMember->uuid = $uuid = (string) Str::uuid();
        $chainGroupMember->group_uuid = $groupUuid = (string) Str::uuid();
        $chainGroupMember->save();

        /** @var ChainGroupMember $retrievedMember */
        $retrievedMember = ChainGroupMember::query()->find($uuid);
        $this->assertEquals($groupUuid, $retrievedMember->group_uuid);
    }
}
