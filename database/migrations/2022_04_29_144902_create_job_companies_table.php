<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_companies', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('added_by');
            $table->string('company_name');
            $table->string('company_registration_number');
            $table->string('company_address_1', 1024);
            $table->string('company_address_2', 1024)->nullable();
            $table->string('company_address_3', 1024)->nullable();
            $table->integer('state_id');
            $table->integer('city_id');
            $table->string('postcode');
            $table->string('employment_sector', 1024);
            $table->tinyInteger('is_existing_training_program')->default(0);
            $table->string('corporate_body_sector', 1024);
            $table->string('contact_name')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_position')->nullable();
            $table->enum('status', [0, 1, 2])->default(1);
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
        Schema::dropIfExists('job_companies');
    }
}
