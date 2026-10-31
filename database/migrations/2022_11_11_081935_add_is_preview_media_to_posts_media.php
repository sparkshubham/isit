<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPreviewMediaToPostsMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts_media', function (Blueprint $table) {
            $table->tinyInteger('is_preview_media')->default(0)->comment('0-Not Preview Media,1-Preview Media');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts_media', function (Blueprint $table) {
            //
        });
    }
}
