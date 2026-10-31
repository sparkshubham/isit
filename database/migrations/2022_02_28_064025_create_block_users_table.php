<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('block_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('block_user_id');
            $table->unsignedBigInteger('block_by');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('block_user_id')->references('id')->on('users');
            $table->foreign('block_by')->references('id')->on('users');
            $table->timestamps();
        });
        Schema::table('block_users', function (Blueprint $table) {
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
        Schema::dropIfExists('block_users');
    }
}
