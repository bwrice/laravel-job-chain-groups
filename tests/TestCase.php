<?php

namespace Bwrice\LaravelJobChainGroups\Tests;

use Illuminate\Database\Schema\Blueprint;
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
        /*
         * Test Schema
         */
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');

        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('preprocessed_at')->nullable();
            $table->dateTime('shipped_at')->nullable();
            $table->timestamps();
        });
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders');
        });

        /*
         * Package Schema
         */
        Schema::dropIfExists('chain_group_members');
        include_once __DIR__.'/../stubs/create_chain_group_members_table.stub.php';
        (new \CreateChainGroupMembersTable())->up();


        /*
         * Jobs table for queue
         */
        Schema::dropIfExists('jobs');
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
    }
}
