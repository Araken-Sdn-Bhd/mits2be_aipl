<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Nullable;

class AddHarmFieldsToSharpRegistraionFinalStepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sharp_registraion_final_step', function (Blueprint $table) {
            $table->date('harm_date')->nullable()->after('self_harm')->index('harm_date');
            $table->string('harm_time')->nullable()->after('self_harm')->index('harm_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sharp_registraion_final_step', function (Blueprint $table) {
            $table->dropColumn('harm_date');
            $table->dropColumn('harm_time');
        });
    }
}
