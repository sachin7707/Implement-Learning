<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_consents', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->text('consent_text')->nullable(true);
            $table->string('company');
            $table->string('name');
            $table->string('email');
            // NOTE: that we are NOT using a foreign key
            $table->unsignedInteger('order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_consents');
    }
}
