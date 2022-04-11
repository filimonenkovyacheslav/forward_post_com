<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceiptsArchiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipts_archive', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('receipt_id')->nullable();
            $table->bigInteger('worksheet_id')->nullable();
            $table->string('which_admin')->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('tracking_main')->nullable();           
            $table->text('description')->nullable();
            $table->date('update_date')->nullable();
            $table->boolean('status')->default(true);
            $table->text('comment')->nullable();
            $table->boolean('in_trash')->default(false);
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
        Schema::dropIfExists('receipts_archive');
    }
}
