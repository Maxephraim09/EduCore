<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Employee;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\FeePayment;
use App\Models\Asset;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::where('is_active', true)->count();
        $totalTeachers = Employee::where('is_active', true)
            ->where(function($q) {
                $q->where('position', 'like', '%Teacher%')
                  ->orWhere('position', 'like', '%Head%');
            })
            ->count();
        $totalClasses = ClassModel::where('is_active', true)->count();
        $totalSubjects = Subject::where('is_active', true)->count();
        $pendingExams = Exam::where('is_published', false)->count();
        $publishedExams = Exam::where('is_published', true)->count();
        $totalRevenue = FeePayment::sum('amount_paid') ?? 0;
        $totalAssetValue = Asset::where('status', 'active')->sum('current_value') ?? 0;
        
        $recentStudents = Student::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboards.admin', compact(
            'totalStudents', 'totalTeachers', 'totalClasses', 'totalSubjects',
            'pendingExams', 'publishedExams', 'totalRevenue', 'totalAssetValue',
            'recentStudents'
        ));
    }
}