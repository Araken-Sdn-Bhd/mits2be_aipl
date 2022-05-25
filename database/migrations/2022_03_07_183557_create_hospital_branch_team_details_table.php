<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalBranchTeamDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_branch_team_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('added_by');
            $table->bigInteger('hospital_id');
            $table->string('hospital_code');
            $table->bigInteger('hospital_branch_id');
            $table->string('hospital_branch_name', 1024);
            $table->string('team_name');
            $table->tinyInteger('status');
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
        Schema::dropIfExists('hospital_branch_team_details');
    }
}
