<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalBranchDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_branch__details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('added_by');
            $table->bigInteger('hospital_id');
            $table->string('hospital_code');
            $table->string('hospital_branch_name', 1024);
            $table->enum('isHeadquator', [0, 1])->default(0); //0 => no,  1 => yes
            $table->string('branch_adrress_1', 1024);
            $table->string('branch_adrress_2', 1024);
            $table->string('branch_adrress_3', 1024);
            $table->integer('branch_state');
            $table->string('branch_city');
            $table->integer('branch_postcode');
            $table->string('branch_contact_number_office');
            $table->string('branch_contact_number_mobile');
            $table->string('branch_email');
            $table->string('branch_fax_no');
            $table->tinyInteger('branch_status');
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
        Schema::dropIfExists('hospital_branch__details');
    }
}
