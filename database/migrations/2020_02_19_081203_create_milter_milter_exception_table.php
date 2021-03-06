<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMilterMilterExceptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('milter_milter_exception', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('milter_id');
            $table->unsignedInteger('milter_exception_id');
            $table->timestamps();

            $table->foreign('milter_id')
                ->references('id')
                ->on('milters')
                ->onDelete('cascade');

            $table->foreign('milter_exception_id')
                ->references('id')
                ->on('milter_exceptions')
                ->onDelete('cascade');
        });
    }
}
