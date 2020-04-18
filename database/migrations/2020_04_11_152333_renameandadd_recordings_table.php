<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameandaddRecordingsTable extends Migration
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
            $table->integer('bot')->nullable(true);
            $table->string('tipe_layanan',32);
            $table->dropColumn('description');

        });
        Schema::table('recording_tbllaporan', function (Blueprint $table) {
            $table->renameColumn('lastresponse_user_id', 'lastresponse_userid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recording_tbllaporan', function (Blueprint $table) {
            $table->renameColumn('lastresponse_userid','lastresponse_user_id');
            $table->longText('description');
            $table->dropColumn('bot');
            $table->dropColumn('tipe_layanan');
        });
        //
    }
}
