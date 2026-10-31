<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFirstNameUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name',255)->nullable();
            $table->string('last_name',255)->nullable();
            $table->string('phone_number',50)->nullable();
            $table->string('profile_picture',255)->nullable();
            $table->string('bio_text',255)->nullable();
            $table->tinyInteger('is_public_visibility')->default(1)->comment('0-Not visible,1-Visible');
            $table->tinyInteger('status')->default(1)->comment('0-disable,1-enable');
            $table->tinyInteger('device_type')->default(0)->comment('1-iOS,2-Android,3-Web');
            $table->string('device_id',255)->nullable();
            $table->tinyInteger('is_verified')->default(0)->comment('0-Not verified,1-Verified');
            $table->string('verifiy_token',50)->nullable();
            $table->string('password_reset_token',10)->nullable();
            $table->dateTime('last_login',0)->nullable();
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
        //
    }
}
