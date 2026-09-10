<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fee_payments')) {
            Schema::create('fee_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->onDelete('cascade');
                $table->unsignedBigInteger('fee_structure_id')->nullable();
                $table->string('receipt_number')->unique();
                $table->string('transaction_ref')->nullable()->unique();
                $table->decimal('amount', 10, 2)->default(0);
                $table->decimal('amount_paid', 10, 2)->default(0);
                $table->decimal('balance', 10, 2)->default(0);
                $table->date('payment_date')->nullable();
                $table->string('payment_method')->default('paystack');
                $table->string('payment_status')->default('pending');
                $table->string('term')->nullable();
                $table->string('academic_year')->nullable();
                $table->json('payment_details')->nullable();
                $table->text('remarks')->nullable();
                $table->unsignedBigInteger('collected_by')->nullable();
                $table->timestamps();
            });

            return;
        }

        Schema::table('fee_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_payments', 'fee_structure_id')) {
                $table->unsignedBigInteger('fee_structure_id')->nullable();
            }

            if (!Schema::hasColumn('fee_payments', 'transaction_ref')) {
                $table->string('transaction_ref')->nullable()->unique();
            }

            if (!Schema::hasColumn('fee_payments', 'amount_paid')) {
                $table->decimal('amount_paid', 10, 2)->default(0);
            }

            if (!Schema::hasColumn('fee_payments', 'balance')) {
                $table->decimal('balance', 10, 2)->default(0);
            }

            if (!Schema::hasColumn('fee_payments', 'payment_status')) {
                $table->string('payment_status')->default('pending');
            }

            if (!Schema::hasColumn('fee_payments', 'payment_details')) {
                $table->json('payment_details')->nullable();
            }

            if (!Schema::hasColumn('fee_payments', 'collected_by')) {
                $table->unsignedBigInteger('collected_by')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::dropIfExists('fee_payments');
    }
};
