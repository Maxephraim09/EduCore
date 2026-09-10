<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('report_cards')) {
            return;
        }

        Schema::table('report_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('report_cards', 'exam_id')) {
                $table->foreignId('exam_id')->nullable()->after('student_id')->constrained('exams')->nullOnDelete();
            }

            if (!Schema::hasIndex('report_cards', 'report_card_unique_exam')) {
                $table->unique(['student_id', 'exam_id'], 'report_card_unique_exam');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('report_cards')) {
            return;
        }

        Schema::table('report_cards', function (Blueprint $table) {
            if (Schema::hasColumn('report_cards', 'exam_id')) {
                $table->dropConstrainedForeignId('exam_id');
            }

            if (Schema::hasIndex('report_cards', 'report_card_unique_exam')) {
                $table->dropUnique('report_card_unique_exam');
            }
        });
    }
};
