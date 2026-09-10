<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('gradings')) {
            return;
        }

        Schema::create('gradings', function (Blueprint $table) {
            $table->id();
            $table->integer('ca1_max')->default(10);
            $table->integer('ca2_max')->default(10);
            $table->integer('ca3_max')->default(10);
            $table->integer('exam_max')->default(60);
            $table->integer('practical_max')->default(10)->nullable();
            $table->integer('passing_marks')->default(40);
            $table->boolean('has_practical')->default(true);
            $table->integer('total_max')->default(100);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gradings');
    }
};
