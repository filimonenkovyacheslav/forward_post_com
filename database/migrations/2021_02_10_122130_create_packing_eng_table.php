<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackingEngTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packing_eng', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tracking')->nullable();
            $table->string('country')->nullable();
            $table->string('shipper_name')->nullable();
            $table->text('shipper_address')->nullable();
            $table->string('shipper_phone')->nullable();
            $table->string('shipper_id')->nullable();
            $table->string('consignee_name')->nullable();
            $table->text('consignee_address')->nullable();
            $table->string('consignee_phone')->nullable();
            $table->string('consignee_id')->nullable();
            $table->string('length')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->text('items')->nullable(); 
            $table->string('shipment_val')->nullable(); 
            $table->integer('work_sheet_id')->nullable();  
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
        Schema::dropIfExists('packing_eng');
    }
}
