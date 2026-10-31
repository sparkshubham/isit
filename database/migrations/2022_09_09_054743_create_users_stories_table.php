<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersStoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_stories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->tinyInteger('is_visible')->default(1)->comment('1-Visible to all user,2-Not Visible to all user');
            $table->string('media_name',255);
            $table->tinyInteger('media_type')->default(0)->comment('1-Image,2-Video');
            $table->string('thumb_image_name',255)->nullable();
            $table->unsignedBigInteger('width')->default(0)->nullable();
            $table->unsignedBigInteger('height')->default(0)->nullable();
            $table->timestamps();
        });
        Schema::table('users_stories', function (Blueprint $table) {
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
        Schema::dropIfExists('users_stories');
    }
}
