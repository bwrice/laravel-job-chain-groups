<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

final class CreateChainGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('chain_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();
        });
    }
}
