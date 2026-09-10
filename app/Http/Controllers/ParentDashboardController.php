<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Result;
use Illuminate\Support\Facades\Auth;

class ParentDashboardController extends Controller
{
    public function index()
    {
        // Get children linked to this parent
        $children = Student::where('guardian_email', Auth::user()->email)
            ->orWhere('parent_email', Auth::user()->email)
            ->get();
        
        $childrenResults = collect();
        foreach ($children as $child) {
            $results = Result::with(['subject', 'exam'])
                ->where('student_id', $child->id)
                ->orderBy('created_at', 'desc')
                ->get();
            $childrenResults[$child->id] = $results;
        }
        
        return view('dashboards.parent', compact('children', 'childrenResults'));
    }
}