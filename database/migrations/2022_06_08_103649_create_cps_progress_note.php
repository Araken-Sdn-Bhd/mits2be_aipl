<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpsProgressNote extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cps_progress_note', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_mrn_id');
            $table->integer('added_by');
            $table->date('cps_date');
            $table->time('cps_time');
            $table->string('cps_seen_by');
            $table->date('cps_date_discussed');
            $table->time('cps_time_discussed');
            $table->string('cps_discussed_with');
            $table->date('visit_date');
            $table->time('visit_time');
            $table->string('informants_name');
            $table->string('informants_relationship');
            $table->string('informants_contact');
            $table->string('case_manager');
            $table->string('visited_by');
            $table->string('visit_outcome');
            $table->string('current_intervention');
            $table->string('compliance_treatment');
            $table->string('medication_supervised_by');
            $table->string('delusions');
            $table->string('hallucination');
            $table->string('behavior');
            $table->string('blunted_affect');
            $table->string('depression');
            $table->string('anxiety');
            $table->string('disorientation');
            $table->string('uncooperativeness');
            $table->string('poor_impulse_control');
            $table->string('others');
            $table->string('ipsychopathology_remarks');
            $table->string('risk_of_violence');
            $table->string('risk_of_suicide');
            $table->string('risk_of_other_deliberate');
            $table->string('risk_of_severe');
            $table->string('risk_of_harm');
            $table->string('changes_in_teratment');
            $table->string('akathisia');
            $table->string('acute_dystonia');
            $table->string('parkinsonism');
            $table->string('tardive_dyskinesia');
            $table->string('tardive_dystonia'); 
            $table->string('others_specify');
            $table->string('side_effects_remarks');
            $table->string('social_performance');
            $table->string('psychoeducation');
            $table->string('coping_skills');
            $table->string('adl_training');
            $table->string('supported_employment');
            $table->string('family_intervention');
            $table->string('intervention_others');
            $table->string('remarks');
            $table->string('employment_past_months');
            $table->string('if_employment_yes')->nullable();
            $table->date('psychiatric_clinic');
            $table->date('im_depot_clinic');
            $table->date('next_community_visit');
            $table->string('comments');
            $table->integer('location_service');
            $table->integer('diagnosis_type');
            $table->string('service_category');
            $table->integer('services_id')->nullable();
            $table->integer('code_id')->nullable();
            $table->integer('sub_code_id')->nullable();
            $table->integer('complexity_services');
            $table->integer('outcome');
            $table->string('medication')->nullable();
            $table->string('staff_name');
            $table->string('designation');
            $table->enum('status', [0, 1,2])->default(1);
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
        Schema::dropIfExists('cps_progress_note');
    }
}
