<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientShharpRegistrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_shharp_registration', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->date('date');
	        $table->time('time');
	        $table->integer('place_occurence');
	        $table->string('nric_no')->nullable();
	        $table->string('passport_no')->nullable();
	        $table->date('expiry_date')->nullable();
	        $table->integer('country_id')->nullable();
	        $table->integer('sex');	// 0->Male 1->Female
	        $table->date('birth_date');
	        $table->integer('age')->nullable();
	        $table->string('mobile_no');
	        $table->string('house_no')->nullable();
	        $table->string('hospital_mrn_no');
	        $table->string('mintari_mrn_no');
	        $table->string('services_type')->nullable();
            $table->string('referral_type')->nullable();
	        $table->string('referral_letter',512)->nullable();
	        $table->string('address1');
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

            $table->string('kin_name_asin_nric');
	        $table->integer('kin_relationship_id');
	        $table->string('kin_mobile_no');
	        $table->string('kin_house_no')->nullable();
	        $table->string('kin_address1')->nullable();
            $table->string('kin_address2')->nullable();
	        $table->string('kin_address3')->nullable();
	        $table->integer('kin_state_id')->nullable();
	        $table->integer('kin_city_id')->nullable();
	        $table->string('kin_postcode')->nullable();

            $table->enum('drug_allergy', [0, 1]);
	        $table->string('drug_allergy_description', 1024)->nullable();
	        $table->enum('traditional_medication', [0, 1]);
	        $table->string('traditional_description', 1024)->nullable();
	        $table->enum('other_allergy', [0, 1]);
	        $table->string('other_description', 1024)->nullable();
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
        Schema::dropIfExists('patient_shharp_registration');
    }
}
