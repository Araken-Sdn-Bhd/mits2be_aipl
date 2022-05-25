<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIcdCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('icd_category', function (Blueprint $table) {
           $table->increments('id');
           $table->bigInteger('added_by');
           $table->integer('icd_type_id');
           $table->string('icd_category_code')->unique();
           $table->string('icd_category_name');
           $table->string('icd_category_description', 1024);
           $table->integer('icd_category_order');
           $table->enum('icd_category_status', [0, 1])->default(1);
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
        Schema::dropIfExists('icd_category');
    }
}
