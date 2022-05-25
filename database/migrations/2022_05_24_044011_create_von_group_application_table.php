<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVonGroupApplicationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('von_group_application', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('added_by');
            $table->enum('is_represent_org', [0, 1])->default('0');
            $table->string('members_count');
            $table->tinyText('member_background');
            $table->enum('is_you_represenative', [0, 1])->default('0');
            $table->enum('is_agree', [0, 1])->default('0');
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
        Schema::dropIfExists('von_group_application');
    }
}
