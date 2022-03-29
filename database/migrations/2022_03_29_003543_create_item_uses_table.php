<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemUsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_uses', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('user_name', 255);
            $table->dateTime('start_use');
            $table->dateTime('end_use')->nullable();
            $table->enum('status', ['in_use', 'finish']);
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
        Schema::dropIfExists('item_uses');
    }
}
