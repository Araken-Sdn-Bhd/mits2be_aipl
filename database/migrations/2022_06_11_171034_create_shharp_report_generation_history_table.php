<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShharpReportGenerationHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shharp_report_generate_history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('generated_by');
            $table->smallInteger('report_month')->index('mnth');
            $table->integer('report_year')->index('yr');
            $table->string('file_path');
            $table->string('report_type')->index('rpt_type');
            $table->enum('status', [0, 1, 2, 3, 4])->default('1')->index('status');
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
        Schema::dropIfExists('shharp_report_generate_history');
    }
}
