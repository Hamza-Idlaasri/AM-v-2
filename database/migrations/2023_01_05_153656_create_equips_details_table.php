<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equips_details', function (Blueprint $table) {
            $table->id();
            $table->string('site_name');
            $table->string('box_name');
            $table->string('box_type');
            $table->string('equip_name');
            $table->string('pin_name');
            $table->string('working_state');
            $table->string('hall_name');
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
        Schema::dropIfExists('equips_details');
    }
}
