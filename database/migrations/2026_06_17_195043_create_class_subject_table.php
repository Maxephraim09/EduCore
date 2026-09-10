<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('class_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->integer('max_marks')->default(100);
            $table->integer('passing_marks')->default(40);
            $table->boolean('is_required')->default(true);
            $table->string('term')->nullable();
            $table->string('academic_year')->nullable();
            $table->timestamps();
            
            $table->unique(['class_id', 'subject_id', 'term']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('class_subject');
    }
};