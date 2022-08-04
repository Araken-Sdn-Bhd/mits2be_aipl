<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobInterestChecklist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_interest_checklist', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->integer('patient_id');
            $table->string('interest_to_work')->nullable(); 
            $table->string('agree_if_mentari_find_job_for_you')->nullable();

            $table->string('clerk_job_interester')->nullable();
            $table->string('clerk_job_notes')->nullable();
            $table->string('factory_worker_job_interested')->nullable();
            $table->string('factory_worker_notes')->nullable();
            $table->string('cleaner_job_interested')->nullable();
            $table->string('cleaner_job_notes')->nullable();
            $table->string('security_guard_job_interested')->nullable();
            $table->string('security_guard_notes')->nullable();
            $table->string('laundry_worker_job_interested')->nullable();
            $table->string('laundry_worker_notes')->nullable();
            $table->string('car_wash_worker_job')->nullable();
            $table->string('car_wash_worker_notes')->nullable();
            $table->string('kitchen_helper_job')->nullable();
            $table->string('kitchen_helper_notes')->nullable();
            $table->string('waiter_job_interested')->nullable();
            $table->string('waiter_job_notes')->nullable();
            $table->string('chef_job_interested')->nullable();
            $table->string('chef_job_notes')->nullable();
            $table->string('others_job_specify')->nullable();
            $table->string('others_job_notes')->nullable();
            $table->string('type_of_job')->nullable();
            $table->string('duration')->nullable();
            $table->string('termination_reason');
            $table->string('note')->nullable();
            $table->string('planning')->nullable();  
            $table->string('patient_consent_interested')->nullable(); 

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
        Schema::dropIfExists('job_interest_checklist');
    }
}
