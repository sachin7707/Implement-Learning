<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            // customer information
            $table->string('name');
            $table->string('cvr');
            $table->string('attention');
            $table->string('address');
            $table->string('postal');
            $table->string('city');
            $table->string('country');
            $table->string('phone');
            $table->string('email');
            $table->string('ean');
            $table->string('purchase_no');

            // billing information
            $table->string('billing_name');
            $table->string('billing_cvr');
            $table->string('billing_attention');
            $table->string('billing_address');
            $table->string('billing_postal');
            $table->string('billing_city');
            $table->string('billing_country');
            $table->string('billing_phone');
            $table->string('billing_email');

            // hooking up the order
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
