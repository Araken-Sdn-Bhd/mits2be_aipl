<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientCbiOnlinetestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_cbi_onlinetest', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->string('Type');
            $table->string('Question',1024);
            $table->string('Answer0')->nullable();
            $table->string('Answer1')->nullable();
            $table->string('Answer2')->nullable();
            $table->string('Answer3')->nullable();
            $table->string('Answer4')->nullable();
            $table->string('Answer5')->nullable();
            $table->enum('status', [0, 1])->default(1);
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
        Schema::dropIfExists('patient_cbi_onlinetest');
    }
}
