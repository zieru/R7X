<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recording_tbllaporan', function (Blueprint $table) {
            $table->bigIncrements('id_laporan')->autoIncrement();
            $table->text('judul');
            $table->string('msisdn_menghubungi');
            $table->string('msisdn_bermasalah');
            $table->longText('description');
            $table->dateTime('tgl_kejadian');
            $table->dateTime('tgl_kejadian_end')->nullable();
            $table->unsignedinteger('id_agent')->nullable();
            $table->dateTime('waktu');
            $table->string('ket');
            $table->string('id_co')->nullable();
            $table->dateTime('lastresponse_date')->nullable();
            $table->string('lastresponse_user')->nullable();
            $table->unsignedinteger('lastresponse_user_id')->nullable();
            $table->integer('priority');
            $table->integer('has_notify')->nullable();
            $table->unsignedinteger('pic')->nullable();
            $table->timestamps();
            $table->foreign('pic')
                ->references('id')->on('users')
                ->onDelete('set null');
            $table->foreign('lastresponse_user_id')
                ->references('id')->on('users')
                ->onDelete('set null');
            $table->foreign('id_agent')
                ->references('id')->on('users')
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
        Schema::dropIfExists('recording_tbllaporan');
    }
}
