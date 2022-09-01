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
            $table->integer('patient_mrn_no')->nullable();
	        $table->integer('referral_or_contact')->nullable();
            $table->string('referral_or_contact_other')->nullable();
	        $table->integer('arrival_mode')->nullable();
            $table->string('arrival_mode_other')->nullable();
            $table->date('date')->nullable();
	        $table->time('time')->nullable();
	        $table->string('physical_consequences')->nullable();
	        $table->string('physical_consequences_des',1024)->nullable();
	        $table->string('patient_admitted')->nullable();
	        $table->string('patient_admitted_des',1024)->nullable();
	        $table->string('discharge_status')->nullable();
            $table->date('discharge_date')->nullable();
	        $table->string('discharge_number_days_in_ward')->nullable();
            $table->integer('main_psychiatric_diagnosis')->nullable();
            $table->integer('external_cause_inquiry')->nullable();
	        $table->string('discharge_psy_mx')->nullable();
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
