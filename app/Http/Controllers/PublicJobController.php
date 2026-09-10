<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ApplicationSetting;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PublicJobController extends Controller
{
    public function index(Request $request)
    {
        $query = JobPost::query()
            ->where('is_active', true)
            ->where('application_deadline', '>=', now());

        if ($request->filled('department')) {
            $query->where('department', $request->string('department'));
        }

        if ($request->filled('job_type')) {
            $query->where('job_type', $request->string('job_type'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('department', 'LIKE', "%{$search}%");
            });
        }

        $jobs = $query->latest()->paginate(10);

        return view('recruitment.jobs.index', [
            'jobs' => $jobs,
            'departments' => JobPost::getDepartments(),
            'jobTypes' => JobPost::getJobTypes(),
        ]);
    }

    public function show($id)
    {
        $job = JobPost::query()
            ->with(['createdBy', 'academicYear'])
            ->where('is_active', true)
            ->where('application_deadline', '>=', now())
            ->findOrFail($id);

        $hasApplied = false;
        if (Auth::check()) {
            $hasApplied = JobApplication::query()
                ->where('job_post_id', $job->id)
                ->where('applicant_id', Auth::id())
                ->exists();
        }

        return view('recruitment.jobs.show', [
            'job' => $job,
            'hasApplied' => $hasApplied,
        ]);
    }

    public function apply($id)
    {
        $job = JobPost::query()
            ->where('is_active', true)
            ->where('application_deadline', '>=', now())
            ->findOrFail($id);

        if (Auth::check()) {
            $existing = JobApplication::query()
                ->where('job_post_id', $job->id)
                ->where('applicant_id', Auth::id())
                ->first();

            if ($existing) {
                return redirect()->route('recruitment.jobs.show', $job->id)
                    ->with('info', 'You have already applied for this position.');
            }

            return view('recruitment.jobs.apply', [
                'job' => $job,
                'user' => Auth::user(),
            ]);
        }

        return view('recruitment.jobs.apply', [
            'job' => $job,
            'user' => null,
        ]);
    }

    public function storeApplication(Request $request, $id)
    {
        $job = JobPost::query()->findOrFail($id);

        $recruitmentStatus = ApplicationSetting::getValue('recruitment_status', 'open');
        if ((string) $recruitmentStatus !== 'open') {
            return redirect()->route('recruitment.jobs.index')
                ->with('error', 'Recruitment is currently closed.');
        }

        if (method_exists($job, 'canApply') && ! $job->canApply()) {
            return redirect()->route('recruitment.jobs.index')
                ->with('error', 'This job is no longer accepting applications.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'qualifications' => 'nullable|string',
            'experience' => 'nullable|string',
            'cover_letter' => 'nullable|string',
            'skills' => 'nullable|string',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'certificates' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'portfolio' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'terms' => 'required|accepted',
        ]);

        DB::beginTransaction();

        try {
            $password = Str::random(8);

            $user = User::create([
                'name' => $validated['first_name'].' '.$validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($password),
                'role' => 'applicant',
                'is_active' => true,
            ]);

            $cvPath = $request->file('cv')?->store('recruitment/cv', 'public');
            $certificatesPath = $request->file('certificates')?->store('recruitment/certificates', 'public');
            $portfolioPath = $request->file('portfolio')?->store('recruitment/portfolio', 'public');

            $application = JobApplication::query()->create([
                'job_post_id' => $job->id,
                'applicant_id' => $user->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'qualifications' => $validated['qualifications'],
                'experience' => $validated['experience'],
                'cover_letter' => $validated['cover_letter'],
                'skills' => $validated['skills'],
                'cv_path' => $cvPath,
                'certificates_path' => $certificatesPath,
                'portfolio_path' => $portfolioPath,
                'status' => 'pending',
                'academic_year_id' => AcademicYear::query()->where('is_current', true)->value('id')
                    ?? AcademicYear::query()->latest()->value('id'),
            ]);

            // Confirmation email (optional if mail classes exist)
            try {
                Mail::to($application->email)->send(new \App\Mail\ApplicationConfirmation($application, $password));
            } catch (\Throwable $e) {
                \Log::error('Failed to send job application confirmation: '.$e->getMessage());
            }

            DB::commit();

            Auth::login($user);

            return redirect()->route('applicant.dashboard')
                ->with('success', 'Application submitted successfully. Check your email for login credentials.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit application: '.$e->getMessage());
        }
    }
}

