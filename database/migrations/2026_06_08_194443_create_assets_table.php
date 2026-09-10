<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('assets')) {
            return;
        }

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->foreignId('category_id')->constrained('asset_categories');
            $table->decimal('purchase_price', 12, 2);
            $table->decimal('current_value', 12, 2);
            $table->date('purchase_date');
            $table->string('supplier');
            $table->string('serial_number')->nullable();
            $table->string('location');
            $table->enum('status', ['active', 'depreciated', 'disposed'])->default('active');
            $table->string('assigned_to')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assets');
    }
};
