<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftdeleteandcreatedatLoglaporanToRecordingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('recording_loglaporan', function (Blueprint $table) {
            $table->softDeletesTz();
            $table->dropColumn('waktu');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recording_loglaporan', function (Blueprint $table) {
            $table->dropSoftDeletestz();
            $table->dateTime('waktu');
        });
        //a
    }
}
