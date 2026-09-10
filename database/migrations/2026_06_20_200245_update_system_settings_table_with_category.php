<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('system_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('system_settings', 'category')) {
                $table->string('category')->default('general')->after('description');
            }
            if (!Schema::hasColumn('system_settings', 'is_public')) {
                $table->boolean('is_public')->default(false)->after('category');
            }
            if (!Schema::hasColumn('system_settings', 'is_editable')) {
                $table->boolean('is_editable')->default(true)->after('is_public');
            }
        });
    }

    public function down()
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['category', 'is_public', 'is_editable']);
        });
    }
};