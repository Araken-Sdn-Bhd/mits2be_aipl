<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientAppointmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_appointment_details', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->string('patient_mrn_id');
            $table->string('nric_or_passportno');
            $table->date('booking_date');
            $table->time('booking_time');
            $table->string('duration');
            $table->integer('appointment_type');
            $table->integer('type_visit');
            $table->integer('patient_category');
            $table->integer('assign_team');
            $table->integer('staff_id')->nullable();
            $table->dateTime('end_appoitment_date')->nullable();
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
        Schema::dropIfExists('patient_appointment_details');
    }
}
