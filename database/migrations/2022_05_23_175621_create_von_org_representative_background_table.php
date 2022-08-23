<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVonOrgRepresentativeBackgroundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('von_org_representative_background', function (Blueprint $table) {
            $table->id();
            $table->string('added_by');
            $table->bigInteger('org_background_id');
            $table->string('section')->nullable();
            $table->string('name');
            $table->string('dob')->nullable();
            $table->string('position_in_org')->nullable();
            $table->string('email');
            $table->string('phone_number');
            $table->string('address', 2048);
            $table->integer('postcode_id');
            $table->integer('city_id');
            $table->integer('state_id');
            $table->integer('education_id');
            $table->integer('occupation_sector_id');
            $table->integer('branch_id');
            $table->string('area_of_involvement');
            $table->enum('is_agree', [0, 1])->default('0');
            $table->enum('status', [0, 1, 2, 3])->default('0');
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
        Schema::dropIfExists('von_org_representative_background');
    }
}
