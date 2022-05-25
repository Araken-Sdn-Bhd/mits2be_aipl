<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPatientMrnToPatientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_registration', function (Blueprint $table) {
            $table->string('patient_mrn')->nullable()->after('id')->index('patient_mrn');
            $table->string('hospital_mrn_no')->nullable(true)->change();
            $table->string('mintari_mrn_no')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_registration', function (Blueprint $table) {
            $table->dropColumn('patient_mrn');
        });
    }
}
