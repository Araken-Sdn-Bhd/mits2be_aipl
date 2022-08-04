<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScreensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('screens', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->integer('module_id');
            $table->string('module_name');
            $table->integer('sub_module_id');
            $table->string('sub_module_name');
            $table->string('screen_name');
            $table->string('screen_route');
            $table->string('icon');
            $table->integer('index_val');
            $table->string('screen_description', 1024);
            $table->enum('screen_status', [0, 1])->default(1);
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
        Schema::dropIfExists('screens');
    }
}
