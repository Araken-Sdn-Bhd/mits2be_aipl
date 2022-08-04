<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRehabDischargeNote extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rehab_discharge_note', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_mrn_id');
            $table->integer('added_by');
            $table->string('name');
            $table->string('mrn');
            $table->date('date');
            $table->time('time');
            $table->string('staff_name');
            $table->string('diagnosis_id');
            $table->string('intervention');
            $table->string('discharge_category');
            $table->string('comment');
            $table->integer('location_services');
            $table->integer('diagnosis_type');
            $table->string('service_category');
            $table->integer('services_id')->nullable();
            $table->integer('code_id')->nullable();
            $table->integer('sub_code_id')->nullable();
            $table->integer('complexity_services');
            $table->integer('outcome');
            $table->string('medication')->nullable();
            $table->string('specialist_name');
            $table->string('case_manager');
            $table->date('verification_date_1');
            $table->date('verification_date_2');
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
        Schema::dropIfExists('rehab_discharge_note');
    }
}
