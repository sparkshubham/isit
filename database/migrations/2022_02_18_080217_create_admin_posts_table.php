<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_posts', function (Blueprint $table) {
            $table->id();
            $table->longText('description')->nullable();
            $table->string('media_name',255)->nullable();
            $table->tinyInteger('media_type')->default(0)->comment('1-Image,2-Video');
            $table->tinyInteger('post_type')->default(1)->comment('1-Daily Devotional');
            $table->tinyInteger('status')->default(1)->comment('0-disable,1-enable');
            $table->timestamps();
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
        Schema::dropIfExists('admin_posts');
    }
}
