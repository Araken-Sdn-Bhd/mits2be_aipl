<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScreenAccessRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('screen_access_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->integer('module_id');
            $table->integer('sub_module_id')->nullable();
            $table->integer('screen_id')->nullable();
            $table->integer('hospital_id');
            $table->integer('branch_id')->nullable();
            $table->integer('team_id')->nullable();
            $table->integer('staff_id');
            $table->boolean('access_screen');
            $table->boolean('read_writes');
            $table->boolean('read_only');
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
        Schema::dropIfExists('screen_access_roles');
    }
}
