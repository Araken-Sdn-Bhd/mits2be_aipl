<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAppointmentStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_appointment_details', function (Blueprint $table) {
            $table->tinyInteger('appointment_status')->default(0)->after('status')->index('appointment_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_appointment_details', function (Blueprint $table) {
            $table->dropColumn('appointment_status');
        });
    }
}
