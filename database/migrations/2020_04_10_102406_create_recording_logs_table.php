<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordingLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recording_loglaporan', function (Blueprint $table) {
            $table->bigIncrements('id_log_laporan');
            $table->dateTime('waktu');
            $table->integer('id_agent');
            $table->string('ket','32')->nullable(false);
            $table->bigInteger('id_laporan');
            $table->text('rec_url');
            $table->longText('isi');
            $table->timestamps();
            $table->foreign('id_agent')
                ->references('id')->on('users')
                ->onDelete('set null');
            $table->foreign('id_laporan')
                ->references('id_laporan')->on('recording_tbllaporan')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recording_loglaporan');
    }
}
