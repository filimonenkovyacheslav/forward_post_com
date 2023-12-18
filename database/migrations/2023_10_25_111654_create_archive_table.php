<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArchiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archive', function (Blueprint $table) {
            $table->increments('id');
            $table->string('table_name', 50)->nullable();
            $table->unsignedInteger('worksheet_id');
            $table->string('site_name', 10)->nullable();
            $table->string('date', 20)->nullable(); 
            $table->string('direction', 20)->nullable();
            $table->string('tariff', 20)->nullable();
            $table->string('status')->nullable();    
            $table->string('status_date', 20)->nullable(); 
            $table->string('order_date')->nullable();
            $table->string('partner', 20)->nullable();     
            $table->string('tracking_main')->nullable();
            $table->string('parcels_qty')->default('1');
            $table->string('tracking_local')->nullable();
            $table->text('pallet_number')->nullable();
            $table->text('comments_1')->nullable();
            $table->text('comments_2')->nullable();          
            $table->string('shipper_name')->nullable();
            $table->string('shipper_country')->nullable();
            $table->string('shipper_city')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('return_date')->nullable();
            $table->text('shipper_address')->nullable();
            $table->string('standard_phone', 50)->nullable();
            $table->string('shipper_phone')->nullable();
            $table->string('shipper_id')->nullable();
            $table->string('consignee_name')->nullable();
            $table->string('consignee_country')->nullable();
            $table->string('house_name')->nullable();
            $table->string('post_office')->nullable();
            $table->string('region')->nullable();
            $table->string('district')->nullable();
            $table->string('state_pincode')->nullable();
            $table->text('consignee_address')->nullable();
            $table->string('recipient_street')->nullable();
            $table->string('recipient_room')->nullable();
            $table->string('body')->nullable();
            $table->string('consignee_phone')->nullable();
            $table->string('consignee_id')->nullable();
            $table->text('shipped_items')->nullable();
            $table->string('shipment_val')->nullable();
            $table->string('operator')->nullable();
            $table->string('courier')->nullable();
            $table->text('delivery_date_comments')->nullable();
            $table->string('weight')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('length')->nullable();
            $table->string('volume_weight')->nullable();
            $table->string('lot')->nullable();
            $table->string('quantity_things')->nullable();
            $table->text('payment_date_comments')->nullable();
            $table->string('amount_payment')->nullable();
            $table->string('status_ru')->nullable();
            $table->string('status_he')->nullable();
            $table->string('status_ua')->nullable();
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
        Schema::dropIfExists('archive');
    }
}
