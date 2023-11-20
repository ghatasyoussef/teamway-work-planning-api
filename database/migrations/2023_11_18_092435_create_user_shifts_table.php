<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserShiftsTable extends Migration
{
    public function up()
    {
        Schema::create('user_shifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worker_id');
            $table->date('day');
            $table->integer('shift_number');

            $table->foreign('worker_id')->references('id')->on('users');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_shifts');
    }
}
