<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBilcoDataSerahsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bilco_data_serahs', function (Blueprint $table) {
            $table->char('account',16);
            $table->char('msisdn',16);
            $table->tinyInteger('bill_cycle')->unsigned();
            $table->char('region',16);
            $table->decimal('bucket_4',16);
            $table->decimal('bucket_3',16);
            $table->decimal('bucket_2',16);
            $table->decimal('bucket_1',16);
            $table->decimal('total_outstanding',32);
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
        Schema::dropIfExists('bilco_data_serahs');
    }
}
