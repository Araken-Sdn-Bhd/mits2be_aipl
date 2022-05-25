<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientShharpRegistrationRiskProtectiveFactorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_shharp_registration_risk_protective_factors', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
	        $table->string('Question');
	        $table->string('Options1');
            $table->string('Options2');
            $table->string('Type');
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
        Schema::dropIfExists('patient_shharp_registration_risk_protective_factors');
    }
}
