<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpsPoliceReferralForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cps_police_referral_form', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->bigInteger('patient_id');
            $table->string('to');  
            $table->string('officer_in_charge'); 
            $table->string('the_above_patient_ongoing'); 
            $table->string('name');
            $table->string('designation');
            $table->enum('status', [0, 1])->default('1');
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
        Schema::dropIfExists('cps_police_referral_form');
    }
}
