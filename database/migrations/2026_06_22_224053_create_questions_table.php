<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_paper_id')->constrained('question_papers')->onDelete('cascade');
            $table->text('question');
            $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'essay'])->default('multiple_choice');
            $table->integer('marks')->default(1);
            $table->json('options')->nullable();
            $table->string('correct_answer')->nullable();
            $table->text('explanation')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index('question_paper_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
};