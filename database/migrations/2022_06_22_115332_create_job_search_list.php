<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobSearchList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_search_list', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('list_of_job_search_id');
            $table->integer('patient_id');
            $table->string('company_name');
            $table->string('job_applied');
            $table->date('application_date');
            $table->date('interview_date');
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
        Schema::dropIfExists('job_search_list');
    }
}
