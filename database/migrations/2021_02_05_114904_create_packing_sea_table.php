<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackingSeaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packing_sea', function (Blueprint $table) {
            $table->increments('id');
            $table->string('payer')->nullable();
            $table->string('contract')->nullable();
            $table->string('type')->nullable();
            $table->string('track_code')->nullable();
            $table->string('full_shipper')->nullable();
            $table->string('full_consignee')->nullable();
            $table->string('country_code')->nullable();
            $table->string('postcode')->nullable();
            $table->string('region')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('house')->nullable();
            $table->string('body')->nullable();
            $table->string('room')->nullable();
            $table->string('phone')->nullable();
            $table->string('tariff')->nullable();
            $table->string('tariff_cent')->nullable();
            $table->string('weight_kg')->nullable();
            $table->string('weight_g')->nullable();
            $table->string('service_code')->nullable();
            $table->string('amount_1')->nullable();
            $table->string('amount_2')->nullable();
            $table->string('attachment_number')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('amount_3')->nullable();
            $table->string('weight_enclosures_kg')->nullable();
            $table->string('weight_enclosures_g')->nullable();
            $table->string('value_euro')->nullable();
            $table->string('value_cent')->nullable();
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
        Schema::dropIfExists('packing_sea');
    }
}
