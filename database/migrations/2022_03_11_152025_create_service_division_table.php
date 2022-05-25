<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceDivisionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_division', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_id');
            $table->integer('hospital_id');
            $table->integer('branch_id');
            $table->integer('division_order');
            $table->string('added_by');
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
        Schema::dropIfExists('service_division');
    }
}
