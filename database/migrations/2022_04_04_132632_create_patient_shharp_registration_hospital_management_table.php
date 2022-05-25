<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientShharpRegistrationHospitalManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_shharp_registration_hospital_management', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->integer('patient_mrn_no');
	        $table->integer('referral_or_contact');
            $table->string('referral_or_contact_other')->nullable();
	        $table->integer('arrival_mode');
            $table->string('arrival_mode_other')->nullable();
            $table->date('date');
	        $table->time('time');
	        $table->string('physical_consequences');
	        $table->string('physical_consequences_des',1024)->nullable();
	        $table->string('patient_admitted');
	        $table->string('patient_admitted_des',1024)->nullable();
	        $table->string('discharge_status');
            $table->date('discharge_date');
	        $table->string('discharge_number_days_in_ward');
            $table->integer('main_psychiatric_diagnosis');
            $table->integer('external_cause_inquiry');
	        $table->string('discharge_psy_mx');
            $table->string('discharge_psy_mx_des',1024)->nullable();
            $table->enum('status', [0, 1, 2])->default(1);
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
        Schema::dropIfExists('patient_shharp_registration_hospital_management');
    }
}
