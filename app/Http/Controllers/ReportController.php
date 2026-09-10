<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\FeePayment;
use App\Models\Employee;
use App\Models\Salary;
use App\Models\Expense;
use App\Models\OtherIncome;
use App\Models\StaffLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // Financial Reports Dashboard
    public function index()
    {
        // Get current year and month
        $currentYear = date('Y');
        $currentMonth = date('m');
        
        // Monthly data for charts
        $monthlyData = $this->getMonthlyData($currentYear);
        
        // Yearly comparison
        $yearlyData = $this->getYearlyData();
        
        // Key metrics
        $metrics = $this->getKeyMetrics();
        
        return view('reports.index', compact('monthlyData', 'yearlyData', 'metrics', 'currentYear'));
    }

    // Fee Collection Report
    public function feeCollection(Request $request)
    {
        $year = $request->year ?? date('Y');
        $month = $request->month ?? null;
        $class = $request->class ?? null;
        
        // Fee collection summary
        $feeSummary = $this->getFeeCollectionSummary($year, $month, $class);
        
        // Monthly fee collection trend
        $monthlyTrend = $this->getMonthlyFeeTrend($year);
        
        // Class-wise collection
        $classWiseCollection = $this->getClassWiseCollection($year);
        
        // Payment method breakdown
        $paymentMethods = $this->getPaymentMethodBreakdown($year, $month);
        
        // Recent transactions
        $recentTransactions = FeePayment::with('student')
            ->when($year, function($q) use ($year) {
                return $q->whereYear('payment_date', $year);
            })
            ->when($month, function($q) use ($month) {
                return $q->whereMonth('payment_date', $month);
            })
            ->orderBy('payment_date', 'desc')
            ->limit(50)
            ->get();
        
        // Classes list for filter
        $classes = Student::distinct()->pluck('class');
        
        return view('reports.fee-collection', compact(
            'feeSummary', 'monthlyTrend', 'classWiseCollection',
            'paymentMethods', 'recentTransactions', 'year', 'month', 'class', 'classes'
        ));
    }

    // Salary Report
    public function salaryReport(Request $request)
    {
        $year = $request->year ?? date('Y');
        $month = $request->month ?? null;
        $department = $request->department ?? null;
        
        // Salary summary
        $salarySummary = $this->getSalarySummary($year, $month, $department);
        
        // Monthly salary trend
        $monthlyTrend = $this->getMonthlySalaryTrend($year);
        
        // Department-wise salary
        $departmentWise = $this->getDepartmentWiseSalary($year);
        
        // Top earners
        $topEarners = $this->getTopEarners($year);
        
        // Recent salary payments
        $recentPayments = Salary::with('employee')
            ->when($year, function($q) use ($year) {
                return $q->whereYear('payment_date', $year);
            })
            ->when($month, function($q) use ($month) {
                return $q->whereMonth('payment_date', $month);
            })
            ->when($department, function($q) use ($department) {
                return $q->whereHas('employee', function($sub) use ($department) {
                    $sub->where('department', $department);
                });
            })
            ->orderBy('payment_date', 'desc')
            ->limit(50)
            ->get();
        
        // Departments for filter
        $departments = Employee::distinct()->pluck('department');
        
        return view('reports.salary', compact(
            'salarySummary', 'monthlyTrend', 'departmentWise',
            'topEarners', 'recentPayments', 'year', 'month', 'department', 'departments'
        ));
    }

    // Profit & Loss Report
    public function profitLoss(Request $request)
    {
        $year = $request->year ?? date('Y');
        $month = $request->month ?? null;
        
        // Income breakdown
        $incomeBreakdown = $this->getIncomeBreakdown($year, $month);
        
        // Expense breakdown
        $expenseBreakdown = $this->getExpenseBreakdown($year, $month);
        
        // Monthly P&L
        $monthlyPL = $this->getMonthlyProfitLoss($year);
        
        // Yearly comparison
        $yearlyComparison = $this->getYearlyProfitLoss();
        
        // Summary
        $totalIncome = $incomeBreakdown->sum('total');
        $totalExpense = $expenseBreakdown->sum('total');
        $netProfit = $totalIncome - $totalExpense;
        $profitMargin = $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0;
        
        return view('reports.profit-loss', compact(
            'incomeBreakdown', 'expenseBreakdown', 'monthlyPL',
            'yearlyComparison', 'totalIncome', 'totalExpense',
            'netProfit', 'profitMargin', 'year', 'month'
        ));
    }

    // Export Functions
    public function exportFeeReport(Request $request)
    {
        $data = $this->getFeeCollectionSummary($request->year, $request->month, $request->class);
        
        $pdf = Pdf::loadView('reports.exports.fee-pdf', compact('data'));
        return $pdf->download('fee-collection-report.pdf');
    }

    public function exportSalaryReport(Request $request)
    {
        $data = $this->getSalarySummary($request->year, $request->month, $request->department);
        
        $pdf = Pdf::loadView('reports.exports.salary-pdf', compact('data'));
        return $pdf->download('salary-report.pdf');
    }

    public function exportProfitLoss(Request $request)
    {
        $data = [
            'income' => $this->getIncomeBreakdown($request->year, $request->month),
            'expense' => $this->getExpenseBreakdown($request->year, $request->month),
            'year' => $request->year,
            'month' => $request->month
        ];
        
        $pdf = Pdf::loadView('reports.exports.pl-pdf', compact('data'));
        return $pdf->download('profit-loss-report.pdf');
    }

    // Private Helper Methods
    private function getMonthlyData($year)
    {
        $months = [];
        $income = [];
        $expense = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('F', mktime(0, 0, 0, $i, 1));
            
            $monthIncome = OtherIncome::whereYear('income_date', $year)
                ->whereMonth('income_date', $i)
                ->sum('amount');
            
            $feeIncome = FeePayment::whereYear('payment_date', $year)
                ->whereMonth('payment_date', $i)
                ->where('payment_status', 'success')
                ->sum('amount');
            
            $totalIncome = $monthIncome + $feeIncome;
            
            $monthExpense = Expense::whereYear('expense_date', $year)
                ->whereMonth('expense_date', $i)
                ->where('status', 'approved')
                ->sum('amount');
            
            $salaryExpense = Salary::whereYear('payment_date', $year)
                ->whereMonth('payment_date', $i)
                ->where('status', 'paid')
                ->sum('net_salary');
            
            $totalExpense = $monthExpense + $salaryExpense;
            
            $income[] = $totalIncome;
            $expense[] = $totalExpense;
        }
        
        return [
            'months' => $months,
            'income' => $income,
            'expense' => $expense
        ];
    }

    private function getYearlyData()
    {
        $years = range(date('Y') - 2, date('Y'));
        $income = [];
        $expense = [];
        
        foreach ($years as $year) {
            $yearIncome = OtherIncome::whereYear('income_date', $year)->sum('amount');
            $yearFees = FeePayment::whereYear('payment_date', $year)->where('payment_status', 'success')->sum('amount');
            
            $yearExpense = Expense::whereYear('expense_date', $year)->where('status', 'approved')->sum('amount');
            $yearSalary = Salary::whereYear('payment_date', $year)->where('status', 'paid')->sum('net_salary');
            
            $income[] = $yearIncome + $yearFees;
            $expense[] = $yearExpense + $yearSalary;
        }
        
        return [
            'years' => $years,
            'income' => $income,
            'expense' => $expense
        ];
    }

    private function getKeyMetrics()
    {
        $currentYear = date('Y');
        $currentMonth = date('m');
        
        return [
            'total_students' => Student::count(),
            'total_employees' => Employee::count(),
            'total_fees_collected' => FeePayment::whereYear('payment_date', $currentYear)
                ->where('payment_status', 'success')
                ->sum('amount'),
            'total_salaries' => Salary::whereYear('payment_date', $currentYear)
                ->where('status', 'paid')
                ->sum('net_salary'),
            'total_expenses' => Expense::whereYear('expense_date', $currentYear)
                ->where('status', 'approved')
                ->sum('amount'),
            'total_other_income' => OtherIncome::whereYear('income_date', $currentYear)->sum('amount'),
            'monthly_fees' => FeePayment::whereYear('payment_date', $currentYear)
                ->whereMonth('payment_date', $currentMonth)
                ->where('payment_status', 'success')
                ->sum('amount'),
            'outstanding_fees' => Student::sum('due_fees'),
            'active_loans' => StaffLoan::where('status', 'active')->sum('remaining_amount')
        ];
    }

    private function getFeeCollectionSummary($year, $month = null, $class = null)
    {
        $query = FeePayment::with('student')
            ->whereYear('payment_date', $year)
            ->where('payment_status', 'success');
        
        if ($month) {
            $query->whereMonth('payment_date', $month);
        }
        
        if ($class) {
            $query->whereHas('student', function($q) use ($class) {
                $q->where('class', $class);
            });
        }
        
        return [
            'total_collected' => $query->sum('amount'),
            'total_transactions' => $query->count(),
            'average_amount' => $query->avg('amount') ?? 0,
            'students_paid' => $query->distinct('student_id')->count('student_id')
        ];
    }

    private function getMonthlyFeeTrend($year)
    {
        $trend = [];
        for ($i = 1; $i <= 12; $i++) {
            $trend[] = [
                'month' => date('F', mktime(0, 0, 0, $i, 1)),
                'amount' => FeePayment::whereYear('payment_date', $year)
                    ->whereMonth('payment_date', $i)
                    ->where('payment_status', 'success')
                    ->sum('amount'),
                'count' => FeePayment::whereYear('payment_date', $year)
                    ->whereMonth('payment_date', $i)
                    ->where('payment_status', 'success')
                    ->count()
            ];
        }
        return $trend;
    }

    private function getClassWiseCollection($year)
    {
        return FeePayment::join('students', 'fee_payments.student_id', '=', 'students.id')
            ->select('students.class', 
                DB::raw('SUM(fee_payments.amount) as total'),
                DB::raw('COUNT(DISTINCT fee_payments.student_id) as students_paid'),
                DB::raw('COUNT(*) as transactions'))
            ->whereYear('fee_payments.payment_date', $year)
            ->where('fee_payments.payment_status', 'success')
            ->groupBy('students.class')
            ->orderBy('total', 'desc')
            ->get();
    }

    private function getPaymentMethodBreakdown($year, $month = null)
    {
        $query = FeePayment::select('payment_method', 
            DB::raw('SUM(amount) as total'),
            DB::raw('COUNT(*) as count'))
            ->whereYear('payment_date', $year)
            ->where('payment_status', 'success');
        
        if ($month) {
            $query->whereMonth('payment_date', $month);
        }
        
        return $query->groupBy('payment_method')->get();
    }

    private function getSalarySummary($year, $month = null, $department = null)
    {
        $query = Salary::with('employee')
            ->whereYear('payment_date', $year)
            ->where('status', 'paid');
        
        if ($month) {
            $query->whereMonth('payment_date', $month);
        }
        
        if ($department) {
            $query->whereHas('employee', function($q) use ($department) {
                $q->where('department', $department);
            });
        }
        
        return [
            'total_salaries' => $query->sum('net_salary'),
            'total_employees' => $query->distinct('employee_id')->count('employee_id'),
            'average_salary' => $query->avg('net_salary') ?? 0,
            'total_deductions' => $query->sum('deductions')
        ];
    }

    private function getMonthlySalaryTrend($year)
    {
        $trend = [];
        for ($i = 1; $i <= 12; $i++) {
            $trend[] = [
                'month' => date('F', mktime(0, 0, 0, $i, 1)),
                'amount' => Salary::whereYear('payment_date', $year)
                    ->whereMonth('payment_date', $i)
                    ->where('status', 'paid')
                    ->sum('net_salary'),
                'count' => Salary::whereYear('payment_date', $year)
                    ->whereMonth('payment_date', $i)
                    ->where('status', 'paid')
                    ->count()
            ];
        }
        return $trend;
    }

    private function getDepartmentWiseSalary($year)
    {
        return Salary::join('employees', 'salaries.employee_id', '=', 'employees.id')
            ->select('employees.department',
                DB::raw('SUM(salaries.net_salary) as total'),
                DB::raw('COUNT(DISTINCT salaries.employee_id) as employees'),
                DB::raw('AVG(salaries.net_salary) as average'))
            ->whereYear('salaries.payment_date', $year)
            ->where('salaries.status', 'paid')
            ->groupBy('employees.department')
            ->orderBy('total', 'desc')
            ->get();
    }

    private function getTopEarners($year)
    {
        return Salary::join('employees', 'salaries.employee_id', '=', 'employees.id')
            ->select('employees.first_name', 'employees.last_name', 'employees.employee_id',
                'employees.position', 'employees.department',
                DB::raw('SUM(salaries.net_salary) as total_earned'))
            ->whereYear('salaries.payment_date', $year)
            ->where('salaries.status', 'paid')
            ->groupBy('salaries.employee_id')
            ->orderBy('total_earned', 'desc')
            ->limit(10)
            ->get();
    }

    private function getIncomeBreakdown($year, $month = null)
    {
        $query = OtherIncome::select('source', DB::raw('SUM(amount) as total'))
            ->whereYear('income_date', $year);
        
        if ($month) {
            $query->whereMonth('income_date', $month);
        }
        
        $otherIncome = $query->groupBy('source')->get();
        
        $feeIncome = FeePayment::whereYear('payment_date', $year)
            ->where('payment_status', 'success');
        
        if ($month) {
            $feeIncome->whereMonth('payment_date', $month);
        }
        
        $totalFees = $feeIncome->sum('amount');
        
        $result = [
            (object)['source' => 'Student Fees', 'total' => $totalFees, 'type' => 'income']
        ];
        
        foreach ($otherIncome as $income) {
            $result[] = (object)[
                'source' => $income->source,
                'total' => $income->total,
                'type' => 'income'
            ];
        }
        
        return collect($result);
    }

    private function getExpenseBreakdown($year, $month = null)
    {
        $query = Expense::with('category')
            ->whereYear('expense_date', $year)
            ->where('status', 'approved');
        
        if ($month) {
            $query->whereMonth('expense_date', $month);
        }
        
        $expenses = $query->get()->groupBy('category.name')
            ->map(function($items) {
                return $items->sum('amount');
            });
        
        $salaryExpense = Salary::whereYear('payment_date', $year)
            ->where('status', 'paid');
        
        if ($month) {
            $salaryExpense->whereMonth('payment_date', $month);
        }
        
        $totalSalary = $salaryExpense->sum('net_salary');
        
        $result = [
            (object)['category' => 'Salaries & Wages', 'total' => $totalSalary, 'type' => 'expense']
        ];
        
        foreach ($expenses as $category => $total) {
            $result[] = (object)[
                'category' => $category,
                'total' => $total,
                'type' => 'expense'
            ];
        }
        
        return collect($result);
    }

    private function getMonthlyProfitLoss($year)
    {
        $pl = [];
        for ($i = 1; $i <= 12; $i++) {
            $fees = FeePayment::whereYear('payment_date', $year)
                ->whereMonth('payment_date', $i)
                ->where('payment_status', 'success')
                ->sum('amount');
            
            $otherIncome = OtherIncome::whereYear('income_date', $year)
                ->whereMonth('income_date', $i)
                ->sum('amount');
            
            $totalIncome = $fees + $otherIncome;
            
            $expenses = Expense::whereYear('expense_date', $year)
                ->whereMonth('expense_date', $i)
                ->where('status', 'approved')
                ->sum('amount');
            
            $salaries = Salary::whereYear('payment_date', $year)
                ->whereMonth('payment_date', $i)
                ->where('status', 'paid')
                ->sum('net_salary');
            
            $totalExpense = $expenses + $salaries;
            
            $pl[] = [
                'month' => date('F', mktime(0, 0, 0, $i, 1)),
                'income' => $totalIncome,
                'expense' => $totalExpense,
                'profit' => $totalIncome - $totalExpense
            ];
        }
        return $pl;
    }

    private function getYearlyProfitLoss()
    {
        $years = range(date('Y') - 2, date('Y'));
        $pl = [];
        
        foreach ($years as $year) {
            $fees = FeePayment::whereYear('payment_date', $year)
                ->where('payment_status', 'success')
                ->sum('amount');
            
            $otherIncome = OtherIncome::whereYear('income_date', $year)->sum('amount');
            $totalIncome = $fees + $otherIncome;
            
            $expenses = Expense::whereYear('expense_date', $year)
                ->where('status', 'approved')
                ->sum('amount');
            
            $salaries = Salary::whereYear('payment_date', $year)
                ->where('status', 'paid')
                ->sum('net_salary');
            
            $totalExpense = $expenses + $salaries;
            
            $pl[] = [
                'year' => $year,
                'income' => $totalIncome,
                'expense' => $totalExpense,
                'profit' => $totalIncome - $totalExpense
            ];
        }
        
        return $pl;
    }
}