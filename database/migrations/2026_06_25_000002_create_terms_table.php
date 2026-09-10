<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->string('status')->default('active');
            $table->integer('sequence')->default(1);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['academic_year_id', 'name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('terms');
    }
};

