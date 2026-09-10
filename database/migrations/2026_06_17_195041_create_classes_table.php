<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // JSS 1, SSS 3, etc.
            $table->string('code')->unique(); // JSS1, SSS3A
            $table->string('section')->nullable(); // A, B, C
            $table->string('full_name')->nullable(); // JSS 1A, SSS 3B
            $table->foreignId('class_teacher_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->integer('capacity')->default(50);
            $table->integer('current_students')->default(0);
            $table->string('academic_year')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('classes');
    }
};