<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('device_type')->default(0)->comment('1-iOS,2-Android');
            $table->string('current_version',10)->nullable();
            $table->string('min_version',10)->nullable();
            $table->tinyInteger('is_force_update')->default(0)->comment('0-No Force update,1-Force Update');
            $table->string('msg_force_update',255)->nullable();
            $table->string('msg_normal_update',255)->nullable();
            $table->tinyInteger('is_prompt_update')->default(0)->comment('0-No Prompt to update,1-Prompt to update');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_versions');
    }
}
