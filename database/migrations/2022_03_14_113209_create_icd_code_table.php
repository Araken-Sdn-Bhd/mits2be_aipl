<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIcdCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('icd_code', function (Blueprint $table) {
           $table->increments('id');
           $table->bigInteger('added_by');
           $table->integer('icd_type_id');
           $table->integer('icd_category_id');
           $table->string('icd_code')->unique();
           $table->string('icd_name');
           $table->string('icd_description');
           $table->integer('icd_order');
           $table->enum('icd_status', [0, 1])->default(1);
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
        Schema::dropIfExists('icd_code');
    }
}
