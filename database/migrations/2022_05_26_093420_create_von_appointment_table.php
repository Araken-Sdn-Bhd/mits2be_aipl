<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVonAppointmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('von_appointment', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->string('parent_section_id');
            $table->string('name');
            $table->date('booking_date');
            $table->time('booking_time');
            $table->string('duration');
            $table->integer('interviewer_id');
            $table->integer('area_of_involvement');
            $table->string('services_type');
            $table->enum('status', [0, 1])->default(1);
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
        Schema::dropIfExists('von_appointment');
    }
}
