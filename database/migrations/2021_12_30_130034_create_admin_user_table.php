<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_user', function (Blueprint $table) {
            $table->bigIncrements('admin_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone_number',50)->nullable();
            $table->string('profile_picture',255)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->tinyInteger('type')->default(0)->comment('0-Super admin,1-Sub admin ');
            $table->tinyInteger('status')->default(1)->comment('0-disable,1-enable');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
        // create a admin account for staging
        DB::table('admin_user')->insert(
            array(
                'first_name' => 'Admin',
                'last_name' => 'Panel',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('123456789'),
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
        Schema::dropIfExists('admin_user');
    }
}
