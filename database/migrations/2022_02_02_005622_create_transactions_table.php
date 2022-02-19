<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets');
            $table->foreignId('member_id')->constrained('members');
            $table->foreignId('user_id')->constrained('users');
            $table->char('invoice', 100);
            $table->dateTime('date');
            $table->dateTime('payment_date')->nullable();
            $table->dateTime('deadline');
            $table->double('additional_cost')->default(0);
            $table->double('discount')->default(0);
            $table->enum('discount_type', ['nominal', 'percent'])->nullable();
            $table->double('tax')->default(0);
            $table->enum('status', ['new', 'process', 'done', 'taken'])->default('new');
            $table->enum('payment_status', ['paid', 'unpaid']);
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
        Schema::dropIfExists('transactions');
    }
}
