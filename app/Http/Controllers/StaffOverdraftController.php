<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\StaffOverdraft;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffOverdraftController extends Controller
{
    public function index()
    {
        $query = StaffOverdraft::with('employee', 'approver');
        if (auth()->user()?->role === 'teacher') {
            $query->where('employee_id', getCurrentEmployeeId() ?? 0);
        }
        $overdrafts = $query
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $totalOverdrafts = StaffOverdraft::sum('limit_amount');
        $totalUsed = StaffOverdraft::sum('used_amount');
        $totalAvailable = StaffOverdraft::sum('available_amount');
        $activeOverdrafts = StaffOverdraft::where('status', 'active')->count();
        $expiredOverdrafts = StaffOverdraft::where('status', 'expired')->count();
        
        return view('overdrafts.index', compact(
            'overdrafts', 'totalOverdrafts', 'totalUsed', 
            'totalAvailable', 'activeOverdrafts', 'expiredOverdrafts'
        ));
    }

    public function create()
    {
        $employees = auth()->user()?->role === 'teacher'
            ? Employee::whereKey(getCurrentEmployeeId() ?? 0)->get()
            : Employee::where('is_active', true)->orderBy('first_name')->get();
        return view('overdrafts.create', compact('employees'));
    }

    public function calculateOverdraft(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'limit_amount' => 'required|numeric|min:1000',
            'interest_rate' => 'required|numeric|min:0|max:30',
            'expiry_date' => 'required|date|after:today'
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        if (auth()->user()?->role === 'teacher') {
            abort_unless($employee->id === getCurrentEmployeeId(), 403);
        }
        
        // Check if employee has existing active overdraft
        $existingOverdraft = StaffOverdraft::where('employee_id', $employee->id)
            ->where('status', 'active')
            ->first();
        
        if ($existingOverdraft) {
            return response()->json([
                'error' => 'Employee already has an active overdraft facility.'
            ], 400);
        }
        
        // Calculate maximum allowed overdraft based on salary (e.g., 50% of annual salary)
        $monthlySalary = $employee->base_salary + $employee->allowances;
        $maxAllowedOverdraft = $monthlySalary * 3; // 3 months salary max
        
        $limitAmount = $request->limit_amount;
        
        if ($limitAmount > $maxAllowedOverdraft) {
            return response()->json([
                'warning' => true,
                'message' => "Requested overdraft limit (₦" . number_format($limitAmount, 2) . 
                           ") exceeds maximum allowed limit based on salary (₦" . number_format($maxAllowedOverdraft, 2) . ").",
                'max_allowed' => $maxAllowedOverdraft
            ]);
        }
        
        // Calculate monthly interest
        $monthlyInterestRate = $request->interest_rate / 100 / 12;
        $estimatedMonthlyInterest = $limitAmount * $monthlyInterestRate;
        
        return response()->json([
            'success' => true,
            'employee' => $employee,
            'limit_amount' => $limitAmount,
            'interest_rate' => $request->interest_rate,
            'expiry_date' => $request->expiry_date,
            'monthly_salary' => $monthlySalary,
            'max_allowed' => $maxAllowedOverdraft,
            'estimated_monthly_interest' => $estimatedMonthlyInterest
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'limit_amount' => 'required|numeric|min:1000',
            'used_amount' => 'nullable|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:30',
            'sanction_date' => 'required|date',
            'expiry_date' => 'required|date|after:sanction_date',
            'remarks' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Check for existing active overdraft
            $existingOverdraft = StaffOverdraft::where('employee_id', $request->employee_id)
                ->where('status', 'active')
                ->first();
            
            if ($existingOverdraft) {
                return back()->with('error', 'Employee already has an active overdraft facility.');
            }

            $usedAmount = $request->used_amount ?? 0;
            $availableAmount = $request->limit_amount - $usedAmount;

            $employeeId = auth()->user()?->role === 'teacher' ? getCurrentEmployeeId() : $request->employee_id;
            $overdraft = StaffOverdraft::create([
                'employee_id' => $employeeId,
                'limit_amount' => $request->limit_amount,
                'used_amount' => $usedAmount,
                'available_amount' => $availableAmount,
                'interest_rate' => $request->interest_rate,
                'sanction_date' => $request->sanction_date,
                'expiry_date' => $request->expiry_date,
                'status' => auth()->user()?->role === 'teacher' ? 'pending' : 'active',
                'approved_by' => auth()->user()?->role === 'teacher' ? null : Auth::id(),
                'remarks' => $request->remarks
            ]);

            DB::commit();

            return redirect()->route('staff-overdrafts.index')
                ->with('success', auth()->user()?->role === 'teacher' ? 'Overdraft application submitted for approval.' : 'Overdraft facility approved and created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating overdraft: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $overdraft = StaffOverdraft::with('employee', 'approver')->findOrFail($id);
        abort_unless(auth()->user()?->role !== 'teacher' || $overdraft->employee_id === getCurrentEmployeeId(), 403);
        
        // Calculate usage percentage
        $usagePercentage = ($overdraft->used_amount / $overdraft->limit_amount) * 100;
        
        // Calculate days remaining
        $today = now();
        $expiryDate = new \DateTime($overdraft->expiry_date);
        $daysRemaining = $today->diff($expiryDate)->days;
        if ($today > $expiryDate) {
            $daysRemaining = -$daysRemaining;
        }
        
        // Calculate monthly interest
        $monthlyInterestRate = $overdraft->interest_rate / 100 / 12;
        $monthlyInterest = $overdraft->used_amount * $monthlyInterestRate;
        
        return view('overdrafts.show', compact('overdraft', 'usagePercentage', 'daysRemaining', 'monthlyInterest'));
    }

    public function edit($id)
    {
        $overdraft = StaffOverdraft::findOrFail($id);
        $employees = Employee::where('is_active', true)->get();
        
        return view('overdrafts.edit', compact('overdraft', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $overdraft = StaffOverdraft::findOrFail($id);
        
        $request->validate([
            'limit_amount' => 'required|numeric|min:1000',
            'used_amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:30',
            'expiry_date' => 'required|date',
            'status' => 'required|in:active,expired,closed'
        ]);

        try {
            $availableAmount = $request->limit_amount - $request->used_amount;
            
            $overdraft->update([
                'limit_amount' => $request->limit_amount,
                'used_amount' => $request->used_amount,
                'available_amount' => $availableAmount,
                'interest_rate' => $request->interest_rate,
                'expiry_date' => $request->expiry_date,
                'status' => $request->status,
                'remarks' => $request->remarks ?? $overdraft->remarks
            ]);

            return redirect()->route('overdrafts.show', $overdraft->id)
                ->with('success', 'Overdraft updated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating overdraft: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $overdraft = StaffOverdraft::findOrFail($id);
            
            // Don't allow deletion if amount has been used
            if ($overdraft->used_amount > 0) {
                return back()->with('error', 'Cannot delete overdraft that has been utilized.');
            }
            
            $overdraft->delete();

            return redirect()->route('overdrafts.index')
                ->with('success', 'Overdraft record deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting overdraft: ' . $e->getMessage());
        }
    }

    public function recordUsage(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:draw,repay',
            'remarks' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            
            $overdraft = StaffOverdraft::findOrFail($id);
            
            if ($overdraft->status != 'active') {
                return response()->json(['error' => 'Overdraft facility is not active'], 400);
            }
            
            if ($request->type == 'draw') {
                $newUsedAmount = $overdraft->used_amount + $request->amount;
                
                if ($newUsedAmount > $overdraft->limit_amount) {
                    return response()->json(['error' => 'Amount exceeds available limit'], 400);
                }
                
                $overdraft->used_amount = $newUsedAmount;
                $overdraft->available_amount = $overdraft->limit_amount - $newUsedAmount;
                
                $message = "₦" . number_format($request->amount, 2) . " drawn from overdraft";
                
            } else {
                $newUsedAmount = $overdraft->used_amount - $request->amount;
                
                if ($newUsedAmount < 0) {
                    return response()->json(['error' => 'Repayment amount exceeds used amount'], 400);
                }
                
                $overdraft->used_amount = $newUsedAmount;
                $overdraft->available_amount = $overdraft->limit_amount - $newUsedAmount;
                
                $message = "₦" . number_format($request->amount, 2) . " repaid to overdraft";
            }
            
            $overdraft->save();
            
            // Here you would record the transaction in a separate table
            // For now, just update the overdraft record
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'used_amount' => $overdraft->used_amount,
                'available_amount' => $overdraft->available_amount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error recording transaction: ' . $e->getMessage()], 500);
        }
    }

    public function reports()
    {
        // Summary statistics
        $totalOverdrafts = StaffOverdraft::sum('limit_amount');
        $totalUtilized = StaffOverdraft::sum('used_amount');
        $totalAvailable = StaffOverdraft::sum('available_amount');
        $utilizationRate = $totalOverdrafts > 0 ? ($totalUtilized / $totalOverdrafts) * 100 : 0;
        
        // Overdrafts by department
        $overdraftsByDepartment = StaffOverdraft::join('employees', 'staff_overdrafts.employee_id', '=', 'employees.id')
            ->select('employees.department',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(staff_overdrafts.limit_amount) as total_limit'),
                DB::raw('SUM(staff_overdrafts.used_amount) as total_used'),
                DB::raw('SUM(staff_overdrafts.available_amount) as total_available'))
            ->groupBy('employees.department')
            ->get();
        
        // Top overdraft users
        $topUsers = StaffOverdraft::join('employees', 'staff_overdrafts.employee_id', '=', 'employees.id')
            ->select('employees.first_name', 'employees.last_name', 'employees.employee_id',
                'staff_overdrafts.limit_amount',
                'staff_overdrafts.used_amount',
                'staff_overdrafts.available_amount',
                'staff_overdrafts.interest_rate')
            ->orderBy('staff_overdrafts.used_amount', 'desc')
            ->limit(10)
            ->get();
        
        // Expiring soon (next 30 days)
        $expiringSoon = StaffOverdraft::where('status', 'active')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>=', now())
            ->count();
        
        // Expired overdrafts
        $expiredCount = StaffOverdraft::where('expiry_date', '<', now())
            ->where('status', 'active')
            ->count();
        
        return view('overdrafts.reports', compact(
            'totalOverdrafts', 'totalUtilized', 'totalAvailable', 'utilizationRate',
            'overdraftsByDepartment', 'topUsers', 'expiringSoon', 'expiredCount'
        ));
    }

    public function statement($id)
    {
        $overdraft = StaffOverdraft::with('employee', 'approver')->findOrFail($id);

        return view('overdrafts.statement', compact('overdraft'));
    }

    public function autoExpireOverdrafts()
    {
        $expired = StaffOverdraft::where('expiry_date', '<', now())
            ->where('status', 'active')
            ->update(['status' => 'expired']);
        
        return redirect()->route('overdrafts.index')
            ->with('success', $expired . ' overdraft facilities have been marked as expired.');
    }
}
