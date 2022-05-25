<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientShharpRegistrationSelfHarmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_shharp_registration_self_harm', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->date('date');
	        $table->time('time');
            $table->integer('patient_mrn_no');
	        $table->integer('place_occurence');
	        $table->string('method_of_self_harm');
	        $table->integer('overdose_poisoning')->nullable();
	        $table->string('other',1024)->nullable();
	        $table->string('patient_get_idea_about_method');
	        $table->string('specify_patient_actual_word')->nullable();
	        $table->string('suicidal_intent');
	        $table->string('suicidal_intent_yes')->nullable();
	        $table->string('suicidal_intent_other',1024)->nullable();
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
        Schema::dropIfExists('patient_shharp_registration_self_harm');
    }
}
