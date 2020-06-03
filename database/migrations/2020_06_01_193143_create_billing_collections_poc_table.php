<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingCollectionsPocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_collections_poc', function (Blueprint $table) {
            $table->id();
            $table->date('periode');
            $table->enum('area',['AREA I', 'AREA II', 'AREA III', 'AREA IV'])->nullable(false)->index();
            $table->char('regional','32')->nullable(false)->index();
            $table->char('poc','32')->nullable(false)->index();
            $table->integer('bill_cycle')->nullable(false)->index();
            $table->bigInteger('import_batch')->unsigned();
            $table->foreign('import_batch')->references('id')->on('importers')->onDelete('cascade');
            $table->enum('blocking_status',['BLOCKED 1','BLOCKED 2','CANCELLED','UNBLOCKED']);
            $table->enum('customer_type',['S','O']);
            $table->integer('bill_amount_1')->nullable(false)->default(0);
            $table->integer('bill_amount_2')->nullable(false)->default(0);
            $table->integer('bill_amount_3')->nullable(false)->default(0);
            $table->integer('bill_amount_4')->nullable(false)->default(0);
            $table->integer('bill_amount_5')->nullable(false)->default(0);
            $table->integer('bill_amount_6')->nullable(false)->default(0);
            $table->integer('bill_amount_7')->nullable(false)->default(0);
            $table->integer('bill_amount_8')->nullable(false)->default(0);
            $table->integer('bill_amount_9')->nullable(false)->default(0);
            $table->integer('bill_amount_10')->nullable(false)->default(0);
            $table->integer('bill_amount_11')->nullable(false)->default(0);
            $table->integer('bill_amount_12')->nullable(false)->default(0);
            $table->integer('bucket_1')->nullable(false)->default(0);
            $table->integer('bucket_2')->nullable(false)->default(0);
            $table->integer('bucket_3')->nullable(false)->default(0);
            $table->integer('bucket_4')->nullable(false)->default(0);
            $table->integer('bucket_5')->nullable(false)->default(0);
            $table->integer('bucket_6')->nullable(false)->default(0);
            $table->integer('bucket_7')->nullable(false)->default(0);
            $table->integer('bucket_8')->nullable(false)->default(0);
            $table->integer('bucket_9')->nullable(false)->default(0);
            $table->integer('bucket_10')->nullable(false)->default(0);
            $table->integer('bucket_11')->nullable(false)->default(0);
            $table->integer('bucket_12')->nullable(false)->default(0);
            $table->integer('bucket_13')->nullable(false)->default(0);
            $table->integer('total_bucket_per_msisdn')->nullable(false)->default(0);
            $table->integer('total_trans')->nullable(false)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billing_collections_poc');
    }
}
