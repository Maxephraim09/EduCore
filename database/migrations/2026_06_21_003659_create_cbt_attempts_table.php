// create_cbt_attempts_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cbt_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cbt_exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->integer('duration_used')->default(0);
            $table->integer('score')->nullable();
            $table->integer('total_answered')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('wrong_answers')->default(0);
            $table->integer('skipped_questions')->default(0);
            $table->float('percentage')->nullable();
            $table->string('grade')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['started', 'in_progress', 'paused', 'completed', 'timed_out']);
            $table->json('answers')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->boolean('is_flagged')->default(false);
            $table->text('flag_reason')->nullable();
            $table->timestamps();
            
            $table->unique(['cbt_exam_id', 'student_id']);
            $table->index(['student_id', 'status']);
            $table->index('submitted_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cbt_attempts');
    }
};