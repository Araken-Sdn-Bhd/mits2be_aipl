<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobStartForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_start_form', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->bigInteger('patient_id');
            $table->string('client');
            $table->string('employment_specialist');
            $table->string('case_manager');
            $table->date('first_date_of_work');
            $table->string('job_title');
            $table->string('duties_field');
            $table->string('rate_of_pay');
            $table->string('benefits_field');
            $table->string('work_schedule');
            $table->string('disclosure');
            $table->string('name_of_employer');
            $table->string('name_of_superviser');
            $table->string('address');
            $table->integer('location_of_service');
            $table->integer('type_of_diagnosis');
            $table->string('category_of_services');
            $table->string('services');
            $table->integer('complexity_of_services');
            $table->integer('outcome');
            $table->integer('icd_9_code')->nullable();
            $table->integer('icd_9_subcode')->nullable();
            $table->string('medication_prescription', 1024);
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
        Schema::dropIfExists('job_start_form');
    }
}
