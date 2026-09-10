<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('class_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('class_categories')->insert([
            ['name' => 'CRACH', 'code' => 'CRACH', 'description' => 'Creche / Nursery category', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'NURSARY', 'code' => 'NURSARY', 'description' => 'Nursery category', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PRIMARY', 'code' => 'PRIMARY', 'description' => 'Primary school category', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'JSS', 'code' => 'JSS', 'description' => 'Junior Secondary School category', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'SSS', 'code' => 'SSS', 'description' => 'Senior Secondary School category', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('class_category_id')->nullable()->constrained('class_categories')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['class_category_id']);
            $table->dropColumn('class_category_id');
        });

        Schema::dropIfExists('class_categories');
    }
};
