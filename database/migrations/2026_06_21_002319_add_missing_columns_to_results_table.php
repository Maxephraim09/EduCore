<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('results', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('results', 'score')) {
                $table->decimal('score', 5, 2)->after('class_id');
            }
            
            if (!Schema::hasColumn('results', 'grade')) {
                $table->string('grade')->nullable()->after('score');
            }
            
            if (!Schema::hasColumn('results', 'remarks')) {
                $table->text('remarks')->nullable()->after('grade');
            }
            
            if (!Schema::hasColumn('results', 'is_published')) {
                $table->boolean('is_published')->default(false)->after('remarks');
            }
        });
    }

    public function down()
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn(['score', 'grade', 'remarks', 'is_published']);
        });
    }
};