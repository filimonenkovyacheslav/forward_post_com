<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhilIndWorksheetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phil_ind_worksheet', function (Blueprint $table) {
            $table->increments('id');
            $table->string('date')->nullable(); 
            $table->string('direction')->nullable();
            $table->string('status')->nullable(); 
            $table->string('status_date')->nullable();  
            $table->string('order_date')->nullable();       
            $table->string('tracking_main')->nullable();
            $table->string('order_number')->nullable();
            $table->string('parcels_qty')->nullable();
            $table->string('tracking_local')->nullable();
            $table->string('pallet_number')->nullable();
            $table->text('comments_1')->nullable();
            $table->text('comments_2')->nullable();          
            $table->string('shipper_name')->nullable();
            $table->string('shipper_country')->nullable();
            $table->string('shipper_region')->nullable();
            $table->string('shipper_city')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('return_date')->nullable();
            $table->text('shipper_address')->nullable();            
            $table->string('standard_phone')->nullable();
            $table->string('shipper_phone')->nullable();
            $table->string('shipper_id')->nullable();
            $table->string('consignee_name')->nullable();
            $table->string('consignee_country')->nullable();
            $table->string('house_name')->nullable();
            $table->string('post_office')->nullable();
            $table->string('district')->nullable();
            $table->string('state_pincode')->nullable();
            $table->text('consignee_address')->nullable();
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
            $table->text('payment_date_comments')->nullable();
            $table->string('amount_payment')->nullable();
            $table->string('status_ru')->nullable();
            $table->string('status_he')->nullable();
            $table->string('consignee_name_customs')->nullable();
            $table->text('consignee_address_customs')->nullable();
            $table->string('consignee_phone_customs')->nullable();
            $table->string('consignee_id_customs')->nullable();
            $table->string('background')->nullable();  
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
        Schema::dropIfExists('phil_ind_worksheet');
    }
}
