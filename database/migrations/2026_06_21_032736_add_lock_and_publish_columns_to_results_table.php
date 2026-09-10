<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('results', function (Blueprint $table) {
            if (!Schema::hasColumn('results', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('is_published');
            }
            if (!Schema::hasColumn('results', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('is_locked');
            }
        });
    }

    public function down()
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn(['is_locked', 'published_at']);
        });
    }
};