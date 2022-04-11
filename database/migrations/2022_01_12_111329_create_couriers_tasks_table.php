<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouriersTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('couriers_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('worksheet_id')->nullable();
            $table->unsignedInteger('eng_worksheet_id')->nullable();
            $table->unsignedInteger('draft_id')->nullable();
            $table->unsignedInteger('eng_draft_id')->nullable();
            $table->foreign('worksheet_id')->references('id')->on('new_worksheet')->onDelete('cascade');
            $table->foreign('eng_worksheet_id')->references('id')->on('phil_ind_worksheet')->onDelete('cascade');
            $table->foreign('draft_id')->references('id')->on('courier_draft_worksheet')->onDelete('cascade');
            $table->foreign('eng_draft_id')->references('id')->on('courier_eng_draft_worksheet')->onDelete('cascade');
            $table->string('status', 100)->nullable();
            $table->string('direction', 50)->nullable();
            $table->string('site_name', 10)->nullable();
            $table->unsignedTinyInteger('parcels_qty')->nullable();
            $table->unsignedTinyInteger('order_number')->nullable();
            $table->text('comments_1')->nullable();
            $table->text('comments_2')->nullable();
            $table->string('shipper_name', 150)->nullable();            
            $table->string('shipper_country', 50)->nullable();
            $table->string('shipper_region')->nullable();
            $table->string('shipper_city', 100)->nullable();
            $table->text('shipper_address')->nullable();
            $table->string('standard_phone', 50)->nullable();
            $table->string('courier', 50)->nullable();
            $table->text('pick_up_date_comments')->nullable();
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
        Schema::dropIfExists('couriers_tasks');
    }
}
