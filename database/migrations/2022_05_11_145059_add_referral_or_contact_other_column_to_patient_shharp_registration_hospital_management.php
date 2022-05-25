<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferralOrContactOtherColumnToPatientShharpRegistrationHospitalManagement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_shharp_registration_hospital_management', function (Blueprint $table) {
            // $table->string('referral_or_contact_other', 1024)->nullable()->after('referral_or_contact');
            // $table->string('arrival_mode_other', 1024)->nullable()->after('arrival_mode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_shharp_registration_hospital_management', function (Blueprint $table) {
            // $table->dropColumn('referral_or_contact_other');
            // $table->dropColumn('arrival_mode_other');
        });
    }
}
