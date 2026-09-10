<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $role = strtolower((string) $user->role);

        // Redirect based on role
        switch ($role) {
            case 'super_admin':
            case 'super-admin':
            case 'admin':
                return app(AdminDashboardController::class)->index();
            case 'teacher':
                return app(TeacherDashboardController::class)->index();
            case 'student':
                return app(StudentDashboardController::class)->index();
            case 'accountant':
                return app(AccountantDashboardController::class)->index();
            case 'parent':
                return app(ParentDashboardController::class)->index();
            case 'frontdesk':
                return app(FrontdeskDashboardController::class)->index();
            default:
                return $this->defaultDashboard();
        }
    }

    /**
     * Default dashboard for users without a specific role
     */
    private function defaultDashboard()
    {
        $now = Carbon::now();

        $totalStudents = $this->countTable('students');
        $totalEmployees = $this->countTable('employees');
        $todayFees = $this->sumForDate('fee_payments', 'payment_date', $this->paymentAmountColumn(), Carbon::today());
        $monthFees = $this->sumForMonth('fee_payments', 'payment_date', $this->paymentAmountColumn(), $now);
        $monthExpenses = $this->sumForMonth('expenses', 'expense_date', 'amount', $now);
        $monthSalaries = $this->sumForMonth('salaries', 'payment_date', 'net_salary', $now);
        $monthIncome = $this->sumForMonth('other_incomes', 'income_date', 'amount', $now);
        $activeLoans = $this->sumWhere('staff_loans', 'remaining_amount', 'status', 'active');
        $totalAssets = $this->sumWhere('assets', 'current_value', 'status', 'active');
        $bankBalance = $this->sumTable('bank_accounts', 'current_balance');
        
        // Recent data
        $recentPayments = $this->recentPayments();
        $recentExpenses = $this->recentExpenses();
        
        // Chart data for last 6 months
        $chartData = $this->getChartData();
        
        return view('dashboard', compact(
            'totalStudents', 'totalEmployees', 'todayFees', 
            'monthFees', 'monthExpenses', 'monthIncome', 'monthSalaries',
            'activeLoans', 'totalAssets', 'bankBalance',
            'recentPayments', 'recentExpenses', 'chartData'
        ));
    }
    
    /**
     * Get chart data for the last 6 months
     */
    private function getChartData()
    {
        $months = collect(range(5, 0))->map(function($i) {
            return Carbon::now()->subMonths($i)->format('M Y');
        });
        
        $income = $months->map(function($month) {
            $date = Carbon::createFromFormat('M Y', $month);
            $fees = $this->sumForMonth('fee_payments', 'payment_date', $this->paymentAmountColumn(), $date);
            $otherIncome = $this->sumForMonth('other_incomes', 'income_date', 'amount', $date);
            return $fees + $otherIncome;
        });
        
        $expense = $months->map(function($month) {
            $date = Carbon::createFromFormat('M Y', $month);
            $expenses = $this->sumForMonth('expenses', 'expense_date', 'amount', $date);
            $salaries = $this->sumForMonth('salaries', 'payment_date', 'net_salary', $date);
            return $expenses + $salaries;
        });
        
        return [
            'months' => $months,
            'income' => $income,
            'expense' => $expense
        ];
    }

    /**
     * Count records in a table
     */
    private function countTable(string $table): int
    {
        return Schema::hasTable($table) ? DB::table($table)->count() : 0;
    }

    /**
     * Sum a column in a table
     */
    private function sumTable(string $table, string $column): float
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
            return 0;
        }

        return (float) DB::table($table)->sum($column);
    }

    /**
     * Sum a column with a where condition
     */
    private function sumWhere(string $table, string $sumColumn, string $whereColumn, mixed $value): float
    {
        if (
            !Schema::hasTable($table) ||
            !Schema::hasColumn($table, $sumColumn) ||
            !Schema::hasColumn($table, $whereColumn)
        ) {
            return 0;
        }

        return (float) DB::table($table)->where($whereColumn, $value)->sum($sumColumn);
    }

    /**
     * Sum for a specific date
     */
    private function sumForDate(string $table, string $dateColumn, string $sumColumn, Carbon $date): float
    {
        if (
            !Schema::hasTable($table) ||
            !Schema::hasColumn($table, $dateColumn) ||
            !Schema::hasColumn($table, $sumColumn)
        ) {
            return 0;
        }

        return (float) DB::table($table)->whereDate($dateColumn, $date)->sum($sumColumn);
    }

    /**
     * Sum for a specific month
     */
    private function sumForMonth(string $table, string $dateColumn, string $sumColumn, Carbon $date): float
    {
        if (
            !Schema::hasTable($table) ||
            !Schema::hasColumn($table, $dateColumn) ||
            !Schema::hasColumn($table, $sumColumn)
        ) {
            return 0;
        }

        return (float) DB::table($table)
            ->whereYear($dateColumn, $date->year)
            ->whereMonth($dateColumn, $date->month)
            ->sum($sumColumn);
    }

    /**
     * Get the payment amount column name
     */
    private function paymentAmountColumn(): string
    {
        if (Schema::hasTable('fee_payments') && Schema::hasColumn('fee_payments', 'amount_paid')) {
            return 'amount_paid';
        }

        return 'amount';
    }

    /**
     * Get recent payments
     */
    private function recentPayments()
    {
        if (!Schema::hasTable('fee_payments') || !Schema::hasTable('students')) {
            return collect();
        }

        return DB::table('fee_payments')
            ->join('students', 'fee_payments.student_id', '=', 'students.id')
            ->select('fee_payments.*', 'students.first_name', 'students.last_name')
            ->orderBy('fee_payments.created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get recent expenses
     */
    private function recentExpenses()
    {
        if (!Schema::hasTable('expenses') || !Schema::hasTable('expense_categories')) {
            return collect();
        }

        return DB::table('expenses')
            ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->select('expenses.*', 'expense_categories.name as category_name')
            ->orderBy('expenses.created_at', 'desc')
            ->limit(5)
            ->get();
    }
}