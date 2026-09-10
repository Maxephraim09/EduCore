<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecruitmentController extends Controller
{
    public function index()
    {
        return view('recruitment.index');
    }

    public function create()
    {
        return view('recruitment.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('recruitment.index')->with('success', 'Recruitment created successfully');
    }

    public function applications()
    {
        return view('recruitment.applications');
    }

    public function showApplication($id)
    {
        return view('recruitment.application.show', ['id' => $id]);
    }

    public function updateApplicationStatus(Request $request, $id)
    {
        return redirect()->route('recruitment.application.show', $id)->with('success', 'Application status updated');
    }

    public function staff()
    {
        return view('recruitment.staff');
    }
}

