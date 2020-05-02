<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RelRecordingtbllaporanwithlogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('recording_tbllaporan', function (Blueprint $table) {
            $table->integer('id_laporan')->unsigned()->index()->nullable(false)->change();
            $table->foreignid('id_agent')->references('id')->on('users');
        });
        Schema::table('recording_loglaporan', function (Blueprint $table) {
            $table->foreignid('id_laporan')->references('id_laporan')->on('recording_tbllaporan');
            $table->foreignid('id_agent')->references('id')->on('users');
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
        Schema::table('recording_loglaporan', function (Blueprint $table) {
            $table->dropForeign('id_laporan','id_agent');
        });
    }
}
