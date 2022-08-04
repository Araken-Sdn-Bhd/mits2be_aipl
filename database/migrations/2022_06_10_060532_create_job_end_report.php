<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobEndReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_end_report', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->bigInteger('patient_id');
            $table->string('name');
            $table->string('job_title');
            $table->string('employer_name');
            $table->date('job_start_date');
            $table->date('job_end_date');
            $table->string('changes_in_job_duties');
            $table->string('reason_for_job_end');
            $table->string('clients_perspective');
            $table->string('staff_comments_regarding_job');
            $table->string('employer_comments');
            $table->string('type_of_support');
            $table->string('person_wish_for_another_job');
            $table->string('clients_preferences');
            $table->string('staff_name');
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
        Schema::dropIfExists('job_end_report');
    }
}
