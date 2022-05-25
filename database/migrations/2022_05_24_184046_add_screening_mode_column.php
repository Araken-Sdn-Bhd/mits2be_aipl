<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScreeningModeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('von_org_representative_background', function (Blueprint $table) {
            $table->enum('screening_mode', [0, 1])->default('0')->after('status')->index('screening');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('von_org_representative_background', function (Blueprint $table) {
            $table->dropColumn('screening_mode');
        });
    }
}
