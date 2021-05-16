<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_history', function (Blueprint $table) {
            $table->id();
            $table->integer("sender_id");
            $table->integer("receiver_id")->default(0);
            $table->integer("group_id")->default(0);
            $table->string("title");
            $table->text("content");
            $table->string("image")->nullable();
            $table->integer("type")->default(0)->commnet('0:call, 1:news, 2:match');
            $table->integer("answer")->default(0)->commnet('0:none, 1:require');
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
        Schema::dropIfExists('notification_history');
    }
}
