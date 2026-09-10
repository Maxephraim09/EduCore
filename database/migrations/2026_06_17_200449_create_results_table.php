<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->integer('ca1_score')->nullable()->default(0);
            $table->integer('ca2_score')->nullable()->default(0);
            $table->integer('ca3_score')->nullable()->default(0);
            $table->integer('exam_score')->nullable()->default(0);
            $table->integer('total_score')->nullable()->default(0);
            $table->string('grade')->nullable();
            $table->string('remark')->nullable();
            $table->integer('position')->nullable();
            $table->boolean('is_published')->default(false);
            $table->text('teacher_comment')->nullable();
            $table->text('principal_comment')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'exam_id', 'subject_id']);
            $table->index(['exam_id', 'class_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('results');
    }
};