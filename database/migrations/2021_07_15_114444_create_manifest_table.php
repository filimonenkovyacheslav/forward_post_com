<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManifestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manifest', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number')->nullable();
            $table->string('tracking')->nullable();
            $table->string('sender_country')->nullable();
            $table->text('sender_name')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_city')->nullable();
            $table->string('recipient_address')->nullable();
            $table->string('content')->nullable();
            $table->string('quantity')->nullable();
            $table->string('weight')->nullable();
            $table->string('cost')->nullable();
            $table->string('attachment_number')->nullable();
            $table->integer('work_sheet_id')->nullable();
            $table->string('batch_number')->nullable();
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
        Schema::dropIfExists('manifest');
    }
}
