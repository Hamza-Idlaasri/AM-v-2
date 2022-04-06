<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifsTabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id');
            $table->integer('hosts');
            $table->integer('services');
            $table->integer('boxes');
            $table->integer('equips');
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
        Schema::dropIfExists('notifs');
    }
}
