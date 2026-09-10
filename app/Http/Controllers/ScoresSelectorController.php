<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Exam;
use Illuminate\Http\Request;

class ScoresSelectorController extends Controller
{
    public function choose(Request $request)
    {
        // Views expect camel-cased keys
        $exams = Exam::query()->orderByDesc('id')->get();
        $classes = ClassModel::query()->orderBy('id')->get();

        return view('scores.choose-exam-class', [
            'exams' => $exams,
            'classes' => $classes,
        ]);
    }
}

