<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fee_structures')) {
            Schema::create('fee_structures', function (Blueprint $table) {
                $table->id();
                $table->string('fee_name');
                $table->string('fee_code')->unique();
                $table->string('class');
                $table->string('term')->nullable();
                $table->string('academic_year');
                $table->decimal('amount', 10, 2);
                $table->text('description')->nullable();
                $table->boolean('is_compulsory')->default(true);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            return;
        }

        Schema::table('fee_structures', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_structures', 'fee_name')) {
                $table->string('fee_name')->nullable();
            }

            if (!Schema::hasColumn('fee_structures', 'fee_code')) {
                $table->string('fee_code')->nullable()->unique();
            }

            if (!Schema::hasColumn('fee_structures', 'is_compulsory')) {
                $table->boolean('is_compulsory')->default(true);
            }
        });
    }

    public function down()
    {
        Schema::dropIfExists('fee_structures');
    }
};
