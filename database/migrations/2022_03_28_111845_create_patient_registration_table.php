<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientRegistrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_registration', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->integer('salutation_id')->nullable();
            $table->string('name_asin_nric');
	        $table->integer('citizenship');	// 1->Malaysian 2->Permanent Resident 3->Foreign
	        $table->string('nric_type')->nullable();
	        $table->string('nric_no')->nullable();
	        $table->string('passport_no')->nullable();
	        $table->date('expiry_date')->nullable();
	        $table->integer('country_id')->nullable();
	        $table->integer('sex');	// 0->Male 1->Female
	        $table->date('birth_date')->nullable();
	        $table->integer('age')->nullable();
	        $table->string('mobile_no');
	        $table->string('house_no')->nullable();
	        $table->string('hospital_mrn_no')->nullable();
	        $table->string('mintari_mrn_no')->nullable();
	        $table->string('services_type')->nullable();
            $table->string('referral_type')->nullable();
	        $table->string('referral_letter',512)->nullable();
	        $table->string('address1')->nullable();
            $table->string('address2')->nullable();
	        $table->string('address3')->nullable();
            $table->string('state_id')->nullable();
	        $table->string('city_id')->nullable();
	        $table->string('postcode')->nullable();

	        $table->integer('race_id')->nullable();
	        $table->integer('religion_id')->nullable();
	        $table->integer('marital_id')->nullable();
	        $table->integer('accomodation_id')->nullable();
	        $table->integer('education_level')->nullable();
	        $table->integer('occupation_status')->nullable();
	        $table->integer('fee_exemption_status')->nullable();
	        $table->integer('occupation_sector')->nullable();

            $table->string('kin_name_asin_nric')->nullable();
	        $table->integer('kin_relationship_id')->nullable();
			$table->string('kin_nric_no')->nullable();
	        $table->string('kin_mobile_no')->nullable();
	        $table->string('kin_house_no')->nullable();
	        $table->string('kin_address1')->nullable();
            $table->string('kin_address2')->nullable();
	        $table->string('kin_address3')->nullable();
	        $table->integer('kin_state_id')->nullable();
	        $table->integer('kin_city_id')->nullable();
	        $table->string('kin_postcode')->nullable();

            $table->enum('drug_allergy', [0, 1])->nullable();
	        $table->string('drug_allergy_description', 1024)->nullable();
	        $table->enum('traditional_medication', [0, 1])->nullable();
	        $table->string('traditional_description', 1024)->nullable();
	        $table->enum('other_allergy', [0, 1])->nullable();
	        $table->string('other_description', 1024)->nullable();
			$table->string('employment_status')->nullable();
			$table->string('household_income')->nullable();
			$table->string('ethnic_group')->nullable();
			$table->string('patient_need_triage_screening')->nullable();
            $table->enum('status', [0, 1, 2])->default(1);
			$table->string('sharp')->nullable();
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
        Schema::dropIfExists('patient_registration');
    }
}
