<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Result;
use Illuminate\Support\Facades\Auth;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $teacherId = getCurrentEmployeeId();
        
        $myClasses = ClassModel::where('class_teacher_id', $teacherId)
            ->where('is_active', true)
            ->count();
        
        $myStudents = Student::whereHas('class', function($q) use ($teacherId) {
            $q->where('class_teacher_id', $teacherId);
        })->where('is_active', true)->count();
        
        $pendingGrading = Result::whereHas('exam', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->where('is_published', false)->count();
        
        $upcomingExams = Exam::where('teacher_id', $teacherId)
            ->where('start_date', '>=', now())
            ->where('is_published', true)
            ->count();
        
        $myClassesList = ClassModel::where('class_teacher_id', $teacherId)
            ->where('is_active', true)
            ->withCount(['students' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('name')
            ->orderBy('section')
            ->get();
        
        return view('dashboards.teacher', compact(
            'myClasses', 'myStudents', 'pendingGrading', 
            'upcomingExams', 'myClassesList'
        ));
    }
}