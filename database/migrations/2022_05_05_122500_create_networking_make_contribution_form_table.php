<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetworkingMakeContributionFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('networking_make_contribution_form', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->integer('volunteer_individual_id');
            $table->string('like_contribute')->nullable();
            $table->integer('estimated_budget');
            
            $table->string('project_location_mentari')->nullable();
            $table->string('project_location_mentari_location')->nullable();
            $table->string('project_location_others')->nullable();
            $table->string('project_location_others_des')->nullable();
            $table->string('number_participants');
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
        Schema::dropIfExists('networking_make_contribution_form');
    }
}
