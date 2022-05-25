<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCounsellingProgressNoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('counselling_progress_note', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->integer('patient_mrn_id');
            $table->date('therapy_date');
            $table->time('therapy_time');
            $table->integer('diagnosis_id');
            $table->string('frequency_session')->nullable();
            $table->string('frequency_session_other')->nullable();
            $table->string('model_therapy')->nullable();
            $table->string('model_therapy_other')->nullable();
            $table->string('mode_therapy')->nullable();
            $table->string('mode_therapy_other')->nullable();
            $table->string('comment_therapy_session')->nullable();
            $table->string('patent_condition')->nullable();
            $table->string('patent_condition_other')->nullable();
            $table->string('comment_patent_condition')->nullable();
            $table->string('session_details')->nullable();
            $table->string('session_issues')->nullable();
            $table->string('conduct_session')->nullable();
            $table->string('outcome_session')->nullable();
            $table->string('transference_session')->nullable();
            $table->string('duration_session')->nullable();
            $table->string('other_comment_session')->nullable();
            $table->string('name')->nullable();
            $table->string('designation')->nullable();
            $table->date('date_session');

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
        Schema::dropIfExists('counselling_progress_note');
    }
}
