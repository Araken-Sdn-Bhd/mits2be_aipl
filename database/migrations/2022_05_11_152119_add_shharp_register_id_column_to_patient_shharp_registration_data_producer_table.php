<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShharpRegisterIdColumnToPatientShharpRegistrationDataProducerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_shharp_registration_data_producer', function (Blueprint $table) {
            $table->integer('shharp_register_id')->default(0)->after('patient_mrn_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_shharp_registration_data_producer', function (Blueprint $table) {
            $table->dropColumn('shharp_register_id');
        });
    }
}
