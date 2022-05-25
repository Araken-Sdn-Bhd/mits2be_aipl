<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTestSectionToAttemptTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attempt_test', function (Blueprint $table) {
            $table->renameColumn('Type', 'test_name');
            $table->string('test_section_name')->nullable(true);
            $table->string('user_ip_address')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attempt_test', function (Blueprint $table) {
            $table->renameColumn('test_name', 'Type');
            $table->dropColumn('test_section_name');
            $table->dropColumn('user_ip_address');
        });
    }
}
