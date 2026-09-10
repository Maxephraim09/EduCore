<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Salary;
use App\Models\StaffLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('created_at', 'desc')->paginate(15);
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('is_active', true)->count();
        $totalSalary = Employee::sum('base_salary');
        
        return view('employees.index', compact('employees', 'totalEmployees', 'activeEmployees', 'totalSalary'));
    }

    public function create()
    {
        $departments = $this->getDepartments();
        $positions = $this->getPositions();
        
        return view('employees.create', compact('departments', 'positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|unique:employees',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'position' => 'required|string',
            'department' => 'required|string',
            'base_salary' => 'required|numeric|min:0',
            'allowances' => 'numeric|min:0',
            'joining_date' => 'required|date',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'ifsc_code' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $employee = Employee::create([
                'employee_id' => $request->employee_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'position' => $request->position,
                'department' => $request->department,
                'base_salary' => $request->base_salary,
                'allowances' => $request->allowances ?? 0,
                'joining_date' => $request->joining_date,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'pan_number' => $request->pan_number,
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->route('employees.index')
                ->with('success', 'Employee added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error adding employee: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        $salaries = Salary::where('employee_id', $id)->orderBy('created_at', 'desc')->limit(6)->get();
        $loans = StaffLoan::where('employee_id', $id)->orderBy('created_at', 'desc')->get();
        $totalPaidSalary = Salary::where('employee_id', $id)->sum('net_salary');
        $totalLoanBalance = StaffLoan::where('employee_id', $id)->where('status', 'active')->sum('remaining_amount');
        
        return view('employees.show', compact('employee', 'salaries', 'loans', 'totalPaidSalary', 'totalLoanBalance'));
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $departments = $this->getDepartments();
        $positions = $this->getPositions();
        
        return view('employees.edit', compact('employee', 'departments', 'positions'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        
        $request->validate([
            'employee_id' => 'required|unique:employees,employee_id,' . $id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'position' => 'required|string',
            'department' => 'required|string',
            'base_salary' => 'required|numeric|min:0',
            'allowances' => 'numeric|min:0',
            'joining_date' => 'required|date',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'ifsc_code' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $employee->update($request->all());

            DB::commit();

            return redirect()->route('employees.show', $employee->id)
                ->with('success', 'Employee updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating employee: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            
            // Check if employee has any salary records
            if (Salary::where('employee_id', $id)->count() > 0) {
                return back()->with('error', 'Cannot delete employee with salary records. Consider marking as inactive instead.');
            }
            
            $employee->delete();

            return redirect()->route('employees.index')
                ->with('success', 'Employee deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting employee: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->is_active = !$employee->is_active;
        $employee->save();

        $status = $employee->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('employees.index')
            ->with('success', "Employee {$status} successfully!");
    }

    public function attendance()
    {
        $employees = Employee::where('is_active', true)->orderBy('first_name')->get();
        $today = date('Y-m-d');
        $currentMonth = date('m');
        $currentYear = date('Y');
        
        // Get attendance summary for current month
        $attendanceSummary = $this->getAttendanceSummary($currentMonth, $currentYear);
        
        return view('employees.attendance', compact('employees', 'today', 'currentMonth', 'currentYear', 'attendanceSummary'));
    }

    public function recordAttendance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,half-day,holiday',
            'check_in_time' => 'nullable',
            'check_out_time' => 'nullable',
            'remarks' => 'nullable|string'
        ]);

        try {
            // Store attendance in database (assuming you have an attendances table)
            // For now, we'll create an array and store in session or database
            $attendance = session('attendance', []);
            $key = $request->employee_id . '_' . $request->date;
            
            $attendance[$key] = [
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'status' => $request->status,
                'check_in_time' => $request->check_in_time,
                'check_out_time' => $request->check_out_time,
                'remarks' => $request->remarks,
                'recorded_at' => now()
            ];
            
            session(['attendance' => $attendance]);

            return response()->json(['success' => true, 'message' => 'Attendance recorded successfully!']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error recording attendance: ' . $e->getMessage()]);
        }
    }

    public function getAttendanceReport(Request $request)
    {
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');
        
        $attendanceReport = $this->getAttendanceSummary($month, $year);
        
        return response()->json($attendanceReport);
    }

    public function bulkAttendance(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.employee_id' => 'required|exists:employees,id',
            'attendances.*.status' => 'required|in:present,absent,late,half-day,holiday'
        ]);

        try {
            $attendance = session('attendance', []);
            
            foreach ($request->attendances as $att) {
                $key = $att['employee_id'] . '_' . $request->date;
                $attendance[$key] = [
                    'employee_id' => $att['employee_id'],
                    'date' => $request->date,
                    'status' => $att['status'],
                    'recorded_at' => now()
                ];
            }
            
            session(['attendance' => $attendance]);

            return response()->json(['success' => true, 'message' => 'Bulk attendance recorded successfully!']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error recording attendance: ' . $e->getMessage()]);
        }
    }

    private function getDepartments()
    {
        return [
            'Administration', 'Academic', 'Accounts', 'Human Resources',
            'IT Department', 'Maintenance', 'Transport', 'Library',
            'Sports', 'Medical', 'Security', 'Catering'
        ];
    }

    private function getPositions()
    {
        return [
            'Principal', 'Vice Principal', 'Head of Department', 'Senior Teacher',
            'Teacher', 'Assistant Teacher', 'Accountant', 'Cashier',
            'IT Officer', 'Librarian', 'Nurse', 'Driver', 'Security Guard',
            'Cleaner', 'Administrative Officer', 'HR Manager', 'Store Keeper'
        ];
    }

    private function getAttendanceSummary($month, $year)
    {
        // This is a placeholder - implement actual database query when attendances table exists
        $attendance = session('attendance', []);
        $summary = [];
        
        foreach ($attendance as $record) {
            $date = $record['date'];
            $recordMonth = date('m', strtotime($date));
            $recordYear = date('Y', strtotime($date));
            
            if ($recordMonth == $month && $recordYear == $year) {
                $status = $record['status'];
                if (!isset($summary[$status])) {
                    $summary[$status] = 0;
                }
                $summary[$status]++;
            }
        }
        
        return $summary;
    }
}