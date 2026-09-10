<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('applicant_id')->nullable()->constrained('users')->nullOnDelete();

            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();

            // Professional Information
            $table->text('qualifications')->nullable();
            $table->text('experience')->nullable();
            $table->text('cover_letter')->nullable();
            $table->text('skills')->nullable();

            // Documents
            $table->string('cv_path')->nullable();
            $table->string('certificates_path')->nullable();
            $table->string('portfolio_path')->nullable();

            // Workflow
            $table->enum('status', [
                'pending',
                'under_review',
                'shortlisted',
                'interviewed',
                'accepted',
                'rejected',
            ])->default('pending');

            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // Review
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            // Interview
            $table->datetime('interview_date')->nullable();
            $table->text('interview_notes')->nullable();
            $table->string('interview_link')->nullable();
            $table->string('interview_location')->nullable();

            // Hiring
            $table->date('hired_date')->nullable();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();

            // Academic year
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'job_post_id']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};

