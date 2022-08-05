<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewWorksheetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_worksheet', function (Blueprint $table) {
            $table->increments('id');
            $table->string('site_name')->default('DD-C');
            $table->string('packing_number')->nullable();
            $table->string('date')->nullable();
            $table->string('direction')->nullable();
            $table->string('tariff')->nullable();           
            $table->text('status')->nullable();
            $table->string('status_date')->nullable();
            $table->string('order_date')->nullable();
            $table->string('partner')->nullable();
            $table->string('tracking_main')->nullable();
            $table->string('order_number')->nullable();
            $table->string('parcels_qty')->nullable();
            $table->string('tracking_local')->nullable();
            $table->string('tracking_transit')->nullable();
            $table->string('pallet_number')->nullable();
            $table->text('comment_2')->nullable();
            $table->text('comments')->nullable();
            $table->text('sender_name')->nullable();
            $table->string('sender_country')->nullable();
            $table->string('shipper_region')->nullable();
            $table->string('sender_city')->nullable();            
            $table->string('sender_postcode')->nullable();
            $table->text('sender_address')->nullable();
            $table->string('standard_phone')->nullable();
            $table->string('sender_phone')->nullable();
            $table->string('sender_passport')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_country')->nullable();
            $table->string('region')->nullable();
            $table->string('district')->nullable();
            $table->string('recipient_city')->nullable();
            $table->string('recipient_postcode')->nullable();
            $table->string('recipient_street')->nullable();
            $table->string('recipient_house')->nullable();
            $table->string('body')->nullable();
            $table->string('recipient_room')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('recipient_passport')->nullable();
            $table->string('recipient_email')->nullable();
            $table->text('package_content')->nullable();
            $table->string('package_cost')->nullable();
            $table->string('courier')->nullable();
            $table->string('pick_up_date')->nullable();
            $table->string('weight')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('length')->nullable();
            $table->string('volume_weight')->nullable();
            $table->string('quantity_things')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('pay_date')->nullable();
            $table->string('pay_sum')->nullable();
            $table->string('status_en')->nullable();
            $table->string('status_he')->nullable();
            $table->string('status_ua')->nullable();
            $table->string('recipient_name_customs')->nullable();
            $table->string('recipient_country_customs')->nullable();
            $table->string('recipient_city_customs')->nullable();
            $table->string('recipient_postcode_customs')->nullable();
            $table->string('recipient_street_customs')->nullable();
            $table->string('recipient_house_customs')->nullable();
            $table->string('recipient_room_customs')->nullable();
            $table->string('recipient_phone_customs')->nullable();
            $table->string('recipient_passport_customs')->nullable();
            $table->date('update_status_date')->nullable();
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
        Schema::dropIfExists('new_worksheet');
    }
}
