<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttemptTestResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attempt_test_result', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('added_by')->index('added_by');
            $table->bigInteger('patient_id')->index('patient_id');
            $table->string('test_name')->index('testname');
            $table->string('test_section_name')->nullable();
            $table->integer('result');
            $table->string('ip_address')->index('ip');
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
        Schema::dropIfExists('attempt_test_result');
    }
}
