<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_management', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('added_by');
            $table->bigInteger('hod_psychiatrist_id');
            $table->string('hod_psychiatrist_name');
            $table->string('hospital_code')->unique();
            $table->string('hospital_prefix');
            $table->string('hospital_name', 1024);
            $table->string('hospital_adrress_1', 1024);
            $table->string('hospital_adrress_2', 1024)->nullable();
            $table->string('hospital_adrress_3', 1024)->nullable();
            $table->integer('hospital_state');
            $table->string('hospital_city');
            $table->integer('hospital_postcode');
            $table->string('hospital_contact_number');
            $table->string('hospital_email');
            $table->string('hospital_fax_no');
            $table->tinyInteger('hospital_status');
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
        Schema::dropIfExists('hospital_management');
    }
}
