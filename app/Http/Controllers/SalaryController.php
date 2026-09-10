<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Salary;
use App\Models\StaffLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    public function index()
    {
        $salaries = Salary::with('employee')->orderBy('payment_date', 'desc')->paginate(15);
        $totalSalaries = Salary::sum('net_salary');
        $monthlySalaries = Salary::whereMonth('payment_date', date('m'))
            ->whereYear('payment_date', date('Y'))
            ->sum('net_salary');
        $paidCount = Salary::where('status', 'paid')->count();

        return view('salaries.index', compact('salaries', 'totalSalaries', 'monthlySalaries', 'paidCount'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->orderBy('first_name')->get();
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December',
        ];
        $years = range(date('Y') - 2, date('Y') + 1);

        return view('salaries.create', compact('employees', 'months', 'years'));
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|string',
            'year' => 'required|integer',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $basicSalary = (float) $employee->base_salary;
        $allowances = (float) $employee->allowances;
        $grossSalary = $basicSalary + $allowances;
        $loanDeduction = (float) StaffLoan::where('employee_id', $employee->id)
            ->where('status', 'active')
            ->sum('monthly_installment');
        $taxDeduction = $grossSalary * 0.05;
        $pensionDeduction = $grossSalary * 0.075;
        $otherDeductions = 0;
        $totalDeductions = $loanDeduction + $taxDeduction + $pensionDeduction + $otherDeductions;
        $netSalary = max(0, $grossSalary - $totalDeductions);

        return response()->json([
            'employee' => $employee,
            'basic_salary' => $basicSalary,
            'allowances' => $allowances,
            'gross_salary' => $grossSalary,
            'loan_deduction' => $loanDeduction,
            'tax_deduction' => $taxDeduction,
            'pension_deduction' => $pensionDeduction,
            'other_deductions' => $otherDeductions,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|string',
            'year' => 'required|integer',
            'basic_salary' => 'required|numeric',
            'allowances' => 'required|numeric',
            'deductions' => 'required|numeric',
            'net_salary' => 'required|numeric',
            'payment_method' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        $salary = Salary::create([
            'employee_id' => $request->employee_id,
            'month' => $request->month,
            'year' => $request->year,
            'basic_salary' => $request->basic_salary,
            'allowances' => $request->allowances,
            'deductions' => $request->deductions,
            'net_salary' => $request->net_salary,
            'payment_date' => now(),
            'payment_method' => $request->payment_method,
            'status' => 'paid',
            'remarks' => $request->remarks,
            'processed_by' => Auth::id(),
        ]);

        return redirect()->route('salaries.show', $salary->id)
            ->with('success', 'Salary processed successfully!');
    }

    public function show($id)
    {
        $salary = Salary::with('employee')->findOrFail($id);
        return view('salaries.show', compact('salary'));
    }

    public function history($employeeId = null)
    {
        $query = Salary::with('employee')->orderBy('payment_date', 'desc');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $salaries = $query->paginate(15);
        $employees = Employee::where('is_active', true)->orderBy('first_name')->get();

        return view('salaries.history', compact('salaries', 'employees', 'employeeId'));
    }

    public function reports()
    {
        $monthlyStats = Salary::select(
                DB::raw('month as month'),
                DB::raw('SUM(net_salary) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->where('year', date('Y'))
            ->groupBy('month')
            ->get();

        $departmentStats = Salary::join('employees', 'salaries.employee_id', '=', 'employees.id')
            ->select(
                'employees.department',
                DB::raw('SUM(salaries.net_salary) as total'),
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(salaries.net_salary) as average')
            )
            ->groupBy('employees.department')
            ->orderBy('total', 'desc')
            ->get();

        $yearlyStats = Salary::select(
                'year',
                DB::raw('SUM(net_salary) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        return view('salaries.reports', compact('monthlyStats', 'departmentStats', 'yearlyStats'));
    }

    public function payslip($id)
    {
        $salary = Salary::with('employee')->findOrFail($id);
        return view('salaries.payslip', compact('salary'));
    }

    public function destroy($id)
    {
        Salary::findOrFail($id)->delete();

        return redirect()->route('salaries.index')
            ->with('success', 'Salary record deleted successfully!');
    }
}
