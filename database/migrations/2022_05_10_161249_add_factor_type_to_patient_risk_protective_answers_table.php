<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFactorTypeToPatientRiskProtectiveAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_risk_protective_answers', function (Blueprint $table) {
            $table->string('factor_type')->nullable()->after('patient_mrn_id')->index('factor_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_risk_protective_answers', function (Blueprint $table) {
            $table->dropColumn('factor_type');
        });
    }
}
