<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkAnalysisFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_analysis_forms', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->integer('patient_id');
            $table->string('company_name'); 
            $table->string('company_address1'); 
            $table->string('company_address2')->nullable(); 
            $table->string('company_address3')->nullable(); 
            $table->integer('state_id');

            $table->integer('city_id');
            $table->integer('postcode_id');
            $table->string('supervisor_name');
            $table->string('email');
            $table->string('position');
            $table->string('job_position');
            $table->string('client_name');
            $table->string('current_wage');
            $table->string('wage_specify')->nullable();
            $table->string('wage_change_occur')->nullable();
            $table->string('change_in_rate')->nullable();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->string('on_date')->nullable();
            $table->string('works_hour_week')->nullable();
            $table->string('work_schedule')->nullable();
            $table->string('no_of_current_employee')->nullable();
            $table->string('no_of_other_employee')->nullable();
            $table->string('during_same_shift')->nullable();

            $table->string('education_level');
            $table->string('grade');
            $table->string('job_experience_year');
            $table->string('job_experience_months');
            $table->string('others');


            $table->integer('location_services');
            $table->integer('type_diagnosis_id');
	        $table->string('category_services');	// 0->Assistant/supervision or External 1->Clinical work 
            $table->integer('services_id')->nullable();
            $table->integer('code_id')->nullable();
            $table->integer('sub_code_id')->nullable();
            $table->integer('complexity_services')->nullable();
            $table->integer('outcome')->nullable();
            $table->string('medication_des',2500)->nullable();
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
        Schema::dropIfExists('work_analysis_forms');
    }
}
