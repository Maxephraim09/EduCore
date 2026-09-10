<?php

namespace App\Http\Controllers;

use App\Models\OtherIncome;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OtherIncomeController extends Controller
{
    public function index()
    {
        $incomes = OtherIncome::with('category', 'receiver')
            ->orderBy('income_date', 'desc')
            ->paginate(15);
        
        $totalIncome = OtherIncome::sum('amount');
        $monthlyIncome = OtherIncome::whereMonth('income_date', date('m'))
            ->whereYear('income_date', date('Y'))
            ->sum('amount');
        $todayIncome = OtherIncome::whereDate('income_date', date('Y-m-d'))->sum('amount');
        
        return view('incomes.index', compact('incomes', 'totalIncome', 'monthlyIncome', 'todayIncome'));
    }

    public function create()
    {
        $categories = IncomeCategory::orderBy('name')->get();
        return view('incomes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:income_categories,id',
            'source' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'income_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'description' => 'required|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Generate income number
            $incomeNumber = 'INC-' . date('Ymd') . '-' . str_pad(OtherIncome::count() + 1, 4, '0', STR_PAD_LEFT);
            
            // Handle receipt upload
            $receiptPath = null;
            if ($request->hasFile('receipt')) {
                $receiptPath = $request->file('receipt')->store('income_receipts', 'public');
            }

            $income = OtherIncome::create([
                'income_number' => $incomeNumber,
                'category_id' => $request->category_id,
                'source' => $request->source,
                'amount' => $request->amount,
                'income_date' => $request->income_date,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'description' => $request->description,
                'receipt_path' => $receiptPath,
                'received_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('other-incomes.index')
                ->with('success', 'Income recorded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording income: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $income = OtherIncome::with('category', 'receiver')->findOrFail($id);
        return view('incomes.show', compact('income'));
    }

    public function edit($id)
    {
        $income = OtherIncome::findOrFail($id);
        $categories = IncomeCategory::orderBy('name')->get();
        
        return view('incomes.edit', compact('income', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $income = OtherIncome::findOrFail($id);
        
        $request->validate([
            'category_id' => 'required|exists:income_categories,id',
            'source' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'income_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'description' => 'required|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Handle receipt upload
            if ($request->hasFile('receipt')) {
                if ($income->receipt_path) {
                    Storage::disk('public')->delete($income->receipt_path);
                }
                $receiptPath = $request->file('receipt')->store('income_receipts', 'public');
                $income->receipt_path = $receiptPath;
            }

            $income->update($request->except('receipt', '_token', '_method'));

            DB::commit();

            return redirect()->route('other-incomes.show', $income->id)
                ->with('success', 'Income updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating income: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $income = OtherIncome::findOrFail($id);
            
            // Delete receipt if exists
            if ($income->receipt_path) {
                Storage::disk('public')->delete($income->receipt_path);
            }
            
            $income->delete();

            return redirect()->route('other-incomes.index')
                ->with('success', 'Income record deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting income: ' . $e->getMessage());
        }
    }

    public function reports()
    {
        // Income by category for current year
        $incomeByCategory = OtherIncome::join('income_categories', 'other_incomes.category_id', '=', 'income_categories.id')
            ->whereYear('income_date', date('Y'))
            ->select('income_categories.name', 
                DB::raw('SUM(other_incomes.amount) as total'),
                DB::raw('COUNT(*) as count'))
            ->groupBy('income_categories.id')
            ->orderBy('total', 'desc')
            ->get();
        
        // Monthly income for current year
        $monthlyIncome = OtherIncome::select(
                DB::raw('strftime("%m", income_date) as month'),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('income_date', date('Y'))
            ->groupBy(DB::raw('strftime("%m", income_date)'))
            ->orderBy('month')
            ->get();
        
        // Income sources breakdown
        $incomeSources = OtherIncome::select('source', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('source')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
        
        // Payment method distribution
        $paymentMethods = OtherIncome::select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();
        
        // Yearly comparison
        $yearlyIncome = OtherIncome::select(
                DB::raw('strftime("%Y", income_date) as year'),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('strftime("%Y", income_date)'))
            ->orderBy('year', 'desc')
            ->get();
        
        return view('incomes.reports', compact('incomeByCategory', 'monthlyIncome', 'incomeSources', 'paymentMethods', 'yearlyIncome'));
    }

    public function exportExcel()
    {
        // This would generate Excel export
        // For now, redirect back with message
        return back()->with('info', 'Excel export feature coming soon!');
    }

    public function exportPdf()
    {
        // This would generate PDF export
        // For now, redirect back with message
        return back()->with('info', 'PDF export feature coming soon!');
    }
}
