<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVolunteerismApplicationFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('volunteerism_application_form', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->integer('volunteer_individual_id');
            $table->string('volunteering_experience_yes')->nullable();
            $table->string('volunteering_experience_yes_des')->nullable();
            $table->string('volunteering_experience_no')->nullable();
            $table->string('health_professional_yes')->nullable();
            $table->string('health_professional_doc')->nullable();
            $table->string('health_professional_no')->nullable();
            $table->string('relevant_mentari_service')->nullable();
            $table->string('relevant_mentari_service_other')->nullable();
            $table->string('day')->nullable();
            $table->string('time')->nullable();
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
        Schema::dropIfExists('volunteerism_application_form');
    }
}
