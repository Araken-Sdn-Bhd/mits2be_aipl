<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkAnalysisJobSpecificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_analysis_job_specification', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id');
            $table->integer('work_analysis_form_id');
            $table->string('question_name');
            $table->string('answer');
            $table->string('comment',2500)->nullable();
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
        Schema::dropIfExists('work_analysis_job_specification');
    }
}
