<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedInteger('job_id');
            $table->string('name')->default('');
            $table->string('status')->default('');
            $table->dateTime('start_time')->nullable(true);
            $table->dateTime('end_time')->nullable(true);
            $table->string('queue_name')->default('');
            $table->text('data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // non-destructive
    }
}
