<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeletedLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deleted_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('table_name', 50)->nullable();
            $table->unsignedInteger('worksheet_id');
            $table->string('packing_num',30)->nullable();
            $table->text('packing_files')->nullable();            
            $table->string('site_name', 10)->nullable();
            $table->string('date', 20)->nullable(); 
            $table->string('direction', 20)->nullable();
            $table->string('tariff', 20)->nullable();
            $table->string('status', 100)->nullable();    
            $table->string('status_date', 20)->nullable(); 
            $table->string('partner', 20)->nullable();     
            $table->string('tracking_main', 20)->nullable();
            $table->string('parcels_qty', 10)->default('1');
            $table->string('tracking_local', 20)->nullable();
            $table->string('pallet_number', 20)->nullable();
            $table->text('comments_1')->nullable();
            $table->text('comments_2')->nullable();          
            $table->string('shipper_name', 150)->nullable();
            $table->string('shipper_country', 50)->nullable();
            $table->string('shipper_city', 50)->nullable();
            $table->string('passport_number', 20)->nullable();
            $table->string('return_date', 20)->nullable();
            $table->text('shipper_address')->nullable();
            $table->string('standard_phone', 50)->nullable();
            $table->string('shipper_phone', 50)->nullable();
            $table->string('shipper_id', 50)->nullable();
            $table->string('consignee_name', 150)->nullable();
            $table->string('consignee_country', 50)->nullable();
            $table->string('house_name', 50)->nullable();
            $table->string('post_office', 50)->nullable();
            $table->string('region', 150)->nullable();
            $table->string('district', 150)->nullable();
            $table->string('state_pincode', 50)->nullable();
            $table->text('consignee_address')->nullable();
            $table->string('recipient_street', 150)->nullable();
            $table->string('recipient_room', 20)->nullable();
            $table->string('body', 20)->nullable();
            $table->string('consignee_phone', 50)->nullable();
            $table->string('consignee_id', 50)->nullable();
            $table->text('shipped_items')->nullable();
            $table->string('shipment_val', 50)->nullable();
            $table->string('operator', 50)->nullable();
            $table->string('courier', 50)->nullable();
            $table->text('delivery_date_comments')->nullable();
            $table->string('weight', 20)->nullable();
            $table->string('width', 20)->nullable();
            $table->string('height', 20)->nullable();
            $table->string('length', 20)->nullable();
            $table->string('volume_weight', 20)->nullable();
            $table->string('lot', 20)->nullable();
            $table->string('quantity_things', 20)->nullable();
            $table->text('payment_date_comments')->nullable();
            $table->string('amount_payment', 20)->nullable();
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
        Schema::dropIfExists('deleted_logs');
    }
}
