<?php


namespace Bwrice\LaravelJobChainGroups\Tests;


use Bwrice\LaravelJobChainGroups\models\ChainGroupMember;
use Illuminate\Support\Str;

class ChainGroupMemberTest extends TestCase
{
    /**
    * @test
    */
    public function it_can_access_the_database()
    {
        $chainGroupMember = new ChainGroupMember();
        $chainGroupMember->group_uuid = $groupUuid = (string) Str::uuid();
        $chainGroupMember->save();

        /** @var ChainGroupMember $retrievedMember */
        $retrievedMember = ChainGroupMember::query()->find($chainGroupMember->id);
        $this->assertEquals($groupUuid, $retrievedMember->group_uuid);
    }
}
