<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Result;
use App\Models\Exam;
use App\Models\FeePayment;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return view('dashboards.student', ['student' => null]);
        }
        
        $results = Result::where('student_id', $student->id)->get();
        $totalScore = $results->sum('total_score');
        $averageScore = $results->count() > 0 ? round($totalScore / $results->count(), 2) : 0;
        $subjectCount = $results->groupBy('subject_id')->count();
        
        $upcomingExams = Exam::where('class_id', $student->class_id)
            ->where('start_date', '>=', now())
            ->where('is_published', true)
            ->count();
        
        $feePayments = FeePayment::where('student_id', $student->id)->get();
        $totalPaid = $feePayments->sum('amount_paid');
        $totalFees = $student->total_fees ?? 0;
        $balance = $totalFees - $totalPaid;
        
        $recentResults = Result::with(['subject', 'exam'])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboards.student', compact(
            'student', 'averageScore', 'subjectCount', 
            'upcomingExams', 'totalPaid', 'balance', 
            'totalFees', 'recentResults'
        ));
    }
}