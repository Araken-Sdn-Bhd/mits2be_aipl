<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcctReferralFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('occt_referral_form', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->integer('patient_mrn_id');
            $table->string('referral_location');
            $table->date('date');
            $table->integer('diagnosis_id');
            $table->string('referral_clinical_assessment',2048);
            $table->string('referral_clinical_assessment_other')->nullable();
            $table->string('referral_clinical_intervention')->nullable();
            $table->string('referral_clinical_intervention_other')->nullable();
            $table->string('referral_clinical_promotive_program')->nullable();
            $table->string('referral_name')->nullable();
            $table->string('referral_designation')->nullable();
           
            $table->string('location_services');
            $table->integer('type_diagnosis_id');
	        $table->string('category_services');	// 0->Assistant/supervision or External 1->Clinical work 
            $table->integer('services_id')->nullable();
            $table->integer('code_id')->nullable();
            $table->integer('sub_code_id')->nullable();
            $table->string('complexity_services')->nullable();
            $table->integer('outcome')->nullable();
            $table->string('medication_des',2500)->nullable();
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
        Schema::dropIfExists('occt_referral_form');
    }
}
