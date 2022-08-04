<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitizenshipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('citizenship', function (Blueprint $table) {
            $table->increments('id');
            $table->string('citizenship_name')->unique();
            $table->string('citizenship_code')->nullable();
            $table->integer('citizenship_order');
            $table->enum('citizenship_status', [0, 1])->default(1);
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
        Schema::dropIfExists('citizenship');
    }
}
