<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTriageFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('triage_form', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->integer('patient_mrn_id');
            $table->string('risk_history_assressive')->nullable();
            $table->string('risk_history_criminal')->nullable();
            $table->string('risk_history_detereotation')->nullable();
            $table->string('risk_history_neglect')->nullable();
            $table->string('risk_history_suicidal_idea')->nullable();
            $table->string('risk_history_suicidal_attempt')->nullable();
            $table->string('risk_history_homicidal_idea')->nullable();
            $table->string('risk_history_homicidal_attempt')->nullable();
            $table->string('risk_history_aggressive_idea')->nullable();
            $table->string('risk_history_aggressive_attempt')->nullable();
            $table->string('risk_social_has_no_family')->nullable();
            $table->string('risk_homeless')->nullable();
            $table->string('capacity_cannot_give_commitment')->nullable();
            $table->string('capacity_showed_no_interest')->nullable();
            $table->string('treatment_checked')->nullable();
            $table->string('treatment_given_appointment')->nullable();
            $table->string('treatment_given_regular')->nullable();
            $table->string('placement_referred')->nullable();
            $table->string('placement_discharge')->nullable();
            $table->integer('screening_id');

            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('appointment_duration');
            $table->integer('appointment_type');
            $table->integer('appointment_type_visit');
            $table->integer('appointment_patient_category');
            $table->integer('appointment_assign_team');
            
            $table->integer('location_services_id');
            $table->integer('type_diagnosis_id');
	        $table->string('category_services');	// 0->Assistant/supervision or External 1->Clinical work 
            $table->integer('services_id')->nullable();
            $table->integer('code_id')->nullable();
            $table->integer('sub_code_id')->nullable();
            $table->integer('complexity_services_id')->nullable();
            $table->integer('outcome_id')->nullable();
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
        Schema::dropIfExists('triage_form');
    }
}
