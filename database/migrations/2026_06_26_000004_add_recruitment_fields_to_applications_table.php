<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Academic year relation
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();

            // Registration workflow fields
            $table->boolean('registration_completed')->default(false);
            $table->timestamp('registration_completed_at')->nullable();
            $table->decimal('registration_fee_paid', 10, 2)->default(0);
            $table->string('registration_payment_reference')->nullable();
            $table->timestamp('registration_payment_date')->nullable();

            // Temporary password/account tracking
            $table->string('student_password')->nullable();
            $table->timestamp('student_account_created_at')->nullable();

            // Document tracking
            $table->json('uploaded_documents')->nullable();
            $table->json('document_verification_status')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);

            $table->dropColumn([
                'academic_year_id',
                'registration_completed',
                'registration_completed_at',
                'registration_fee_paid',
                'registration_payment_reference',
                'registration_payment_date',
                'student_password',
                'student_account_created_at',
                'uploaded_documents',
                'document_verification_status',
            ]);
        });
    }
};

