<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutReachProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('out_reach_projects', function (Blueprint $table) {
            $table->id();
            $table->string('added_by');
            $table->bigInteger('parent_section_id');
            $table->string('parent_section');
            $table->string('project_name');
            $table->tinyText('project_background');
            $table->tinyText('project_objectives');
            $table->string('target_audience');
            $table->string('no_of_paricipants');
            $table->string('time_frame');
            $table->string('estimated_budget');
            $table->tinyText('project_scopes');
            $table->string('project_loaction');
            $table->string('project_loaction_value');
            $table->tinyText('target_outcome');
            $table->tinyText('followup_projects');
            $table->string('mentari_services', 512);
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
        Schema::dropIfExists('out_reach_projects');
    }
}
