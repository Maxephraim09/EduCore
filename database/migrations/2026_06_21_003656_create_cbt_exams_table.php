// create_cbt_exams_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cbt_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['test', 'exam', 'quiz', 'assignment']);
            $table->enum('term', ['First Term', 'Second Term', 'Third Term']);
            $table->string('academic_year');
            $table->integer('duration_minutes');
            $table->integer('total_marks')->default(100);
            $table->integer('passing_marks')->default(40);
            $table->integer('total_questions')->default(0);
            $table->boolean('is_randomized')->default(false);
            $table->boolean('show_results_immediately')->default(false);
            $table->enum('status', ['draft', 'pending', 'approved', 'published', 'closed', 'archived'])->default('draft');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['subject_id', 'class_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cbt_exams');
    }
};