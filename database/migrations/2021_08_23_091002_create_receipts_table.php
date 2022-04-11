<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('legal_entity')->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('range_number')->nullable();
            $table->string('sum')->nullable();
            $table->string('date')->nullable();
            $table->string('tracking_main')->nullable();
            $table->string('courier_name')->nullable();
            $table->string('comment')->nullable();
            $table->boolean('double')->default(false);
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
        Schema::dropIfExists('receipts');
    }
}
