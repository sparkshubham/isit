<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCmsPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title',255)->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        // create a admin account for staging
        DB::table('cms_pages')->insert(
            array(
                'title' => 'ABOUT US',
                'description' => 'In Spirit In Truth About Us Page',
            )
        );
        DB::table('cms_pages')->insert(
            array(
                'title' => 'PRIVACY POLICY',
                'description' => 'In Spirit In Truth Privacy Policy Page',
            )
        );
        DB::table('cms_pages')->insert(
            array(
                'title' => 'TERMS AND CONDITIONS',
                'description' => 'In Spirit In Truth Terms and Conditions Page',
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cms_pages');
    }
}
