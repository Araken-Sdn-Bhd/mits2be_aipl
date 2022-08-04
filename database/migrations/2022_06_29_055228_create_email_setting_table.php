<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_setting', function (Blueprint $table) {
            $table->increments('id');
            $table->string('send_email_from');
            $table->string('outgoing_smtp_server');
            $table->string('login_user_id');
            $table->string('login_password');
            $table->string('verify_password');
            $table->string('smtp_port_number');
            $table->string('security');
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
        Schema::dropIfExists('email_setting');
    }
}
