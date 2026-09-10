<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\StaffLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffLoanController extends Controller
{
    public function index()
    {
        $query = StaffLoan::with('employee', 'approver');
        if (auth()->user()?->role === 'teacher') {
            $query->where('employee_id', getCurrentEmployeeId() ?? 0);
        }
        $loans = $query
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $totalLoans = StaffLoan::sum('amount');
        $activeLoans = StaffLoan::where('status', 'active')->sum('remaining_amount');
        $totalInterest = StaffLoan::sum(DB::raw('total_payable - amount'));
        
        return view('staff-loans.index', compact('loans', 'totalLoans', 'activeLoans', 'totalInterest'));
    }

    public function create()
    {
        $employees = auth()->user()?->role === 'teacher'
            ? Employee::whereKey(getCurrentEmployeeId() ?? 0)->get()
            : Employee::where('is_active', true)->orderBy('first_name')->get();
        
        return view('staff-loans.create', compact('employees'));
    }

    public function calculateLoan(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:1000',
            'interest_rate' => 'required|numeric|min:0|max:30',
            'tenure_months' => 'required|integer|min:1|max:60',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        if (auth()->user()?->role === 'teacher') {
            abort_unless($employee->id === getCurrentEmployeeId(), 403);
        }
        
        // Check if employee has existing active loan
        $existingLoan = StaffLoan::where('employee_id', $employee->id)
            ->where('status', 'active')
            ->first();
        
        if ($existingLoan) {
            return response()->json([
                'error' => 'Employee already has an active loan. Please complete the existing loan first.'
            ], 400);
        }
        
        $amount = $request->amount;
        $interestRate = $request->interest_rate;
        $tenureMonths = $request->tenure_months;
        
        // Calculate simple interest
        $interest = ($amount * $interestRate * $tenureMonths) / 100;
        $totalPayable = $amount + $interest;
        $monthlyInstallment = $totalPayable / $tenureMonths;
        
        // Check if monthly installment exceeds 50% of salary
        $maxAllowedInstallment = ($employee->base_salary + $employee->allowances) * 0.5;
        
        if ($monthlyInstallment > $maxAllowedInstallment) {
            return response()->json([
                'warning' => true,
                'message' => "Monthly installment (₦" . number_format($monthlyInstallment, 2) . 
                           ") exceeds 50% of employee's salary (₦" . number_format($maxAllowedInstallment, 2) . 
                           "). Consider reducing loan amount or increasing tenure.",
                'monthly_installment' => $monthlyInstallment,
                'total_payable' => $totalPayable,
                'interest' => $interest,
                'max_allowed' => $maxAllowedInstallment
            ]);
        }
        
        return response()->json([
            'success' => true,
            'employee' => $employee,
            'amount' => $amount,
            'interest_rate' => $interestRate,
            'tenure_months' => $tenureMonths,
            'interest' => $interest,
            'total_payable' => $totalPayable,
            'monthly_installment' => $monthlyInstallment,
            'max_allowed' => $maxAllowedInstallment
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:1000',
            'interest_rate' => 'required|numeric|min:0|max:30',
            'tenure_months' => 'required|integer|min:1|max:60',
            'monthly_installment' => 'required|numeric',
            'total_payable' => 'required|numeric',
            'purpose' => 'required|string',
            'first_installment_date' => 'required|date',
            'remarks' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Check for existing active loan again
            $existingLoan = StaffLoan::where('employee_id', $request->employee_id)
                ->where('status', 'active')
                ->first();
            
            if ($existingLoan) {
                return back()->with('error', 'Employee already has an active loan.');
            }

            $employeeId = auth()->user()?->role === 'teacher' ? getCurrentEmployeeId() : $request->employee_id;
            $loan = StaffLoan::create([
                'employee_id' => $employeeId,
                'amount' => $request->amount,
                'interest_rate' => $request->interest_rate,
                'tenure_months' => $request->tenure_months,
                'monthly_installment' => $request->monthly_installment,
                'total_payable' => $request->total_payable,
                'paid_amount' => 0,
                'remaining_amount' => $request->total_payable,
                'sanction_date' => now(),
                'first_installment_date' => $request->first_installment_date,
                'purpose' => $request->purpose,
                'status' => auth()->user()?->role === 'teacher' ? 'pending' : 'active',
                'approved_by' => auth()->user()?->role === 'teacher' ? null : Auth::id(),
                'remarks' => $request->remarks
            ]);

            DB::commit();

            return redirect()->route('staff-loans.index')
                ->with('success', auth()->user()?->role === 'teacher' ? 'Loan application submitted for approval.' : 'Loan approved and disbursed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing loan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $loan = StaffLoan::with('employee', 'approver')->findOrFail($id);
        abort_unless(auth()->user()?->role !== 'teacher' || $loan->employee_id === getCurrentEmployeeId(), 403);
        
        // Calculate payment schedule
        $paymentSchedule = $this->calculatePaymentSchedule($loan);
        
        return view('staff-loans.show', compact('loan', 'paymentSchedule'));
    }

    public function edit($id)
    {
        $loan = StaffLoan::findOrFail($id);
        $employees = Employee::where('is_active', true)->get();
        
        return view('staff-loans.edit', compact('loan', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $loan = StaffLoan::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,active,completed,defaulted',
            'remarks' => 'nullable|string'
        ]);

        try {
            if ($request->status == 'completed') {
                $loan->remaining_amount = 0;
                $loan->paid_amount = $loan->total_payable;
            }
            
            $loan->status = $request->status;
            $loan->remarks = $request->remarks;
            $loan->save();

            return redirect()->route('staff-loans.index')
                ->with('success', 'Loan status updated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating loan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $loan = StaffLoan::findOrFail($id);
            
            // Don't allow deletion if any payments have been made
            if ($loan->paid_amount > 0) {
                return back()->with('error', 'Cannot delete loan that has received payments.');
            }
            
            $loan->delete();

            return redirect()->route('staff-loans.index')
                ->with('success', 'Loan record deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting loan: ' . $e->getMessage());
        }
    }

    public function recordPayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string'
        ]);

        try {
            DB::beginTransaction();
            
            $loan = StaffLoan::findOrFail($id);
            
            if ($loan->status != 'active') {
                return response()->json(['error' => 'Cannot make payment on a completed or defaulted loan'], 400);
            }
            
            $newPaidAmount = $loan->paid_amount + $request->amount;
            $newRemainingAmount = $loan->total_payable - $newPaidAmount;
            
            if ($newRemainingAmount < 0) {
                return response()->json(['error' => 'Payment amount exceeds remaining balance'], 400);
            }
            
            $loan->paid_amount = $newPaidAmount;
            $loan->remaining_amount = $newRemainingAmount;
            
            if ($newRemainingAmount <= 0) {
                $loan->status = 'completed';
            }
            
            $loan->save();
            
            // Record payment transaction (can be implemented with a LoanPayment model)
            // For now, we'll just update the loan record
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'remaining_balance' => $newRemainingAmount,
                'status' => $loan->status
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error recording payment: ' . $e->getMessage()], 500);
        }
    }

    public function reports()
    {
        // Loan summary statistics
        $totalLoans = StaffLoan::sum('amount');
        $totalInterest = StaffLoan::sum(DB::raw('total_payable - amount'));
        $activeLoanAmount = StaffLoan::where('status', 'active')->sum('remaining_amount');
        $completedLoans = StaffLoan::where('status', 'completed')->count();
        $activeLoans = StaffLoan::where('status', 'active')->count();
        $defaultedLoans = StaffLoan::where('status', 'defaulted')->count();
        
        // Loans by department
        $loansByDepartment = StaffLoan::join('employees', 'staff_loans.employee_id', '=', 'employees.id')
            ->select('employees.department', 
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(staff_loans.amount) as total_amount'),
                DB::raw('SUM(staff_loans.remaining_amount) as remaining_amount'))
            ->groupBy('employees.department')
            ->get();
        
        // Monthly loan disbursements
        $monthlyDisbursements = StaffLoan::select(
                DB::raw('strftime("%Y-%m", sanction_date) as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy(DB::raw('strftime("%Y-%m", sanction_date)'))
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
        
        // Top borrowers
        $topBorrowers = StaffLoan::join('employees', 'staff_loans.employee_id', '=', 'employees.id')
            ->select('employees.first_name', 'employees.last_name', 'employees.employee_id',
                DB::raw('SUM(staff_loans.amount) as total_borrowed'),
                DB::raw('SUM(staff_loans.remaining_amount) as total_remaining'))
            ->groupBy('employees.id')
            ->orderBy('total_borrowed', 'desc')
            ->limit(10)
            ->get();
        
        return view('staff-loans.reports', compact(
            'totalLoans', 'totalInterest', 'activeLoanAmount', 
            'completedLoans', 'activeLoans', 'defaultedLoans',
            'loansByDepartment', 'monthlyDisbursements', 'topBorrowers'
        ));
    }

    private function calculatePaymentSchedule($loan)
    {
        $schedule = [];
        $remaining = $loan->total_payable;
        $paid = $loan->paid_amount;
        $startDate = new \DateTime($loan->first_installment_date);
        
        for ($i = 1; $i <= $loan->tenure_months; $i++) {
            $dueDate = clone $startDate;
            $dueDate->modify("+{$i} months");
            
            $isPaid = $paid >= ($loan->monthly_installment * $i);
            $amountDue = $loan->monthly_installment;
            
            if ($i == $loan->tenure_months && $remaining > 0) {
                $amountDue = $remaining;
            }
            
            $schedule[] = [
                'installment_no' => $i,
                'due_date' => $dueDate->format('Y-m-d'),
                'amount_due' => $amountDue,
                'status' => $isPaid ? 'Paid' : 'Pending'
            ];
        }
        
        return $schedule;
    }
}
