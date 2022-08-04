<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSharpRegistraionSelfHarmResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sharp_registraion_self_harm_result', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('added_by')->index('added_by');
            $table->bigInteger('patient_id')->index('patient_id');
            $table->string('section')->index('section')->nullable();
            $table->string('section_value', 2048)->nullable();
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
        Schema::dropIfExists('sharp_registraion_self_harm_result');
    }
}
