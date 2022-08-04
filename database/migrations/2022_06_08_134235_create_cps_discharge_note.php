<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpsDischargeNote extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cps_discharge_note', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_mrn_id');
            $table->integer('added_by');
            $table->string('name');
            $table->string('mrn');
            $table->date('cps_discharge_date');
            $table->time('time');
            $table->string('staff_name');
            $table->string('diagnosis');
            $table->string('post_intervention');
            $table->string('psychopathology');
            $table->string('psychosocial');
            $table->string('potential_risk');
            $table->string('category_of_discharge');
            $table->string('discharge_diagnosis');
            $table->string('outcome_medication');
            $table->integer('location_service');
            $table->integer('diagnosis_type');
            $table->string('service_category');
            $table->integer('services_id')->nullable();
            $table->integer('code_id')->nullable();
            $table->integer('sub_code_id')->nullable();
            $table->integer('complexity_services');
            $table->integer('outcome');
            $table->string('medication')->nullable();
            $table->string('specialist_name');
            $table->date('verification_date');
            $table->string('case_manager');
            $table->date('date');
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
        Schema::dropIfExists('cps_discharge_note');
    }
}
