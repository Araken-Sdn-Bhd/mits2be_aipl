<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpsReferralFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cps_referral_form', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('added_by');
            $table->bigInteger('patient_id');
            $table->string('treatment_needs_individual', 512);
            $table->string('treatment_needs_medication', 512);
            $table->string('treatment_needs_support', 512);
            $table->integer('location_of_service');
            $table->integer('type_of_diagnosis');
            $table->string('category_of_services');
            $table->integer('services');
            $table->integer('complexity_of_services');
            $table->integer('outcome');
            $table->integer('icd_9_code');
            $table->integer('icd_9_subcode');
            $table->string('medication_des',1024)->nullable();
            $table->string('medication_referrer_name');
            $table->string('medication_referrer_designation');
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
        Schema::dropIfExists('cps_referral_form');
    }
}
