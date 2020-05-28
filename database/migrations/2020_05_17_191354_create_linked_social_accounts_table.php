<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinkedSocialAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('linked_social_accounts')) {
            Schema::create('linked_social_accounts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('provider_id');
                $table->string('provider_name');
                $table->unsignedInteger('user_id');
                $table->timestamps();

                //$table->foreign('user_id')->references('id')->on('users');
            });
        };
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('linked_social_accounts');
    }
}
