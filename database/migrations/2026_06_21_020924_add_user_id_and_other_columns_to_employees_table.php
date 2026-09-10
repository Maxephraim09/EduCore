<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Check if column exists before adding
            if (!Schema::hasColumn('employees', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('employees', 'employee_id')) {
                $table->string('employee_id')->nullable()->unique()->after('user_id');
            }
            
            if (!Schema::hasColumn('employees', 'position')) {
                $table->string('position')->nullable()->after('employee_id');
            }
            
            if (!Schema::hasColumn('employees', 'department')) {
                $table->string('department')->nullable()->after('position');
            }
            
            if (!Schema::hasColumn('employees', 'phone')) {
                $table->string('phone')->nullable()->after('department');
            }
            
            if (!Schema::hasColumn('employees', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('employees', 'gender')) {
                $table->enum('gender', ['Male', 'Female', 'Other'])->nullable()->after('address');
            }
            
            if (!Schema::hasColumn('employees', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('gender');
            }
            
            if (!Schema::hasColumn('employees', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('date_of_birth');
            }
            
            if (!Schema::hasColumn('employees', 'salary')) {
                $table->decimal('salary', 15, 2)->default(0)->after('hire_date');
            }
            
            if (!Schema::hasColumn('employees', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('salary');
            }
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id', 
                'employee_id', 
                'position', 
                'department', 
                'phone', 
                'address', 
                'gender', 
                'date_of_birth', 
                'hire_date', 
                'salary',
                'is_active'
            ]);
        });
    }
};