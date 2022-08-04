<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobClubConsentForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_club_consent_form', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->bigInteger('patient_id');
            $table->enum('consent_for_participation', [0, 1])->default('0');
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
        Schema::dropIfExists('job_club_consent_form');
    }
}
