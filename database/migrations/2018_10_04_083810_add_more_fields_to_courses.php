<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreFieldsToCourses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('participants_min');
            $table->integer('participants_current');
            $table->string('name');
            $table->string('language');
            $table->string('venue_number');
            $table->string('venue_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->removeColumn('participants_min');
            $table->removeColumn('participants_current');
            $table->removeColumn('name');
            $table->removeColumn('language');
            $table->removeColumn('venue_number');
            $table->removeColumn('venue_name');
        });
    }
}
