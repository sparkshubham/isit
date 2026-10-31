<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->tinyInteger('media_type')->default(0)->comment('1-Image,2-Video');
            $table->string('file_name',255);
            $table->tinyInteger('media_order')->default(0)->comment('Order of media');
            $table->tinyInteger('is_default')->default(0)->comment('0-Not set as default,1-set as default');
            $table->unsignedBigInteger('width')->default(0);
            $table->unsignedBigInteger('height')->default(0);
            $table->string('thumb_image_name',255)->nullable();
            $table->timestamps();
        });
        Schema::table('products_media', function (Blueprint $table) {
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
        Schema::dropIfExists('products_media');
    }
}
