<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRecordinglogsTable extends Migration
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
            $table->text('rec_url')->nullable(true)->change();
            $table->text('isi')->nullable(true)->change();
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
            $table->text('rec_url')->change();
            $table->text('isi')->change();
        });
    }
}
