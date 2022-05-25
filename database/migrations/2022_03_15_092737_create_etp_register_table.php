<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtpRegisterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etp_register', function (Blueprint $table) {
            $table->increments('id');
            $table->string('etp_code');
            $table->string('etp_name');
            $table->string('etp_description', 1024);
            $table->integer('etp_order');
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
        Schema::dropIfExists('etp_register');
    }
}
