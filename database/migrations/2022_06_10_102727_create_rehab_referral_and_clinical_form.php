<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRehabReferralAndClinicalForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rehab_referral_and_clinical_form', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->integer('patient_mrn_id'); 
            $table->string('patient_referred_for');
            $table->string('diagnosis');
            $table->date('date_onset');
            $table->date('date_of_referral'); 
            $table->string('no_of_admission');
            $table->date('latest_admission_date');
            $table->string('current_medication')->nullable();
            $table->string('alerts');
            $table->string('education_level');
            $table->string('aggresion');
            $table->string('suicidality');
            $table->string('criminality');
            $table->string('age_first_started');

            $table->string('heroin');
            $table->string('cannabis');
            $table->string('ats');
            $table->string('inhalant');
            $table->string('alcohol');
            $table->string('tobacco');
            $table->string('others');
            $table->string('other_information');
           
            $table->integer('location_services');
            $table->integer('type_diagnosis_id');
	        $table->string('category_services');	// 0->Assistant/supervision or External 1->Clinical work 
            $table->integer('services_id')->nullable();
            $table->integer('code_id')->nullable();
            $table->integer('sub_code_id')->nullable();
            $table->integer('complexity_services')->nullable();
            $table->integer('outcome')->nullable();
            $table->string('medication_des',2500)->nullable();
            $table->string('referral_name');
            $table->string('designation');
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
        Schema::dropIfExists('rehab_referral_and_clinical_form');
    }
}
