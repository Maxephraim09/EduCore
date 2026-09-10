<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('paystack_transactions')) {
            return;
        }

        Schema::create('paystack_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('transaction_id')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency')->default('NGN');
            $table->string('status')->default('pending');
            $table->string('gateway_response')->nullable();
            $table->string('channel')->nullable();
            $table->string('ip_address')->nullable();
            $table->json('paystack_response')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('paystack_transactions');
    }
};
