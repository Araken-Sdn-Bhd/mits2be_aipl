<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementMgmtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_mgmt', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('added_by');
            $table->string('title', 512);
            $table->longText('content');
            $table->string('document')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('branch_id');
            $table->string('audience_ids'); // 0/1->Psychiatrist, 0/1->Medical Officer 0/1->Counsellor 0/1->Occupational Therapist, 0/1->Staff Nurse 0/1->Healthcare Assistant
            $table->enum('status', [0, 1, 2]); // 0->draft, 1->Publish 2->deleted
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
        Schema::dropIfExists('announcement_mgmt');
    }
}
