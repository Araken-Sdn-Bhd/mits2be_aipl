<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuestionMlColumnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_cbi_onlinetest', function (Blueprint $table) {
            $table->string('question_ml', 2048)->nullable()->after('Question');
            $table->integer('question_order')->default(0)->after('Answer5');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_cbi_onlinetest', function (Blueprint $table) {
            $table->dropColumn('question_ml');
            $table->dropColumn('question_order');
        });
    }
}
