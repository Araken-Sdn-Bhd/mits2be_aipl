<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointment_request', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by')->default(0);
            $table->integer('patient_mrn_id')->nullable();
            $table->integer('branch_id');
            $table->string('name');
            $table->string('nric_or_passportno')->nullable();
            $table->string('contact_no');
            $table->string('address')->nullable();
            $table->string('address1')->nullable();
            $table->string('email');
            $table->string('ip_address')->nullable();
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
        Schema::dropIfExists('appointment_request');
    }
}
