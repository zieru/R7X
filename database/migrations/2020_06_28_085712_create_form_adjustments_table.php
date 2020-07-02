<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_adjustments', function (Blueprint $table) {
          $table->engine = 'InnoDB';
            $table->id();
          $table->unsignedInteger('author');
          $table->bigInteger('import_batch')->unsigned();
          $table->foreign('import_batch')->references('id')->on('importers')->onDelete('cascade');
            $table->text('shop')->nullable(false);
            $table->text('account')->nullable(false);
            $table->integer('bill_cycle')->nullable(false);
          $table->string('status_msisdn')->nullable(false);
            $table->string('msisdn');
            $table->string('los');
            $table->decimal('arpu');
            $table->date('bulantagihan');
            $table->decimal('nominal');
            $table->text('reason');
            $table->text('notes_dsc');
            $table->string('nodin_ba');
            $table->date('tgl_adj');
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
        Schema::dropIfExists('form_adjustments');
    }
}
