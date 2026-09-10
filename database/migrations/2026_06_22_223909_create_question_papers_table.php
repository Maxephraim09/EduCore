<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('question_papers', function (Blueprint $table) {
            $table->id();
            
            // Academic Information
            $table->string('academic_year');
            $table->string('term'); // First Term, Second Term, Third Term
            
            // Relationships
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            // Question Paper Details
            $table->string('title');
            $table->string('code')->unique();
            $table->text('instructions')->nullable();
            $table->text('description')->nullable();
            
            // Timing & Marks
            $table->integer('time_allowed')->default(120); // in minutes
            $table->integer('total_marks')->default(0);
            $table->integer('passing_marks')->default(40);
            
            // Dates
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            
            // Status
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            
            // Settings
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('show_results_immediately')->default(false);
            $table->boolean('allow_review')->default(false);
            
            $table->timestamps();
            
            // Composite unique key
            $table->unique(['academic_year', 'term', 'class_id', 'subject_id'], 'unique_question_paper');
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_papers');
    }
};