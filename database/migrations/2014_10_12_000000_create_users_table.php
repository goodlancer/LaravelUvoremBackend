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
            $table->id();
            $table->string('name');
            $table->string('firstname');
            $table->string('lastname');
            $table->integer('gender')->default(0);
            $table->string('indicate')->nullable();
            $table->string('country');
            $table->string('countryCode');
            $table->string('new_country')->default('France')->nullable();
            $table->string('new_countryCode')->default('FR')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phonenumber')->nullable();
            $table->string('avatar')->default("/uploads/avatar/default.png");
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('role')->default(2);
            $table->integer('active')->default(0);
            $table->timestamp('dt_premium')->nullable();
            $table->integer('change_country')->default(0);
            $table->string('country_image')->nullable();
            $table->string('device_token')->nullable();
            $table->string('iphone_device_token')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
