<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarehouseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse', function (Blueprint $table) {
            $table->increments('id');
            $table->text('tracking_numbers')->nullable();
            $table->string('pallet')->nullable();
            $table->string('cell')->nullable();
            $table->string('arrived')->nullable();
            $table->string('left')->nullable();
            $table->string('lot')->nullable();
            $table->text('notifications')->nullable();
            $table->string('which_admin')->nullable();
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
        Schema::dropIfExists('warehouse');
    }
}
