<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeePayment;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class AccountantDashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = FeePayment::sum('amount_paid') ?? 0;
        $totalExpenses = Expense::sum('amount') ?? 0;
        $pendingInvoices = FeePayment::where('status', 'pending')->count();
        $totalTransactions = FeePayment::count();
        
        $recentPayments = FeePayment::with('student')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $monthlyRevenue = FeePayment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount_paid');
        
        return view('dashboards.accountant', compact(
            'totalRevenue', 'totalExpenses', 'pendingInvoices', 
            'totalTransactions', 'recentPayments', 'monthlyRevenue'
        ));
    }
}