<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

final class CreateChainGroupMembersTable extends Migration
{
    public function up()
    {
        Schema::create('chain_group_members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('chain_group_id')->unsigned();
            $table->foreign('chain_group_id')->references('id')->on('chain_groups');
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();
        });
    }
}
