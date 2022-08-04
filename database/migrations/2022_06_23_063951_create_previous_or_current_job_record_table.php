<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreviousOrCurrentJobRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('previous_or_current_job_record', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('list_previous_current_job_id');
            $table->integer('patient_id');
            $table->string('job');
            $table->string('salary');
            $table->string('duration');
            $table->string('reason_for_quit');
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
        Schema::dropIfExists('previous_or_current_job_record');
    }
}
