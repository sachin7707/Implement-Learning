<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ws_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->text('access_token');
            $table->string('token_type');
            $table->integer('expires_in');
            $table->string('username');
            $table->dateTime('issued');
            $table->dateTime('expires');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ws_tokens');
    }
}
