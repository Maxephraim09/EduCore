<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            
            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('date_of_birth');
            $table->string('gender');
            $table->string('nationality')->nullable();
            $table->string('state_of_origin')->nullable();
            $table->string('local_government')->nullable();
            $table->string('religion')->nullable();
            $table->string('blood_group')->nullable();
            
            // Contact Information
            $table->string('email')->unique();
            $table->string('phone');
            $table->text('address');
            
            // Parent/Guardian Information
            $table->string('parent_name');
            $table->string('parent_phone');
            $table->string('parent_email')->nullable();
            $table->string('parent_occupation')->nullable();
            
            // Academic Information
            $table->string('applied_class')->nullable();
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null');
            $table->string('previous_school')->nullable();
            $table->string('previous_class')->nullable();
            $table->integer('previous_year')->nullable();
            
            // Application Details
            $table->string('application_number')->unique();
            $table->string('application_type')->default('new'); // new, transfer, re-admission
            $table->string('status')->default('pending'); // pending, under_review, approved, admitted, rejected
            $table->text('remarks')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Payment Information
            $table->string('payment_reference')->nullable();
            $table->decimal('application_fee', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            $table->timestamp('payment_date')->nullable();
            $table->text('payment_details')->nullable();
            
            // Documents
            $table->string('birth_certificate')->nullable();
            $table->string('passport_photo')->nullable();
            $table->string('report_card')->nullable();
            $table->string('other_documents')->nullable();
            
            // Admission Details
            $table->foreignId('admitted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('admission_number')->nullable()->unique();
            $table->timestamp('admitted_at')->nullable();
            $table->timestamp('admission_letter_sent_at')->nullable();
            
            // Student Account (after admission)
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Fee Payment Status
            $table->decimal('total_fees', 10, 2)->default(0);
            $table->decimal('fees_paid', 10, 2)->default(0);
            $table->string('fee_status')->default('unpaid'); // unpaid, partial, paid
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['email', 'phone']);
            $table->index(['status', 'payment_status']);
            $table->index('application_number');
            $table->index('admission_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('applications');
    }
};