<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientIndexForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_index_form', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_mrn_id');
            $table->integer('diagnosis')->nullable();
            $table->Date('date_onset')->nullable();
            $table->Date('date_of_diagnosis')->nullable();
            $table->Date('date_of_referral')->nullable(); 
            $table->Date('date_of_first_assessment')->nullable();
            $table->string('hospital')->nullable();
            $table->Date('latest_admission_date')->nullable();
            $table->Date('date_of_discharge')->nullable();
            $table->string('reason')->nullable();
            $table->string('adherence_to_medication')->nullable();
            $table->string('aggresion')->nullable();
            $table->string('suicidality')->nullable();
            $table->string('criminality')->nullable();
            $table->string('age_First_Started')->nullable();
            $table->string('heroin')->nullable();
            $table->string('cannabis')->nullable();
            $table->string('ats')->nullable();
            $table->string('inhalant')->nullable();
            $table->string('alcohol')->nullable();
            $table->string('tobacco')->nullable();
            $table->string('others')->nullable();
            $table->string('past_Medical')->nullable();
            $table->string('background_history')->nullable();
            $table->string('who_das_assessment')->nullable();
            $table->string('mental_state_examination')->nullable();
            $table->string('summary_of_issues')->nullable();
            $table->string('management_plan')->nullable();
            $table->integer('location_of_services')->nullable();
            $table->integer('type_of_diagnosis')->nullable();
            $table->string('category_of_services')->nullable();

            $table->integer('services_id')->nullable();
            $table->bigInteger('added_by')->nullable();
            $table->integer('code_id')->nullable()->nullable();
            $table->integer('sub_code_id')->nullable()->nullable();
            $table->integer('complexity_of_service')->nullable();
            $table->integer('outcome')->nullable();

            $table->string('medication')->nullable();
            $table->string('zone')->nullable();
            $table->string('case_manager')->nullable();
            $table->string('specialist')->nullable();
            $table->date('date')->nullable();
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
        Schema::dropIfExists('patient_index_form');
    }
}
