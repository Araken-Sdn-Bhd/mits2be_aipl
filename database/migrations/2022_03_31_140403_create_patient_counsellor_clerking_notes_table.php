<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientCounsellorClerkingNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_counsellor_clerking_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->integer('diagnosis_id');
            $table->integer('patient_mrn_id');
            $table->string('clinical_summary',1024)->nullable();
            $table->string('background_history',1024)->nullable();
            $table->string('clinical_notes',1024)->nullable();
            $table->string('management',1024)->nullable();
            $table->integer('location_services_id');
            $table->integer('type_diagnosis_id');
	        $table->string('category_services');	// 0->Assistant/supervision or External 1->Clinical work 
            $table->integer('services_id')->nullable();
            $table->integer('code_id')->nullable();
            $table->integer('sub_code_id')->nullable();
            $table->integer('complexity_services_id')->nullable();
            $table->integer('outcome_id')->nullable();
            $table->string('medication_des',2500)->nullable();
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
        Schema::dropIfExists('patient_counsellor_clerking_notes');
    }
}
