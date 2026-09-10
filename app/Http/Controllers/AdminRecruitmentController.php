<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ApplicationSetting;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminRecruitmentController extends Controller
{
    public function index()
    {
        $jobs = JobPost::with(['createdBy', 'academicYear'])
            ->withCount(['applications'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => JobPost::count(),
            'active' => JobPost::where('is_active', true)->count(),
            'expired' => JobPost::where('application_deadline', '<', now())->count(),
            'total_applications' => JobApplication::count(),
            'pending_applications' => JobApplication::where('status', 'pending')->count(),
        ];

        return view('recruitment.admin.index', compact('jobs', 'stats'));
    }

    public function create()
    {
        return view('recruitment.admin.create', [
            'departments' => JobPost::getDepartments(),
            'jobTypes' => JobPost::getJobTypes(),
            'experienceLevels' => JobPost::getExperienceLevels(),
            'academicYears' => AcademicYear::query()->where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'salary_range' => 'nullable|string|max:255',
            'application_deadline' => 'required|date|after:today',
            'job_type' => 'required|string',
            'experience_level' => 'required|string',
            'is_featured' => 'nullable|boolean',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);

        JobPost::create($validated);

        return redirect()->route('admin.recruitment.jobs.index')
            ->with('success', 'Job posted successfully!');
    }

    public function show($id)
    {
        $job = JobPost::with(['createdBy', 'academicYear', 'applications'])->findOrFail($id);

        $stats = [
            'total_applications' => $job->applications->count(),
            'pending' => $job->applications->where('status', 'pending')->count(),
            'shortlisted' => $job->applications->where('status', 'shortlisted')->count(),
            'interviewed' => $job->applications->where('status', 'interviewed')->count(),
            'accepted' => $job->applications->where('status', 'accepted')->count(),
            'rejected' => $job->applications->where('status', 'rejected')->count(),
        ];

        return view('recruitment.admin.show', compact('job', 'stats'));
    }

    public function edit($id)
    {
        $job = JobPost::query()->findOrFail($id);

        return view('recruitment.admin.edit', [
            'job' => $job,
            'departments' => JobPost::getDepartments(),
            'jobTypes' => JobPost::getJobTypes(),
            'experienceLevels' => JobPost::getExperienceLevels(),
            'academicYears' => AcademicYear::query()->where('is_active', true)->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $job = JobPost::query()->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'salary_range' => 'nullable|string|max:255',
            'application_deadline' => 'required|date',
            'job_type' => 'required|string',
            'experience_level' => 'required|string',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['is_featured'] = $request->boolean('is_featured', false);

        $job->update($validated);

        return redirect()->route('admin.recruitment.jobs.index')
            ->with('success', 'Job updated successfully!');
    }

    public function destroy($id)
    {
        $job = JobPost::query()->findOrFail($id);

        if ($job->applications()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete job with existing applications.');
        }

        $job->delete();

        return redirect()->route('admin.recruitment.jobs.index')
            ->with('success', 'Job deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $job = JobPost::query()->findOrFail($id);
        $job->is_active = ! $job->is_active;
        $job->save();

        return redirect()->back()->with('success', 'Job status updated successfully!');
    }

    public function applications(Request $request)
    {
        $query = JobApplication::with(['jobPost', 'applicant']);

        if ($request->filled('job_id')) {
            $query->where('job_post_id', $request->integer('job_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $applications = $query->latest()->paginate(20);
        $jobs = JobPost::query()->where('is_active', true)->get();
        $statuses = ['pending', 'under_review', 'shortlisted', 'interviewed', 'accepted', 'rejected'];

        $stats = [
            'total' => JobApplication::count(),
            'pending' => JobApplication::where('status', 'pending')->count(),
            'shortlisted' => JobApplication::where('status', 'shortlisted')->count(),
            'accepted' => JobApplication::where('status', 'accepted')->count(),
            'rejected' => JobApplication::where('status', 'rejected')->count(),
        ];

        return view('recruitment.admin.applications', compact('applications', 'jobs', 'statuses', 'stats'));
    }

    public function applicationDetails($id)
    {
        $application = JobApplication::with(['jobPost', 'applicant', 'reviewedBy', 'employee'])->findOrFail($id);

        $statuses = ['pending', 'under_review', 'shortlisted', 'interviewed', 'accepted', 'rejected'];

        return view('recruitment.admin.application-details', compact('application', 'statuses'));
    }

    public function updateApplicationStatus(Request $request, $id)
    {
        $application = JobApplication::query()->findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,under_review,shortlisted,interviewed,accepted,rejected',
            'admin_notes' => 'nullable|string',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
            'interview_date' => 'nullable|date|after_or_equal:today',
            'interview_location' => 'nullable|string|max:255',
            'interview_link' => 'nullable|url',
        ]);

        DB::beginTransaction();

        try {
            $application->status = $request->string('status');
            $application->admin_notes = $request->input('admin_notes');

            if ($application->status === 'rejected') {
                $application->rejection_reason = $request->input('rejection_reason');
            }

            if (in_array($application->status, ['shortlisted', 'interviewed'], true)) {
                $application->interview_date = $request->input('interview_date');
                $application->interview_location = $request->input('interview_location');
                $application->interview_link = $request->input('interview_link');
            }

            if ($application->status === 'accepted') {
                $application->hired_date = now();
                $this->convertToEmployee($application);
            }

            $application->reviewed_by = Auth::id();
            $application->reviewed_at = now();

            $application->save();

            // Optional email notification
            try {
                Mail::to($application->email)->send(new \App\Mail\ApplicationStatusUpdate($application));
            } catch (\Throwable $e) {
                \Log::error('Failed to send recruitment status email: '.$e->getMessage());
            }

            DB::commit();

            return redirect()->route('admin.recruitment.application.details', $application->id)
                ->with('success', 'Application status updated successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Failed to update status: '.$e->getMessage());
        }
    }

    public function convertToEmployee(JobApplication $application)
    {
        if ($application->employee_id) {
            return;
        }

        $employee = Employee::query()->create([
            'first_name' => $application->first_name,
            'last_name' => $application->last_name,
            'email' => $application->email,
            'phone' => $application->phone,
            'address' => $application->address,
            'date_of_birth' => $application->date_of_birth,
            'gender' => $application->gender,
            'position' => $application->jobPost?->title,
            'department' => $application->jobPost?->department,
            'joining_date' => now(),
            'is_active' => true,
        ]);

        $application->employee_id = $employee->id;
        $application->save();

        $autoCreate = ApplicationSetting::getValue('auto_create_user_on_acceptance', false);
        if (! $autoCreate) {
            return;
        }

        $password = Str::random(8);

        $user = User::query()->create([
            'name' => $application->first_name.' '.$application->last_name,
            'email' => $application->email,
            'password' => Hash::make($password),
            'role' => ApplicationSetting::getValue('default_employee_role', 'teacher'),
            'is_active' => true,
        ]);

        try {
            Mail::to($application->email)->send(new \App\Mail\EmployeeWelcome($application, $user, $password));
        } catch (\Throwable $e) {
            \Log::error('Failed to send employee welcome email: '.$e->getMessage());
        }
    }

    public function downloadCV($id)
    {
        $application = JobApplication::query()->findOrFail($id);

        if (! $application->cv_path) {
            return redirect()->back()->with('error', 'No CV found for this application.');
        }

        return response()->download(storage_path('app/public/'.$application->cv_path));
    }

    public function downloadCertificates($id)
    {
        $application = JobApplication::query()->findOrFail($id);

        if (! $application->certificates_path) {
            return redirect()->back()->with('error', 'No certificates found for this application.');
        }

        return response()->download(storage_path('app/public/'.$application->certificates_path));
    }

    public function settings()
    {
        $settings = ApplicationSetting::query()
            ->where('group', 'recruitment')
            ->get()
            ->pluck('typed_value', 'key')
            ->toArray();

        return view('recruitment.admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'recruitment_status' => 'required|in:open,closed',
            'default_application_deadline_days' => 'required|integer|min:1|max:365',
            'recruitment_required_documents' => 'nullable|array',
            'auto_create_user_on_acceptance' => 'required|boolean',
            'default_employee_role' => 'required|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            ApplicationSetting::setValue($key, $value);
        }

        return redirect()->route('admin.recruitment.settings')
            ->with('success', 'Recruitment settings updated successfully!');
    }
}

