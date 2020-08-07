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
            $table->date('periode');
            $table->char('account',16);
            $table->char('customer_id',16);
            $table->char('msisdn',16);
            $table->tinyInteger('bill_cycle')->unsigned();
            $table->char('regional',16);
            $table->char('grapari',32);
            $table->char('hlr_city',32);
            $table->char('customer_address',100);
            $table->char('bbs',50);
            $table->char('bbs_name',70);
            $table->char('bbs_company_name',66);
            $table->char('bbs_first_address',70);
            $table->char('bbs_second_address',70);
            $table->char('bbs_zip_code',70);
            $table->char('bbs_city',70);
            $table->char('customer_city',20);
            $table->char('aging_cust_subtype',70);
            $table->char('bbs_pay_type',70);
            $table->char('bbs_RT',70);
            $table->char('aging_status_subscribe',100);
            $table->char('blocking_status',100);
            $table->char('note',75);
            $table->char('customer_phone',15);
            $table->char('cek_halo',15);
            $table->char('cek_cp',15);
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
