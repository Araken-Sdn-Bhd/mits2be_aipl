<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpsHomevisitWithdrawalForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cps_homevisit_withdrawal_form', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->bigInteger('patient_id');
            $table->enum('community_psychiatry_services', [0, 1])->default('0');
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
        Schema::dropIfExists('cps_homevisit_withdrawal_form');
    }
}
