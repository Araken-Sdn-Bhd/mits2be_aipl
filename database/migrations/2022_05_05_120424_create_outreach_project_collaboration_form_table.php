<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutreachProjectCollaborationFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outreach_project_collaboration_form', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->integer('volunteer_individual_id');
            $table->string('project_name');
            $table->string('project_background',2048);
            $table->string('project_objectives',2048);
            $table->string('target_audience');
            $table->integer('number_participants');
            $table->string('estimated_budget');
            $table->string('project_scopes',2048);
            $table->string('project_location_mentari')->nullable();
            $table->string('project_location_mentari_location')->nullable();
            $table->string('project_location_others')->nullable();
            $table->string('project_location_others_des')->nullable();
            $table->string('measure_target_outcome',2048);
            $table->string('planned_follow_projects',2048);
            $table->string('relevant_mentari_service')->nullable();
            $table->string('relevant_mentari_service_other')->nullable();
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
        Schema::dropIfExists('outreach_project_collaboration_form');
    }
}
