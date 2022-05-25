<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePsychiatryClerkingNoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psychiatry_clerking_note', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->integer('patient_mrn_id');
            $table->string('chief_complain',1024)->nullable();
            $table->string('presenting_illness',1024)->nullable();
            $table->string('background_history',1024)->nullable();
            $table->string('general_examination',1024)->nullable();
            $table->string('mental_state_examination',1024)->nullable();
            $table->integer('diagnosis_id');
            $table->string('management',1024)->nullable();
            $table->string('discuss_psychiatrist_name',1024)->nullable();
            $table->date('date');
            $table->time('time');
            $table->integer('location_services_id');
            $table->integer('type_diagnosis_id');
	        $table->string('category_services');	// assisstance->Assistant/supervision,  external->External 1->clinical-work 
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
        Schema::dropIfExists('psychiatry_clerking_note');
    }
}
