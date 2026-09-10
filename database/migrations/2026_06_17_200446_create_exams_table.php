<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['continuous_assessment', 'mid_term', 'end_term', 'promotion']);
            $table->string('term');
            $table->string('academic_year');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_marks')->default(100);
            $table->integer('passing_marks')->default(40);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exams');
    }
};