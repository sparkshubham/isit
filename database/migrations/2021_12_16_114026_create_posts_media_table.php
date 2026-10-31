<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->foreign('post_id')->references('id')->on('posts');
            $table->tinyInteger('media_type')->default(0)->comment('1-Image,2-Video');
            $table->string('file_name',255);
            $table->tinyInteger('media_order')->default(0)->comment('Order of media');
            $table->tinyInteger('is_default')->default(0)->comment('0-Not set as default,1-set as default');
            $table->timestamps();
        });
        Schema::table('posts_media', function (Blueprint $table) {
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
        Schema::dropIfExists('posts_media');
    }
}
