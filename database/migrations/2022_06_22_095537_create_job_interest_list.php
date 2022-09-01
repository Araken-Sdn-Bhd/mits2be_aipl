<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobInterestList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_interest_list', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_interest_checklist_id')->nullable();
            $table->integer('patient_id')->nullable();
            $table->string('type_of_job')->nullable();
            $table->string('duration')->nullable();
            $table->string('termination_reason')->nullable();
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
        Schema::dropIfExists('job_interest_list');
    }
}
