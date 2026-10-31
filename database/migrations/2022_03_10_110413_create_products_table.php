<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('product_type')->default(0)->comment('1-Want to But,2-Want to sell');
            $table->string('name',255)->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('category_name',255)->nullable();
            $table->string('city',255)->nullable();
            $table->string('zip_code',10)->nullable();
            $table->tinyInteger('price_type')->default(0)->comment('1-Free,2-Money');
            $table->decimal('price',10,2)->nullable();
            $table->tinyInteger('status')->default(1)->comment('1-Active,0-Not active,2-Sold');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->timestamps();
        });
        Schema::table('products', function (Blueprint $table) {
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
        Schema::dropIfExists('products');
    }
}
