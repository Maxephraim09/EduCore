<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Visitor;
use App\Models\Inquiry;
use Illuminate\Http\Request;


class FrontdeskDashboardController extends Controller
{
    public function index()
    {
        $todayVisitors = Visitor::whereDate('created_at', today())->count();
        $pendingRegistrations = Student::where('is_active', false)->count();
        $totalInquiries = Inquiry::count();

        $todayRegistrations = Student::whereDate('created_at', today())->count();

        // Use recentVisitors/recentInquiries variables to match the existing Blade UI.
        $recentVisitors = Visitor::latest()->limit(5)->get();
        $recentInquiries = Inquiry::latest()->limit(5)->get();

        return view('dashboards.frontdesk', compact(
            'todayVisitors',
            'pendingRegistrations',
            'totalInquiries',
            'todayRegistrations',
            'recentVisitors',
            'recentInquiries'
        ));
    }
}