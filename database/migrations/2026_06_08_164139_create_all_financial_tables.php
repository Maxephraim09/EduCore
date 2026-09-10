<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Users table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['super_admin', 'admin', 'accountant', 'teacher', 'frontdesk', 'parent', 'student'])->default('student');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // Students table
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('admission_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->text('address');
            $table->date('date_of_birth');
            $table->string('class');
            $table->string('section')->nullable();
            $table->string('parent_name');
            $table->string('parent_phone');
            $table->string('parent_email');
            $table->decimal('total_fees', 10, 2)->default(0);
            $table->decimal('paid_fees', 10, 2)->default(0);
            $table->decimal('due_fees', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Fee structures table
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->string('fee_type');
            $table->string('class');
            $table->decimal('amount', 10, 2);
            $table->string('term')->nullable();
            $table->string('academic_year');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Fee payments table
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('receipt_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->string('payment_method');
            $table->string('transaction_id')->nullable();
            $table->string('fee_type');
            $table->string('term');
            $table->string('academic_year');
            $table->text('remarks')->nullable();
            $table->foreignId('received_by')->constrained('users');
            $table->string('status')->default('completed');
            $table->timestamps();
        });

        // Employees table
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->text('address');
            $table->string('position');
            $table->string('department');
            $table->decimal('base_salary', 10, 2);
            $table->decimal('allowances', 10, 2)->default(0);
            $table->date('joining_date');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('ifsc_code');
            $table->string('pan_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Salaries table
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('month');
            $table->year('year');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('allowances', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2);
            $table->date('payment_date');
            $table->string('payment_method');
            $table->string('transaction_id')->nullable();
            $table->string('status')->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('processed_by')->constrained('users');
            $table->timestamps();
        });

        // Staff loans table
        Schema::create('staff_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->integer('tenure_months');
            $table->decimal('monthly_installment', 10, 2);
            $table->decimal('total_payable', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2);
            $table->date('sanction_date');
            $table->date('first_installment_date');
            $table->string('purpose');
            $table->string('status')->default('active');
            $table->foreignId('approved_by')->constrained('users');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Staff overdrafts table
        Schema::create('staff_overdrafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->decimal('limit_amount', 10, 2);
            $table->decimal('used_amount', 10, 2)->default(0);
            $table->decimal('available_amount', 10, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->date('sanction_date');
            $table->date('expiry_date');
            $table->string('status')->default('active');
            $table->foreignId('approved_by')->constrained('users');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Expense categories table
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Expenses table
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('expense_categories');
            $table->string('expense_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->string('payment_method');
            $table->string('vendor_name')->nullable();
            $table->string('invoice_number')->nullable();
            $table->text('description');
            $table->string('receipt_path')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        // Asset categories table
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->integer('depreciation_rate');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Assets table
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->foreignId('category_id')->constrained('asset_categories');
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('current_value', 10, 2);
            $table->date('purchase_date');
            $table->string('supplier');
            $table->string('serial_number')->nullable();
            $table->string('location');
            $table->string('status')->default('active');
            $table->string('assigned_to')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Income categories table
        Schema::create('income_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Other incomes table
        Schema::create('other_incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('income_categories');
            $table->string('income_number')->unique();
            $table->string('source');
            $table->decimal('amount', 10, 2);
            $table->date('income_date');
            $table->string('payment_method');
            $table->string('reference_number')->nullable();
            $table->text('description');
            $table->string('receipt_path')->nullable();
            $table->foreignId('received_by')->constrained('users');
            $table->timestamps();
        });

        // Bank accounts table
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_name');
            $table->string('bank_name');
            $table->string('account_number')->unique();
            $table->string('ifsc_code');
            $table->decimal('current_balance', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Transactions table
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->string('type');
            $table->string('category');
            $table->string('reference_type');
            $table->unsignedBigInteger('reference_id');
            $table->decimal('amount', 10, 2);
            $table->date('transaction_date');
            $table->string('payment_method');
            $table->foreignId('bank_account_id')->nullable()->constrained();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('other_incomes');
        Schema::dropIfExists('income_categories');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_categories');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('staff_overdrafts');
        Schema::dropIfExists('staff_loans');
        Schema::dropIfExists('salaries');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('students');
        Schema::dropIfExists('users');
    }
};
