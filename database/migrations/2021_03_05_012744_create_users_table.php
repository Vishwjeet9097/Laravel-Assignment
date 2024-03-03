<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('contact')->default(NULL);
            $table->string('password');
            $table->unsignedBigInteger('profile_id')->nullable();
            $table->enum('user_type', array('SUPER_ADMIN', 'ADMIN', 'USER'));
            $table->enum('status', array('ACTIVE', 'INACTIVE'))->default('ACTIVE');
            $table->string('verification_otp')->nullable();
            $table->string('forgot_password_otp')->nullable();
            $table->enum('verification_status', array('VERIFIED', 'NOT VERIFIED'))->default('NOT VERIFIED');
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
        Schema::dropIfExists('users');
    }
}
