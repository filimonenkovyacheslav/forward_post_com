<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignedDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signed_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('worksheet_id')->nullable();
            $table->unsignedInteger('eng_worksheet_id')->nullable();
            $table->unsignedInteger('draft_id')->nullable();
            $table->unsignedInteger('eng_draft_id')->nullable();
            $table->foreign('worksheet_id')->references('id')->on('new_worksheet')->onDelete('cascade');
            $table->foreign('eng_worksheet_id')->references('id')->on('phil_ind_worksheet')->onDelete('cascade');
            $table->foreign('draft_id')->references('id')->on('courier_draft_worksheet')->onDelete('cascade');
            $table->foreign('eng_draft_id')->references('id')->on('courier_eng_draft_worksheet')->onDelete('cascade');
            $table->string('signature')->nullable();
            $table->string('pdf_file')->nullable();
            $table->boolean('first_file')->default(true);
            $table->string('signature_for_cancel')->nullable();
            $table->string('file_for_cancel')->nullable();
            $table->unsignedInteger('old_document_id')->nullable();
            $table->string('uniq_id', 20)->nullable();
            $table->date('date')->nullable();
            $table->string('screen_ru_form')->nullable();
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
        Schema::dropIfExists('signed_documents');
    }
}
