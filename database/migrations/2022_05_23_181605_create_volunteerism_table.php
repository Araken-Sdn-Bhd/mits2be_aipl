<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVolunteerismTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('volunteerism', function (Blueprint $table) {
            $table->id();
            $table->string('added_by');
            $table->bigInteger('parent_section_id');
            $table->string('parent_section');
            $table->enum('is_voluneering_exp', [0, 1])->default('0');
            $table->string('exp_details', 3500);
            $table->enum('is_mental_health_professional', [0, 1])->default('0');
            $table->string('resume', 255);
            $table->string('mentari_services', 512);
            $table->string('available_date');
            $table->string('available_time');
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
        Schema::dropIfExists('volunteerism');
    }
}
