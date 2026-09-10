<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\ClassModel;
use App\Models\ClassCategory;
use App\Models\Student;
use App\Models\User;
use App\Models\FeeStructure;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PaystackTransaction;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    // ==================== PUBLIC APPLICATION FORM ====================

    public function showApplicationForm()
    {
        try {
            // If an applicant is logged in via applicant guard, require payment before showing main form
            if (Auth::guard('applicant')->check()) {
                $applicant = Auth::guard('applicant')->user();
                $paidApplicantId = Session::get('applicant_paid_application_fee');

                // Also check database transactions for verified payments (online or manual)
                $hasPaid = PaystackTransaction::where('applicant_id', $applicant->id)
                    ->whereIn('status', ['success','success_manual'])
                    ->exists();

                if (!($paidApplicantId && intval($paidApplicantId) === intval($applicant->id)) && !$hasPaid) {
                    return redirect()->route('application.select-category')
                        ->with('error', 'Please complete the application fee payment before proceeding.');
                }
            }

            $classes = ClassModel::where('is_active', true)->get();
            $categories = ClassCategory::where('is_active', true)->get();
            $applicationFee = SystemSetting::getValue('application_fee', 5000);
            $registrationFees = SystemSetting::getValue('registration_fees', '{}');
            $applicant = Auth::guard('applicant')->user();
            $selectedCategoryId = session('applicant_selected_category_id') ?? Cookie::get('applicant_selected_category_id');
            $selectedClassId = session('applicant_selected_class_id') ?? Cookie::get('applicant_selected_class_id');

            if (empty($selectedCategoryId) && !empty($selectedClassId)) {
                $selectedCategoryId = ClassModel::where('id', $selectedClassId)->value('class_category_id');
            }

            return view('application.form', compact('classes', 'categories', 'applicationFee', 'registrationFees', 'applicant', 'selectedCategoryId', 'selectedClassId'));
        } catch (\Exception $e) {
            Log::error('Error loading application form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load application form. Please try again.');
        }
    }

    public function submitApplication(Request $request)
    {
        $request->validate([
            // Personal Information
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:applications,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'address' => 'required|string',
            
            // Parent Information
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|max:20',
            'parent_email' => 'nullable|email',
            
            // Academic Information
            'class_id' => 'required|exists:classes,id',
            'previous_school' => 'nullable|string',
            'previous_class' => 'nullable|string',
            
            // Documents
            'birth_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'passport_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'report_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Create application
            $application = new Application();
            $application->fill($request->except(['birth_certificate', 'passport_photo', 'report_card']));
            $application->application_number = $application->generateApplicationNumber();
            $application->status = 'pending';
            $application->payment_status = 'pending';
            $application->application_fee = SystemSetting::getValue('application_fee', 5000);
            $application->save();

            $application->refresh();

            // Handle document uploads
            $documents = ['birth_certificate', 'passport_photo', 'report_card'];
            foreach ($documents as $doc) {
                if ($request->hasFile($doc)) {
                    $path = $request->file($doc)->store('applications/documents/' . $application->id, 'public');
                    ApplicationDocument::create([
                        'application_id' => $application->id,
                        'document_type' => $doc,
                        'document_name' => $request->file($doc)->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $request->file($doc)->getMimeType(),
                        'file_size' => $request->file($doc)->getSize(),
                    ]);
                }
            }

            DB::commit();

            // Send confirmation email
            $this->sendApplicationConfirmation($application);

            return redirect()->route('application.success', $application->application_number)
                ->with('success', 'Application submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Application submission error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error submitting application: ' . $e->getMessage());
        }
    }

    public function applicationSuccess($applicationNumber)
    {
        $application = Application::where('application_number', $applicationNumber)->firstOrFail();
        return view('application.success', compact('application'));
    }

    // ==================== PAYMENT ====================

    public function paymentForm($applicationNumber)
    {
        $application = Application::where('application_number', $applicationNumber)
            ->where('status', 'pending')
            ->firstOrFail();
        
        return view('application.payment', compact('application'));
    }

    public function processPayment(Request $request, $applicationNumber)
    {
        $application = Application::where('application_number', $applicationNumber)
            ->where('status', 'pending')
            ->firstOrFail();

        $request->validate([
            'payment_method' => 'required|in:card,bank_transfer,paystack',
            'payment_reference' => 'required_if:payment_method,bank_transfer',
        ]);

        try {
            DB::beginTransaction();

            // This is a simplified payment processing
            // Integrate with Paystack/Flutterwave here
            $application->payment_status = 'paid';
            $application->payment_reference = $request->payment_reference ?? 'REF-' . Str::random(10);
            $application->amount_paid = $application->application_fee;
            $application->payment_date = now();
            $application->payment_details = [
                'method' => $request->payment_method,
                'reference' => $application->payment_reference,
            ];
            $application->save();

            DB::commit();

            // Send payment confirmation email
            $this->sendPaymentConfirmation($application);

            return redirect()->route('application.payment.success', $application->application_number)
                ->with('success', 'Payment successful! Your application is now being processed.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    public function paymentSuccess($applicationNumber)
    {
        $application = Application::where('application_number', $applicationNumber)
            ->where('payment_status', 'paid')
            ->firstOrFail();
        
        return view('application.payment-success', compact('application'));
    }

    // ==================== ADMIN MANAGEMENT ====================

    public function adminApplications(Request $request)
    {
        $query = Application::with(['class', 'student']);

        // Filters
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'LIKE', "%{$request->search}%")
                  ->orWhere('last_name', 'LIKE', "%{$request->search}%")
                  ->orWhere('email', 'LIKE', "%{$request->search}%")
                  ->orWhere('application_number', 'LIKE', "%{$request->search}%");
            });
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(20);
        $classes = ClassModel::where('is_active', true)->get();

        $stats = [
            'pending' => Application::pending()->count(),
            'under_review' => Application::underReview()->count(),
            'approved' => Application::approved()->count(),
            'admitted' => Application::admitted()->count(),
            'rejected' => Application::rejected()->count(),
            'paid' => Application::paid()->count(),
        ];

        return view('application.admin.index', compact('applications', 'classes', 'stats'));
    }

    public function adminApplicationDetails($id)
    {
        $application = Application::with(['class', 'documents', 'student'])->findOrFail($id);
        $classes = ClassModel::where('is_active', true)->get();
        
        return view('application.admin.details', compact('application', 'classes'));
    }

    public function adminUpdateStatus(Request $request, $id)
    {
        $application = Application::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,under_review,approved,admitted,rejected',
            'class_id' => 'nullable|exists:classes,id',
            'remarks' => 'nullable|string',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $application->status;
            $application->status = $request->status;
            
            if ($request->class_id) {
                $application->class_id = $request->class_id;
            }
            
            if ($request->remarks) {
                $application->remarks = $request->remarks;
            }
            
            if ($request->status === 'rejected' && $request->rejection_reason) {
                $application->rejection_reason = $request->rejection_reason;
            }

            // If status changes to admitted
            if ($request->status === 'admitted' && $oldStatus !== 'admitted') {
                $application->admitted_at = now();
                $application->admitted_by = auth()->id();
                $application->admission_number = $application->generateAdmissionNumber();

                // Create student account
                $student = $this->createStudentFromApplication($application);
                $application->student_id = $student->id;

                // Create user account for login
                $user = $this->createUserFromApplication($application, $student);
                $application->user_id = $user->id;

                // Send admission email
                $this->sendAdmissionLetter($application, $student, $user);
                
                // Insert into fee structure
                $this->setupStudentFees($application, $student);
            }

            $application->save();

            DB::commit();

            return redirect()->route('application.admin.details', $id)
                ->with('success', 'Application status updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin status update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    public function adminBulkAction(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:applications,id',
            'action' => 'required|in:approve,reject,under_review',
        ]);

        try {
            DB::beginTransaction();

            $count = 0;
            foreach ($request->application_ids as $id) {
                $application = Application::find($id);
                if ($application && $application->isPending()) {
                    $application->status = $request->action === 'approve' ? 'approved' : 
                                          ($request->action === 'reject' ? 'rejected' : 'under_review');
                    $application->save();
                    $count++;
                }
            }

            DB::commit();

            return redirect()->route('application.admin.index')
                ->with('success', "{$count} applications updated successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk action error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ==================== ADMISSION LETTER ====================

    public function admissionLetter($admissionNumber)
    {
        try {
            $application = Application::where('admission_number', $admissionNumber)
                ->where('status', 'admitted')
                ->firstOrFail();
            
            return view('application.admission-letter', compact('application'));
        } catch (\Exception $e) {
            Log::error('Admission letter view error: ' . $e->getMessage());
            return redirect()->route('application.portal.dashboard')
                ->with('error', 'Unable to load admission letter. Please try again.');
        }
    }

    public function downloadAdmissionLetter($admissionNumber)
    {
        try {
            // Set execution time limit to prevent timeout
            set_time_limit(60);
            
            $application = Application::where('admission_number', $admissionNumber)
                ->where('status', 'admitted')
                ->with(['student', 'class'])
                ->firstOrFail();
            
            // Check if PDF directory exists
            $pdfDir = storage_path('app/public/admission-letters');
            if (!file_exists($pdfDir)) {
                mkdir($pdfDir, 0755, true);
            }
            
            // Generate a unique filename
            $filename = 'admission-letter-' . $application->admission_number . '.pdf';
            $pdfPath = $pdfDir . '/' . $filename;
            
            // Check if PDF already exists and is recent (less than 7 days old)
            if (file_exists($pdfPath) && filemtime($pdfPath) > strtotime('-7 days')) {
                return response()->download($pdfPath, $filename, [
                    'Content-Type' => 'application/pdf',
                ]);
            }
            
            // Load view with all necessary data
            $data = [
                'application' => $application,
                'student' => $application->student,
                'schoolName' => SystemSetting::getValue('school_name', 'School Name'),
                'schoolLogo' => SystemSetting::getValue('school_logo_url', ''),
                'schoolAddress' => SystemSetting::getValue('school_address', ''),
                'schoolPhone' => SystemSetting::getValue('school_phone', ''),
                'schoolEmail' => SystemSetting::getValue('school_email', ''),
                'principalName' => SystemSetting::getValue('principal_name', ''),
                'currentDate' => now()->format('F d, Y'),
            ];
            
            // Generate PDF with proper options.
            // NOTE: Keep remote rendering disabled to avoid hangs in some environments.
            $pdf = PDF::setOptions([
                'isRemoteEnabled' => false,
                'defaultFont' => 'sans-serif',
                'tempDir' => storage_path('temp'),
                'chroot' => realpath(base_path()),
                'isHtml5ParserEnabled' => true,
            ])->loadView('application.admission-letter', $data);

            // Save PDF for future use
            $pdfOutput = $pdf->output();
            file_put_contents($pdfPath, $pdfOutput);

            // Return download response
            return response()->download($pdfPath, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
            
        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            
            // If PDF generation fails, return a fallback response
            return redirect()->back()
                ->with('error', 'Unable to generate admission letter at this time. Please try again later or contact support.');
        }
    }

    // ==================== REGISTRATION METHODS ====================

    public function showRegistrationForm()
    {
        try {
            $applicant = Auth::guard('applicant')->user();
            
            if (!$applicant) {
                return redirect()->route('application.login')
                    ->with('error', 'Please login to continue.');
            }
            
            $application = Application::where('email', $applicant->email)
                ->where('status', 'admitted')
                ->first();
            
            if (!$application) {
                return redirect()->route('application.portal.dashboard')
                    ->with('error', 'You do not have any admitted application.');
            }
            
            if ($application->registration_completed) {
                return redirect()->route('application.portal.dashboard')
                    ->with('info', 'You have already completed registration.');
            }
            
            // Get fee structure
            $feeStructure = $this->getRegistrationFee($application);
            $classes = ClassModel::where('is_active', true)->get();
            
            return view('application.portal.registration', compact('application', 'feeStructure', 'classes'));
            
        } catch (\Exception $e) {
            Log::error('Registration form error: ' . $e->getMessage());
            return redirect()->route('application.portal.dashboard')
                ->with('error', 'Unable to load registration page. Please try again.');
        }
    }

    public function processRegistration(Request $request)
    {
        try {
            $applicant = Auth::guard('applicant')->user();
            
            if (!$applicant) {
                return redirect()->route('application.login')
                    ->with('error', 'Please login to continue.');
            }
            
            $application = Application::where('email', $applicant->email)
                ->where('status', 'admitted')
                ->first();
            
            if (!$application) {
                return redirect()->back()
                    ->with('error', 'Application not found.');
            }
            
            if ($application->registration_completed) {
                return redirect()->route('application.portal.dashboard')
                    ->with('info', 'Registration already completed.');
            }
            
            $request->validate([
                'terms' => 'required|accepted',
                'emergency_contact' => 'nullable|string|max:255',
                'emergency_phone' => 'nullable|string|max:20',
                'medical_conditions' => 'nullable|string',
                'allergies' => 'nullable|string',
            ]);
            
            DB::beginTransaction();
            
            // Mark registration as completed
            $application->registration_completed = true;
            $application->registration_completed_at = now();
            $application->emergency_contact = $request->emergency_contact;
            $application->emergency_phone = $request->emergency_phone;
            $application->medical_conditions = $request->medical_conditions;
            $application->allergies = $request->allergies;
            $application->save();
            
            // Update student record if exists
            if ($application->student_id) {
                $student = Student::find($application->student_id);
                if ($student) {
                    $student->emergency_contact = $request->emergency_contact;
                    $student->emergency_phone = $request->emergency_phone;
                    $student->medical_conditions = $request->medical_conditions;
                    $student->allergies = $request->allergies;
                    $student->save();
                }
            }
            
            DB::commit();
            
            // Log the user in as student if user exists
            if ($application->user_id) {
                Auth::loginUsingId($application->user_id);
            }
            
            return redirect()->route('dashboard.student')
                ->with('success', 'Registration completed successfully! Welcome to the school.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Registration failed. Please try again: ' . $e->getMessage());
        }
    }

    private function getRegistrationFee($application)
    {
        // Get registration fee based on class
        $regFeesJson = SystemSetting::getValue('registration_fees', '{}');
        $regFees = json_decode($regFeesJson, true) ?: [];
        
        if ($application->class_id && isset($regFees[$application->class_id])) {
            return $regFees[$application->class_id];
        }
        
        // Fallback to fee structure
        $feeStructure = FeeStructure::where('class_id', $application->class_id)
            ->where('fee_type', 'registration')
            ->where('is_active', true)
            ->first();
            
        return $feeStructure ? $feeStructure->amount : 0;
    }

    // ==================== HELPER METHODS ====================

    private function createStudentFromApplication(Application $application)
    {
        $className = null;
        if ($application->class_id) {
            $cls = ClassModel::find($application->class_id);
            $className = $cls ? ($cls->full_class_name ?? $cls->name ?? null) : null;
        }

        $studentData = [
            'admission_number' => $application->admission_number,
            'first_name' => $application->first_name,
            'last_name' => $application->last_name,
            'middle_name' => $application->middle_name,
            'date_of_birth' => $application->date_of_birth,
            'gender' => $application->gender,
            'email' => $application->email,
            'phone' => $application->phone,
            'address' => $application->address,
            'class_id' => $application->class_id,
            'class' => $className ?? 'Unassigned',
            'parent_name' => $application->parent_name ?? $application->father_name ?? $application->mother_name ?? 'Not provided',
            'parent_phone' => $application->parent_phone ?? $application->father_phone ?? $application->mother_phone ?? 'Not provided',
            'parent_email' => $application->parent_email ?? $application->father_email ?? $application->mother_email ?? 'not-provided@example.com',
            'father_name' => $application->parent_name,
            'father_phone' => $application->parent_phone,
            'father_email' => $application->parent_email,
            'is_active' => true,
            'admission_date' => now(),
        ];

        return Student::create($studentData);
    }

    private function createUserFromApplication(Application $application, Student $student)
    {
        $password = Str::random(8);
        $user = User::create([
            'name' => $application->full_name,
            'email' => $application->email,
            'password' => Hash::make($password),
            'role' => 'student',
            'is_active' => true,
        ]);

        // Store password in a secure way or send it via email
        session()->flash('student_password', $password);
        
        return $user;
    }

    private function setupStudentFees(Application $application, Student $student)
    {
        // First prefer registration_fees set in system settings (JSON map class_id => amount)
        $regFeesJson = \App\Models\SystemSetting::getValue('registration_fees', '{}');
        $regFees = json_decode($regFeesJson, true) ?: [];

        if ($application->class_id && isset($regFees[$application->class_id]) && is_numeric($regFees[$application->class_id])) {
            $amount = floatval($regFees[$application->class_id]);
            $student->total_fees = $amount;
            $student->save();

            $application->total_fees = $amount;
            $application->fee_status = 'unpaid';
            $application->save();
            return;
        }

        // Next try normalized fee_structures with fee_type = 'registration' and matching class_id
        $feeStructure = null;
        if ($application->class_id) {
            $feeStructure = FeeStructure::where('class_id', $application->class_id)
                ->where('fee_type', 'registration')
                ->where('is_active', true)
                ->first();
        }

        // Next try fee_type match by name or any active fee for the class
        if (!$feeStructure && $application->class_id) {
            $feeStructure = FeeStructure::where('class_id', $application->class_id)
                ->where('is_active', true)
                ->first();
        }

        // Fallback to earlier logic (match by class name or any active fee)
        if (!$feeStructure) {
            if ($application->class_id) {
                $class = ClassModel::find($application->class_id);
                if ($class) {
                    $feeStructure = FeeStructure::where(function($q) use ($class) {
                        $q->where('class', $class->full_class_name ?? $class->name)
                          ->orWhere('class', $class->name);
                    })->where('is_active', true)->first();
                }
            }
        }

        if (!$feeStructure) {
            $feeStructure = FeeStructure::where('is_active', true)->first();
        }

        if ($feeStructure) {
            $student->total_fees = $feeStructure->amount;
            $student->save();

            // Update application with fee info
            $application->total_fees = $feeStructure->amount;
            $application->fee_status = 'unpaid';
            $application->save();
        }
    }

    // ==================== EMAIL NOTIFICATIONS ====================

    private function sendApplicationConfirmation(Application $application)
    {
        try {
            Mail::to($application->email)->send(new \App\Mail\ApplicationConfirmation($application));
        } catch (\Exception $e) {
            // Log error but don't stop execution
            Log::error('Failed to send application confirmation email: ' . $e->getMessage());
        }
    }

    private function sendPaymentConfirmation(Application $application)
    {
        try {
            Mail::to($application->email)->send(new \App\Mail\PaymentConfirmation($application));
        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation email: ' . $e->getMessage());
        }
    }

    private function sendAdmissionLetter(Application $application, Student $student, User $user)
    {
        try {
            $password = session('student_password');
            Mail::to($application->email)->send(new \App\Mail\AdmissionLetter($application, $student, $user, $password));
        } catch (\Exception $e) {
            Log::error('Failed to send admission letter: ' . $e->getMessage());
        }
    }

    // ==================== PUBLIC METHODS ====================

    public function showStatusForm()
    {
        return view('application.status-form');
    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'application_number' => 'required|string',
            'email' => 'required|email',
        ]);

        $application = Application::where('application_number', $request->application_number)
            ->where('email', $request->email)
            ->first();

        if (!$application) {
            return redirect()->back()->with('error', 'Application not found. Please check your details.');
        }

        return view('application.status', compact('application'));
    }
}