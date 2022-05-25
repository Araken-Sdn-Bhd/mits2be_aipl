<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('added_by');
            $table->bigInteger('company_id');
            $table->string('position_offered');
            $table->string('position_location_1', 1024);
            $table->string('position_location_2', 1024)->nullable();
            $table->string('position_location_3', 1024)->nullable();
            $table->integer('education_id');
            $table->integer('duration_of_employment');
            $table->string('salary_offered');
            $table->string('work_schedule');
            $table->tinyInteger('is_transport')->default(0);
            $table->tinyInteger('is_accommodation')->default(0);
            $table->string('work_requirement', 1024);
            $table->integer('branch_id');
            $table->enum('job_availability', [1, 2])->default(1);
            $table->enum('status', [0, 1, 2])->default(1);  //1->pending  0->rejected 2->Approved
            $table->integer('status_changed_by')->default(0);
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
        Schema::dropIfExists('job_offers');
    }
}
