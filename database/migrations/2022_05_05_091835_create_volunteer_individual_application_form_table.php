<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVolunteerIndividualApplicationFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('volunteer_individual_application_form', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->string('name');
            $table->date('date')->nullable();
            $table->string('email');
            $table->string('phone_number');
            $table->string('address')->nullable();
            $table->integer('postcode_id');
            $table->integer('city_id');
            $table->integer('state_id');
            $table->string('highest_education');
            $table->string('current_occupation');
            $table->integer('hospital_id');
            $table->string('areas_involvement');
            $table->enum('status', [0, 1])->default(0);
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
        Schema::dropIfExists('volunteer_individual_application_form');
    }
}
