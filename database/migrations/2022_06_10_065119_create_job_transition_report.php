<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobTransitionReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_transition_report', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->bigInteger('patient_id');
            $table->string('future_plan');
            $table->string('short_term_goal');
            $table->string('long_term_goal');
            $table->string('who_have_you_called_past');
            $table->string('my_case_manager_yes_no');
            $table->string('my_case_manager_name')->nullable();
            $table->string('my_case_manager_contact')->nullable();
            $table->string('my_therapist_yes_no');
            $table->string('my_therapist_name')->nullable();
            $table->string('my_therapist_contact')->nullable();
            $table->string('my_family_yes_no');
            $table->string('my_family_name')->nullable();
            $table->string('my_family_contact')->nullable();
            $table->string('my_friend_yes_no');
            $table->string('my_friend_name')->nullable();
            $table->string('my_friend_contact')->nullable();
            $table->string('my_significant_other_yes_no');
            $table->string('my_significant_other_name')->nullable();
            $table->string('my_significant_other_contact')->nullable();
            $table->string('clergy_yes_no');
            $table->string('clergy_name')->nullable();
            $table->string('clergy_contact')->nullable();
            $table->string('benefit_planner_yes_no');
            $table->string('benefit_planner_name')->nullable();
            $table->string('benefit_planner_contact')->nullable();
            $table->string('other_yes_no');
            $table->string('other_name')->nullable();
            $table->string('other_contact')->nullable();
            $table->string('schedule_meeting_discuss_for_transition');
            $table->string('who_check_in_with_you');
            $table->string('who_contact_you');
            $table->string('how_would_like_to_contacted');
            $table->string('coping_strategies');
            $table->string('dissatisfied_with_your_job');
            $table->string('reasons_to_re_connect_to_ips');
            $table->string('patient_name');
            $table->string('doctor_name');
            $table->date('transition_report_date');
            $table->date('date');
            $table->integer('location_of_service');
            $table->integer('type_of_diagnosis');
            $table->string('category_of_services');
            $table->string('services');
            $table->integer('complexity_of_services');
            $table->integer('outcome');
            $table->integer('icd_9_code')->nullable();
            $table->integer('icd_9_subcode')->nullable();
            $table->string('medication_prescription', 1024)->nullable();
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
        Schema::dropIfExists('job_transition_report');
    }
}
