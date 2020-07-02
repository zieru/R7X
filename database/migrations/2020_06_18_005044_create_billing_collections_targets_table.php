<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingCollectionsTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_collections_targets', function (Blueprint $table) {
            $table->id();
            $table->decimal('target', 5, 4);
            $table->char('regional','32')->nullable(false)->index();
            $table->date('periode');
            $table->unique(['periode', 'regional']);
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
        Schema::dropIfExists('billing_collections_targets');
    }
}
