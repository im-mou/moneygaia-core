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

            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('ammount', 9, 2);
            $table->boolean('starred')->default(false);

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('credit_account_id');
            $table->unsignedBigInteger('transaction_type_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('credit_account_id')->references('id')->on('credit_accounts');
            $table->foreign('transaction_type_id')->references('id')->on('transaction_types');

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
