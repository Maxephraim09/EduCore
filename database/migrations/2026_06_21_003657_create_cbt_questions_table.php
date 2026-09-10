// create_cbt_questions_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cbt_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cbt_exam_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['multiple_choice', 'true_false', 'fill_blank', 'essay']);
            $table->text('question');
            $table->text('explanation')->nullable();
            $table->integer('marks')->default(1);
            $table->string('correct_answer')->nullable();
            $table->json('options')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('cbt_exam_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cbt_questions');
    }
};