<?php

namespace Bwrice\LaravelJobChainGroups\Tests;

use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();
    }

    protected function setUpDatabase()
    {
        Schema::dropIfExists('chain_group_members');
        include_once __DIR__.'/../stubs/create_chain_group_members_table.stub.php';
        (new \CreateChainGroupMembersTable())->up();
    }
}
