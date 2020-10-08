<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyncBilcoDataserahCekBayarLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_bilco_dataserah_cek_bayar_logs', function (Blueprint $table) {
            $table->date('periode');
            $table->char('detil_pembayaran',64);
            $table->char('account',16);
            $table->char('customer_id',16);
            $table->char('msisdn',16);
            $table->char('hlr_region',16);
            $table->decimal('nominal_bayar',16);
            $table->char('kpi',16);
            $table->bigInteger('import_batch');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sync_bilco_dataserah_cek_bayar_logs');
    }
}
