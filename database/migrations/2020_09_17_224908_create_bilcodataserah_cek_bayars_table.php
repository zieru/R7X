<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBilcodataserahCekBayarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bilcodataserah_cek_bayars', function (Blueprint $table) {
            $table->date('periode');
            $table->char('account',16);
            $table->decimal('a120',16);
            $table->decimal('a90',16);
            $table->decimal('a60',16);
            $table->decimal('a30',16);
            $table->decimal('b120',16);
            $table->decimal('b90',16);
            $table->decimal('b60',16);
            $table->decimal('b30',16);
            $table->decimal('b0',16);
            $table->boolean('h30')->default(0);
            $table->boolean('h60')->default(0);
            $table->boolean('h90')->default(0);
            $table->boolean('h120')->default(0);
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
        Schema::dropIfExists('bilcodataserah_cek_bayars');
    }
}
