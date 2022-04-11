<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorksheetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('worksheet', function (Blueprint $table) {
            $table->increments('id');
            $table->string('num_row')->nullable();
            $table->string('date')->nullable();
            $table->string('direction')->nullable();
            $table->string('status')->nullable();
            $table->string('local')->nullable();
            $table->string('tracking')->nullable();
            $table->string('manager_comments')->nullable();
            $table->string('comment')->nullable();
            $table->string('comments')->nullable();
            $table->string('sender')->nullable();
            $table->string('data_sender')->nullable();
            $table->string('recipient')->nullable();
            $table->string('data_recipient')->nullable();
            $table->string('email_recipient')->nullable();
            $table->string('parcel_cost')->nullable();
            $table->string('packaging')->nullable();
            $table->string('pays_parcel')->nullable();
            $table->string('number_weight')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('length')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('shipment_type')->nullable();
            $table->string('parcel_description')->nullable();
            $table->string('position_1')->nullable();
            $table->string('position_2')->nullable();
            $table->string('position_3')->nullable();
            $table->string('position_4')->nullable();
            $table->string('position_5')->nullable();
            $table->string('position_6')->nullable();
            $table->string('position_7')->nullable();
            $table->string('guarantee_text_en')->nullable();
            $table->string('guarantee_text_ru')->nullable();
            $table->string('guarantee_text_he')->nullable();
            $table->string('guarantee_text_ua')->nullable();
            $table->string('payment')->nullable();
            $table->string('phys_weight')->nullable();
            $table->string('volume_weight')->nullable();
            $table->string('quantity')->nullable();
            $table->string('comments_2')->nullable();
            $table->string('cost_price')->nullable();
            $table->string('shipment_cost')->nullable();
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
        Schema::dropIfExists('worksheet');
    }
}
