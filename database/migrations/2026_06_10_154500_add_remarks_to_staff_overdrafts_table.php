<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('staff_overdrafts', 'remarks')) {
            Schema::table('staff_overdrafts', function (Blueprint $table) {
                $table->text('remarks')->nullable()->after('approved_by');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('staff_overdrafts', 'remarks')) {
            Schema::table('staff_overdrafts', function (Blueprint $table) {
                $table->dropColumn('remarks');
            });
        }
    }
};
