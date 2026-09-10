<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('department');
            $table->text('description');
            $table->text('requirements');
            $table->string('salary_range')->nullable();

            $table->date('application_deadline');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            $table->string('job_type')->default('full-time'); // full-time, part-time, contract, temporary
            $table->string('experience_level')->default('mid'); // entry, mid, senior, executive

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'application_deadline']);
            $table->index('department');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};

