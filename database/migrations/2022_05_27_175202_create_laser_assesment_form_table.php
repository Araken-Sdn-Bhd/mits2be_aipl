<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaserAssesmentFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laser_assesmen_form', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('added_by');
            $table->bigInteger('patient_id');
            $table->string('pre_contemplation', 512);
            $table->string('contemplation', 512);
            $table->string('action', 512);
            $table->integer('location_of_service');
            $table->integer('type_of_diagnosis');
            $table->string('category_of_services');
            $table->integer('services');
            $table->integer('complexity_of_services');
            $table->integer('outcome');
            $table->integer('icd_9_code');
            $table->integer('icd_9_subcode');
            $table->string('medication_prescription', 1024)->nullable();
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
        Schema::dropIfExists('laser_assesmen_form');
    }
}
