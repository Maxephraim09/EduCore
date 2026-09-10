<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds `class_id` and `fee_type` to `fee_structures` and attempts to backfill `class_id`
     */
    public function up()
    {
        // Add columns only if they don't exist (avoid duplicate column errors on re-run)
        if (!Schema::hasColumn('fee_structures', 'class_id') || !Schema::hasColumn('fee_structures', 'fee_type')) {
            Schema::table('fee_structures', function (Blueprint $table) {
                if (!Schema::hasColumn('fee_structures', 'class_id')) {
                    $table->unsignedBigInteger('class_id')->nullable()->after('class');
                }
                if (!Schema::hasColumn('fee_structures', 'fee_type')) {
                    $table->string('fee_type')->nullable()->after('fee_name');
                }
            });
        }

        // Backfill class_id by matching class name to classes.name or full_class_name
        $feeRows = DB::table('fee_structures')->get();
        foreach ($feeRows as $row) {
            if (empty($row->class)) continue;

            $class = DB::table('classes')
                ->where('name', $row->class)
                ->orWhere('full_class_name', $row->class)
                ->first();

            if ($class) {
                DB::table('fee_structures')->where('id', $row->id)->update(['class_id' => $class->id]);
            }

            // backfill fee_type if empty using fee_name
            if (empty($row->fee_type) && !empty($row->fee_name)) {
                DB::table('fee_structures')->where('id', $row->id)->update(['fee_type' => $row->fee_name]);
            }
        }

        // Add foreign key if classes table exists
        if (Schema::hasTable('classes')) {
            Schema::table('fee_structures', function (Blueprint $table) {
                $table->foreign('class_id')->references('id')->on('classes')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            // drop foreign first if exists
            if (Schema::hasColumn('fee_structures', 'class_id')) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $table->dropForeign(['class_id']);
            }

            if (Schema::hasColumn('fee_structures', 'class_id')) {
                $table->dropColumn('class_id');
            }
            if (Schema::hasColumn('fee_structures', 'fee_type')) {
                $table->dropColumn('fee_type');
            }
        });
    }
};
