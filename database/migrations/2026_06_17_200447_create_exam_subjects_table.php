<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exam_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->integer('max_marks')->default(100);
            $table->integer('passing_marks')->default(40);
            $table->timestamp('exam_date')->nullable();
            $table->timestamps();
            
            $table->unique(['exam_id', 'subject_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_subjects');
    }
};