<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetworkingContributionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('networking_contribution', function (Blueprint $table) {
            $table->id();
            $table->string('added_by');
            $table->bigInteger('parent_section_id');
            $table->string('parent_section');
            $table->string('contribution');
            $table->tinyText('budget');
            $table->string('project_loaction');
            $table->string('project_loaction_value');
            $table->string('no_of_paricipants');
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
        Schema::dropIfExists('networking_contribution');
    }
}
