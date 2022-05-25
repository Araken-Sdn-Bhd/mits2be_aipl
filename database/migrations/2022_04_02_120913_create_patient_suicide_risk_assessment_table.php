<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientSuicideRiskAssessmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_suicide_risk_assessment', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->string('Type');
            $table->string('risk_level')->nullable();
            $table->string('risk')->nullable();
            $table->string('suicidal_intent')->nullable();
            $table->enum('status', [0, 1])->default(1);
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
        Schema::dropIfExists('patient_suicide_risk_assessment');
    }
}
