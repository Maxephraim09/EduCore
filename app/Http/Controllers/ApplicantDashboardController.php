<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicantDashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $applications = JobApplication::query()
            ->where('applicant_id', $user->id)
            ->with(['jobPost'])
            ->latest()
            ->get();

        $stats = [
            'total' => $applications->count(),
            'pending' => $applications->where('status', 'pending')->count(),
            'shortlisted' => $applications->where('status', 'shortlisted')->count(),
            'accepted' => $applications->where('status', 'accepted')->count(),
            'rejected' => $applications->where('status', 'rejected')->count(),
        ];

        $recentApplications = $applications->take(5);

        return view('recruitment.applicant.dashboard', compact('applications', 'stats', 'recentApplications'));
    }

    public function applications()
    {
        $applications = JobApplication::query()
            ->where('applicant_id', Auth::id())
            ->with(['jobPost'])
            ->latest()
            ->paginate(10);

        return view('recruitment.applicant.applications', compact('applications'));
    }

    public function applicationStatus($id)
    {
        $application = JobApplication::query()
            ->where('applicant_id', Auth::id())
            ->with(['jobPost', 'reviewedBy'])
            ->findOrFail($id);

        return view('recruitment.applicant.status', compact('application'));
    }

    public function editApplication($id)
    {
        $application = JobApplication::query()
            ->where('applicant_id', Auth::id())
            ->findOrFail($id);

        if ($application->status !== 'pending') {
            return redirect()->route('applicant.applications')
                ->with('error', 'You cannot edit an application that is already under review.');
        }

        return view('recruitment.applicant.edit', compact('application'));
    }

    public function updateApplication(Request $request, $id)
    {
        $application = JobApplication::query()
            ->where('applicant_id', Auth::id())
            ->findOrFail($id);

        if ($application->status !== 'pending') {
            return redirect()->route('applicant.applications')
                ->with('error', 'You cannot edit an application that is already under review.');
        }

        $request->validate([
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'experience' => 'nullable|string',
            'cover_letter' => 'nullable|string',
            'skills' => 'nullable|string',
        ]);

        $application->update($request->only([
            'phone',
            'address',
            'qualifications',
            'experience',
            'cover_letter',
            'skills',
        ]));

        return redirect()->route('applicant.applications')
            ->with('success', 'Application updated successfully.');
    }
}

