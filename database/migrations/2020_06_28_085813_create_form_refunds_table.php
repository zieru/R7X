<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_refunds', function (Blueprint $table) {
          $table->engine = 'InnoDB';
          $table->id();
          $table->unsignedInteger('author');
          $table->bigInteger('import_batch')->unsigned();
          $table->foreign('import_batch')->references('id')->on('importers')->onDelete('cascade');
          $table->text('shop')->nullable(false);
          $table->string('msisdn');
          $table->date('tanggal_permintaan');
          $table->date('tanggal_eksekusi');
          $table->decimal('amount');
          $table->decimal('balance');
          $table->text('reason');
          $table->string('nodin_ba');
          $table->text('notes_dsc');
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_refunds');
    }
}
