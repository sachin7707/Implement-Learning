<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultValuesToCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('cvr')->default('')->change();
            $table->string('attention')->default('')->change();
            $table->string('address')->default('')->change();
            $table->string('postal')->default('')->change();
            $table->string('city')->default('')->change();
            $table->string('country')->default('')->change();
            $table->string('phone')->default('')->change();
            $table->string('email')->default('')->change();
            $table->string('ean')->default('')->change();
            $table->string('purchase_no')->default('')->change();
            $table->string('billing_name')->default('')->change();
            $table->string('billing_attention')->default('')->change();
            $table->string('billing_address')->default('')->change();
            $table->string('billing_postal')->default('')->change();
            $table->string('billing_city')->default('')->change();
            $table->string('billing_country')->default('')->change();
            $table->string('billing_phone')->default('')->change();
            $table->string('billing_email')->default('')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // nothing -- non destructive changes always :)
    }
}
