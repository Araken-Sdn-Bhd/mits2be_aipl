<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSharpRegistraionFinalStepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sharp_registraion_final_step', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('added_by');
            $table->bigInteger('patient_id')->index('patient_id');
            $table->string('risk')->index('risk');
            $table->string('protective')->index('protective');
            $table->string('self_harm')->index('self_harm');
            $table->string('suicide_risk')->index('suicide_risk');
            $table->string('hospital_mgmt')->index('hospital_mgmt');
            $table->enum('status', [0, 1, 2])->default(0);
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
        Schema::dropIfExists('sharp_registraion_final_step');
    }
}
