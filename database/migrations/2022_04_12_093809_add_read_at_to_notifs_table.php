<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReadAtToNotifsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifs', function (Blueprint $table) {
            $table->string('read_hosts_at')->default('2020-01-01 00:00:00');
            $table->string('read_services_at')->default('2020-01-01 00:00:00');
            $table->string('read_boxes_at')->default('2020-01-01 00:00:00');
            $table->string('read_equips_at')->default('2020-01-01 00:00:00');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifs', function (Blueprint $table) {
            $table->dropColumn('read_hosts_at');
            $table->dropColumn('read_services_at');
            $table->dropColumn('read_boxes_at');
            $table->dropColumn('read_equips_at');
        });
    }
}
