<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveOrderCourseDataToManytomany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // fetching the order id and the course_id from the orders table
        $results = DB::table('orders')->select('id', 'course_id')->get();

        // moving the data from the order table into the course_order table
        foreach ($results as $result) {
            // Insert the split values into new columns.
            DB::table('course_order')->insert([
                'order_id' => $result->id,
                'course_id' => $result->course_id
            ]);
        }

        // dropping the course_id column from the orders table
        Schema::table('orders', function ($table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
