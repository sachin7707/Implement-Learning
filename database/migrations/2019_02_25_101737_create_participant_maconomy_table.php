<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateParticipantMaconomyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participant_maconomy', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('maconomy_id')->nullable(true);
            $table->unsignedInteger('course_id');
            $table->foreign('course_id')->references('id')->on('courses');
            $table->unsignedInteger('participant_id');
            $table->foreign('participant_id')->references('id')->on('participants')->onDelete('cascade');
        });

        // fetching the order id and the course_id from the orders table
        $rows = DB::select('
            select p.id as participant_id, co.course_id, p.maconomy_id from participants as p
            left join companies as c on c.id = p.company_id
            left join orders as o on o.id = c.order_id
            left join course_order as co on co.order_id = o.id
            where co.course_id is not null
            and p.maconomy_id <> ""
        ');

        // moving the data from the participant table into the participant_maconomy table
        foreach ($rows as $row) {
            // Insert the split values into new columns.
            DB::table('participant_maconomy')->insert([
                'participant_id' => $row->participant_id,
                'course_id' => $row->course_id,
                'maconomy_id' => $row->maconomy_id,
                'created_at' => DB::raw('NOW()'),
            ]);
        }
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
