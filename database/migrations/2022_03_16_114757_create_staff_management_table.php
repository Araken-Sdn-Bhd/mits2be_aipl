<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_management', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->string('name');
            $table->string('nric_no')->unique();
            $table->string('registration_no')->unique();
            $table->integer('role_id');
            $table->string('email')->unique();
            $table->integer('team_id');
            $table->integer('branch_id');//mentari_location
            $table->string('contact_no');
            $table->integer('designation_id');
            $table->enum('is_incharge',[0,1])->default(0);
            $table->date('designation_period_start_date');
            $table->date('designation_period_end_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('document',512)->nullable();
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
        Schema::dropIfExists('staff_management');
    }
}
