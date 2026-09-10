<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            // New exam fields
            if (!Schema::hasColumn('exams', 'exam_type')) {
                $table->enum('exam_type', ['end_of_term', 'first_ca', 'second_ca', 'third_ca', 'mock', 'promotion'])->after('term');
            }
            if (!Schema::hasColumn('exams', 'subject_id')) {
                $table->foreignId('subject_id')->nullable()->constrained()->after('exam_type');
            }
            if (!Schema::hasColumn('exams', 'class_id')) {
                $table->foreignId('class_id')->nullable()->constrained('classes')->after('subject_id');
            }
            if (!Schema::hasColumn('exams', 'teacher_id')) {
                $table->foreignId('teacher_id')->nullable()->constrained('employees')->after('class_id');
            }
            if (!Schema::hasColumn('exams', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->after('teacher_id');
            }
            if (!Schema::hasColumn('exams', 'time_allowed')) {
                $table->integer('time_allowed')->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('exams', 'instructions')) {
                $table->text('instructions')->nullable()->after('total_marks');
            }
            if (!Schema::hasColumn('exams', 'questions_data')) {
                $table->json('questions_data')->nullable()->after('instructions');
            }
            if (!Schema::hasColumn('exams', 'shuffle_questions')) {
                $table->boolean('shuffle_questions')->default(false)->after('is_published');
            }
            if (!Schema::hasColumn('exams', 'show_results_immediately')) {
                $table->boolean('show_results_immediately')->default(false)->after('shuffle_questions');
            }
            if (!Schema::hasColumn('exams', 'allow_review')) {
                $table->boolean('allow_review')->default(false)->after('show_results_immediately');
            }
            if (!Schema::hasColumn('exams', 'status')) {
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->after('allow_review');
            }
            if (!Schema::hasColumn('exams', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn([
                'exam_type', 'subject_id', 'class_id', 'teacher_id', 'created_by',
                'time_allowed', 'instructions', 'questions_data',
                'shuffle_questions', 'show_results_immediately', 'allow_review',
                'status', 'published_at'
            ]);
        });
    }
};