<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('category', 'approver', 'creator')
            ->orderBy('expense_date', 'desc')
            ->paginate(15);
        
        $totalExpenses = Expense::sum('amount');
        $monthlyExpenses = Expense::whereMonth('expense_date', date('m'))
            ->whereYear('expense_date', date('Y'))
            ->sum('amount');
        $pendingApprovals = Expense::where('status', 'pending')->count();
        
        return view('expenses.index', compact('expenses', 'totalExpenses', 'monthlyExpenses', 'pendingApprovals'));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method' => 'required|string',
            'vendor_name' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string',
            'description' => 'required|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Generate expense number
            $expenseNumber = 'EXP-' . date('Ymd') . '-' . str_pad(Expense::count() + 1, 4, '0', STR_PAD_LEFT);
            
            // Handle receipt upload
            $receiptPath = null;
            if ($request->hasFile('receipt')) {
                $receiptPath = $request->file('receipt')->store('expense_receipts', 'public');
            }

            $expense = Expense::create([
                'expense_number' => $expenseNumber,
                'category_id' => $request->category_id,
                'amount' => $request->amount,
                'expense_date' => $request->expense_date,
                'payment_method' => $request->payment_method,
                'vendor_name' => $request->vendor_name,
                'invoice_number' => $request->invoice_number,
                'description' => $request->description,
                'receipt_path' => $receiptPath,
                'status' => 'pending',
                'created_by' => Auth::id(),
                'approved_by' => null
            ]);

            DB::commit();

            return redirect()->route('expenses.index')
                ->with('success', 'Expense recorded successfully! Waiting for approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording expense: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $expense = Expense::with('category', 'approver', 'creator')->findOrFail($id);
        return view('expenses.show', compact('expense'));
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);
        
        $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method' => 'required|string',
            'vendor_name' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string',
            'description' => 'required|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Handle receipt upload
            if ($request->hasFile('receipt')) {
                // Delete old receipt if exists
                if ($expense->receipt_path) {
                    Storage::disk('public')->delete($expense->receipt_path);
                }
                $receiptPath = $request->file('receipt')->store('expense_receipts', 'public');
                $expense->receipt_path = $receiptPath;
            }

            $expense->update($request->except('receipt', '_token', '_method'));

            DB::commit();

            return redirect()->route('expenses.index')
                ->with('success', 'Expense updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating expense: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $expense = Expense::findOrFail($id);
            
            // Delete receipt if exists
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            
            $expense->delete();

            return redirect()->route('expenses.index')
                ->with('success', 'Expense deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting expense: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        try {
            $expense = Expense::findOrFail($id);
            
            if ($expense->status != 'pending') {
                return back()->with('error', 'Expense has already been processed.');
            }
            
            $expense->status = 'approved';
            $expense->approved_by = Auth::id();
            $expense->save();

            return redirect()->route('expenses.index')
                ->with('success', 'Expense approved successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error approving expense: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        try {
            $expense = Expense::findOrFail($id);
            
            if ($expense->status != 'pending') {
                return back()->with('error', 'Expense has already been processed.');
            }
            
            $expense->status = 'rejected';
            $expense->save();

            return redirect()->route('expenses.index')
                ->with('warning', 'Expense rejected!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error rejecting expense: ' . $e->getMessage());
        }
    }

    public function reports()
    {
        // Expense summary by category for current year
        $expensesByCategory = Expense::join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->whereYear('expense_date', date('Y'))
            ->select('expense_categories.name', 
                DB::raw('SUM(expenses.amount) as total'),
                DB::raw('COUNT(*) as count'))
            ->groupBy('expense_categories.id')
            ->orderBy('total', 'desc')
            ->get();
        
        // Monthly expenses for current year
        $monthlyExpenses = Expense::select(
                DB::raw('strftime("%m", expense_date) as month'),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('expense_date', date('Y'))
            ->groupBy(DB::raw('strftime("%m", expense_date)'))
            ->orderBy('month')
            ->get();
        
        // Top vendors
        $topVendors = Expense::select('vendor_name', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->whereNotNull('vendor_name')
            ->groupBy('vendor_name')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
        
        // Payment method distribution
        $paymentMethods = Expense::select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();
        
        return view('expenses.reports', compact('expensesByCategory', 'monthlyExpenses', 'topVendors', 'paymentMethods'));
    }

    public function exportExcel()
    {
        return back()->with('info', 'Excel export feature coming soon!');
    }

    public function exportPdf()
    {
        return back()->with('info', 'PDF export feature coming soon!');
    }

    public function emailReport(Request $request)
    {
        return back()->with('info', 'Email report feature coming soon!');
    }
}
