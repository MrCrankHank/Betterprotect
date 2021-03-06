<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('priority')->default('7');
            $table->integer('policy_id')->unsigned();
            $table->binary('email')->unique();
            $table->string('fullname')->nullable();
            $table->char('local');
        });
    }
}
