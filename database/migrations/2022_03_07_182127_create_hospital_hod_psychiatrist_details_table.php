<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalHodPsychiatristDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_hod_psychiatrist_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('added_by');
            $table->integer('salutation');
            $table->string('name');
            $table->integer('gender');
            $table->integer('citizenship');
            $table->string('passport_nric_no');
            $table->integer('religion');
            $table->integer('designation');
            $table->string('email')->unique();
            $table->string('contact_mobile');
            $table->string('contact_office');
            $table->tinyInteger('status');
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
        Schema::dropIfExists('hospital_hod_psychiatrist_details');
    }
}
