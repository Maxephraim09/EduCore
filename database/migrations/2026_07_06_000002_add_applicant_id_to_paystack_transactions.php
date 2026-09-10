<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('paystack_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('paystack_transactions', 'applicant_id')) {
                $table->unsignedBigInteger('applicant_id')->nullable()->after('payment_id');
                $table->foreign('applicant_id')->references('id')->on('applicants')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('paystack_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('paystack_transactions', 'applicant_id')) {
                $table->dropForeign(['applicant_id']);
                $table->dropColumn('applicant_id');
            }
        });
    }
};
