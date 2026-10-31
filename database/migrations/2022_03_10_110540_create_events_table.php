<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('name',255)->nullable();
            $table->date('date')->nullable();
            $table->timestamp('start_time')->nullable()->comment('Start time in GMT');
            $table->timestamp('end_time')->nullable()->comment('End time in GMT');
            $table->string('timeoffset',10)->nullable();
            $table->tinyInteger('admin_status')->default(0)->comment('1-Accepted,2-Declined');
            $table->tinyInteger('status')->default(1)->comment('1-Active,0-InActive');
            $table->timestamps();
        });
        Schema::table('events', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
