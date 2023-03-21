<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpdatesArchiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('updates_archive', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('worksheet_id')->nullable();
            $table->unsignedInteger('eng_worksheet_id')->nullable();
            $table->unsignedInteger('draft_id')->nullable();
            $table->unsignedInteger('eng_draft_id')->nullable();
            $table->foreign('worksheet_id')->references('id')->on('new_worksheet')->onDelete('cascade');
            $table->foreign('eng_worksheet_id')->references('id')->on('phil_ind_worksheet')->onDelete('cascade');
            $table->foreign('draft_id')->references('id')->on('courier_draft_worksheet')->onDelete('cascade');
            $table->foreign('eng_draft_id')->references('id')->on('courier_eng_draft_worksheet')->onDelete('cascade');
            $table->date('updates_date');
            $table->string('user_name', 100);
            $table->string('column_name', 50);
            $table->text('old_data')->nullable();
            $table->text('new_data')->nullable();
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
        Schema::dropIfExists('updates_archive');
    }
}
