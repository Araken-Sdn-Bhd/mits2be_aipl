<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVonOrgBcakgroundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('von_org_background', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('added_by');
            $table->string('org_name');
            $table->string('org_reg_number');
            $table->tinyText('org_desc');
            $table->string('org_email');
            $table->string('org_phone');
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
        Schema::dropIfExists('von_org_background');
    }
}
