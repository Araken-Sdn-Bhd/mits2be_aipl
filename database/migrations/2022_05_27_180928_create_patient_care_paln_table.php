<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientCarePalnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_care_paln', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('added_by');
            $table->bigInteger('patient_id');
            $table->date('plan_date');
            $table->string('reason_of_review', 512);
            $table->string('diagnosis', 512);
            $table->string('medication_oral', 512);
            $table->string('medication_depot', 512);
            $table->string('medication_im', 512);
            $table->string('background_history', 512);
            $table->string('staff_incharge_dr', 512);
            $table->string('treatment_plan', 3000);
            $table->date('next_review_date');
            $table->date('case_manager_date');
            $table->string('case_manager_name');
            $table->string('case_manager_designation');
            $table->date('specialist_incharge_date');
            $table->string('specialist_incharge_name');
            $table->string('specialist_incharge_designation');
            $table->integer('location_of_service');
            $table->integer('type_of_diagnosis');
            $table->string('category_of_services');
            $table->integer('services');
            $table->integer('complexity_of_services');
            $table->integer('outcome');
            $table->integer('icd_9_code');
            $table->integer('icd_9_subcode');
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
        Schema::dropIfExists('patient_care_paln');
    }
}
