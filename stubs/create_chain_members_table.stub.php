<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

final class CreateStoredEventsTable extends Migration
{
    public function up()
    {
        Schema::create('chain_group_members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('group_uuid');
            $table->dateTime('finalized_at')->nullable();
        });
    }
}
