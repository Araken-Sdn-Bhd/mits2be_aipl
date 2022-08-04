<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePsychologyReferralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psychology_referral', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->integer('patient_id');
            $table->string('patient_acknowledged');
            $table->integer('diagnosis_id');
            $table->string('reason_referral_assessment');
            $table->string('reason_referral_assessment_other')->nullable();
            $table->string('reason_referral_intervention');
            $table->string('reason_referral_intervention_other')->nullable();
            $table->string('case_formulation',2500);
            $table->string('referring_doctor')->nullable();
            $table->string('designation')->nullable();
            $table->date('date')->nullable();
           
            $table->string('location_services');
            $table->integer('type_diagnosis_id');
	        $table->string('category_services');	// assistant->Assistant/supervision or External 1->Clinical work 
            $table->integer('services_id')->nullable();
            $table->integer('code_id')->nullable();
            $table->integer('sub_code_id')->nullable();
            $table->integer('complexity_services')->nullable();
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
        Schema::dropIfExists('psychology_referral');
    }
}
