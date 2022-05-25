<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIcdTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('icd_type', function (Blueprint $table) {
           $table->increments('id');
           $table->bigInteger('added_by');
           $table->string('icd_type_code')->unique();
           $table->string('icd_type_name');
           $table->string('icd_type_description', 1024);
           $table->integer('icd_type_order');
           $table->enum('icd_type_status', [0, 1])->default(1);
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
        Schema::dropIfExists('icd_type');
    }
}
