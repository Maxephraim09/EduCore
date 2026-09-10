<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Application;
use App\Models\ClassCategory;
use App\Models\ClassModel;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cookie;
use App\Services\PaystackService;
use App\Models\PaystackTransaction;
use App\Mail\ApplicantPaymentConfirmation;
use Illuminate\Support\Facades\Session;

class ApplicantPortalController extends Controller
{
    protected $paystack;

    public function __construct(PaystackService $paystack)
    {
        $this->paystack = $paystack;
    }

    public function showLanding()
    {
        if (Auth::guard('applicant')->check()) {
            return redirect()->route('application.portal.dashboard');
        }

        return view('application.portal.index');
    }

    public function showRegister()
    {
        $categories = ClassCategory::where('is_active', true)->get();
        return view('application.portal.register', compact('categories'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:applicants,email',
            'phone' => 'nullable|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $applicant = Applicant::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('applicant')->loginUsingId($applicant->id);

        return redirect()->route('application.portal.dashboard');
    }

    public function showLogin()
    {
        return view('application.portal.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('applicant')->attempt($request->only('email', 'password'))) {
            return redirect()->route('application.portal.dashboard');
        }

        return redirect()->back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function dashboard()
    {
        $applicant = Auth::guard('applicant')->user();
        $application = $this->getApplicantApplication($applicant);
        $hasAdmittedApplication = $application && $application->isAdmitted();
        $transactionCount = PaystackTransaction::where('applicant_id', $applicant->id)->count();

        return view('application.portal.dashboard', compact('applicant', 'application', 'hasAdmittedApplication', 'transactionCount'));
    }

    public function profile()
    {
        $applicant = Auth::guard('applicant')->user();
        return view('application.portal.profile', compact('applicant'));
    }

    public function applicationOverview()
    {
        $applicant = Auth::guard('applicant')->user();
        $application = $this->getApplicantApplication($applicant);
        return view('application.portal.application', compact('applicant', 'application'));
    }

    public function admission()
    {
        $applicant = Auth::guard('applicant')->user();
        $application = $this->getApplicantApplication($applicant);

        if (!$application || !$application->isAdmitted()) {
            return redirect()->route('application.portal.dashboard')->with('error', 'Admission details are available only after approval.');
        }

        return view('application.portal.admission', compact('applicant', 'application'));
    }

    public function registration()
    {
        $applicant = Auth::guard('applicant')->user();
        $application = $this->getApplicantApplication($applicant);

        if (!$application || !$application->isAdmitted()) {
            return redirect()->route('application.portal.dashboard')->with('error', 'Registration information is available only after admission.');
        }

        return view('application.portal.registration', compact('applicant', 'application'));
    }

    public function paymentHistory()
    {
        $applicant = Auth::guard('applicant')->user();
        $payments = PaystackTransaction::where('applicant_id', $applicant->id)->latest()->get();
        return view('application.portal.payment-history', compact('applicant', 'payments'));
    }

    public function enquiry()
    {
        $applicant = Auth::guard('applicant')->user();
        return view('application.portal.enquiry', compact('applicant'));
    }

    public function sendEnquiry(Request $request)
    {
        $applicant = Auth::guard('applicant')->user();

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $contactEmail = getSchoolEmail();
        $payload = [
            'applicant' => $applicant,
            'subject' => $request->subject,
            'message' => $request->message,
        ];

        if ($contactEmail) {
            try {
                Mail::raw("Applicant enquiry from {$applicant->name} ({$applicant->email})\n\nSubject: {$request->subject}\n\nMessage:\n{$request->message}", function ($mail) use ($contactEmail, $request) {
                    $mail->to($contactEmail)
                        ->subject('Applicant Enquiry: ' . $request->subject);
                });
            } catch (\Exception $e) {
                \Log::error('Applicant enquiry mail failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('application.portal.enquiry')->with('success', 'Your enquiry has been submitted. We will respond shortly.');
    }

    public function logout(Request $request)
    {
        Auth::guard('applicant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('application.portal.index');
    }

    private function getApplicantApplication(Applicant $applicant)
    {
        return Application::where('email', $applicant->email)->latest()->first();
    }

    public function selectCategory()
    {
        $categories = ClassCategory::where('is_active', true)->get();
        return view('application.portal.select-category', compact('categories'));
    }

    public function postSelectCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:class_categories,id'
        ]);

        session(['applicant_selected_category_id' => $request->category_id]);
        Cookie::queue('applicant_selected_category_id', $request->category_id, 60);

        $classes = ClassModel::where('class_category_id', $request->category_id)->where('is_active', true)->get();
        return view('application.portal.choose-class', compact('classes'));
    }

    public function payForApplication(Request $request)
    {
        $applicant = Auth::guard('applicant')->user();
        if (!$applicant) {
            return redirect()->route('application.login')->with('error', 'Please login to continue');
        }

        $request->validate([
            'class_id' => 'required|exists:classes,id'
        ]);

        $class = ClassModel::findOrFail($request->class_id);

        session([
            'applicant_selected_class_id' => $request->class_id,
            'applicant_selected_category_id' => $class->class_category_id,
        ]);
        Cookie::queue('applicant_selected_class_id', $request->class_id, 60);
        Cookie::queue('applicant_selected_category_id', $class->class_category_id, 60);

        $amount = floatval(SystemSetting::getValue('application_fee', 5000));
        $reference = 'APP_PAY_' . time() . '_' . uniqid();

        // Initialize Paystack
        try {
            $response = $this->paystack->initializeTransaction($applicant->email, $amount, $reference, [
                'applicant_id' => $applicant->id,
                'class_id' => $class->id,
                'purpose' => 'application_fee'
            ], route('application.pay.callback'));
        } catch (\Exception $e) {
            \Log::error('Paystack init failed for applicant: ' . $applicant->id . ' - ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment gateway is currently unavailable. Please try again in a few minutes.');
        }

        // store transaction
        PaystackTransaction::create([
            'reference' => $reference,
            'amount' => $amount,
            'status' => 'pending',
            'applicant_id' => $applicant->id,
        ]);

        return redirect($response['data']['authorization_url']);
    }

    public function manualPaymentIntent(Request $request)
    {
        $applicant = Auth::guard('applicant')->user();
        if (!$applicant) {
            return redirect()->route('application.login')->with('error', 'Please login to continue');
        }

        $request->validate([
            'class_id' => 'required|exists:classes,id'
        ]);

        $class = ClassModel::findOrFail($request->class_id);
        $amount = floatval(SystemSetting::getValue('application_fee', 5000));

        session([
            'applicant_selected_class_id' => $request->class_id,
            'applicant_selected_category_id' => $class->class_category_id,
        ]);
        Cookie::queue('applicant_selected_class_id', $request->class_id, 60);
        Cookie::queue('applicant_selected_category_id', $class->class_category_id, 60);

        // create a pending manual transaction record
        $reference = 'APP_MANUAL_' . time() . '_' . uniqid();
        $tx = PaystackTransaction::create([
            'reference' => $reference,
            'amount' => $amount,
            'status' => 'pending_manual',
            'applicant_id' => $applicant->id,
        ]);

        // show bank details and instructions
        $bankName = getSetting('school_bank_name');
        $accountNumber = getSetting('school_account_number');
        $accountName = getSetting('school_account_name');

        return view('application.portal.manual-payment', compact('tx', 'class', 'bankName', 'accountNumber', 'accountName'));
    }

    public function manualPaymentVerify(Request $request, PaystackTransaction $tx)
    {
        $applicant = Auth::guard('applicant')->user();
        if (!$applicant || $tx->applicant_id != $applicant->id) {
            return redirect()->route('application.select-category')->with('error', 'Invalid transaction.');
        }

        // mark as awaiting admin verification
        $tx->status = 'awaiting_manual_verification';
        $tx->save();

        return redirect()->route('application.select-category')->with('success', 'Payment recorded. An administrator will verify and activate your application access.');
    }

    public function paymentCallback(Request $request)
    {
        $reference = $request->query('reference');
        if (!$reference) {
            return redirect()->route('application.portal.index')->with('error', 'Invalid payment reference');
        }

        $result = $this->paystack->verifyTransaction($reference);
        if ($result['data']['status'] === 'success') {
            // mark paystack transaction
            $tx = PaystackTransaction::where('reference', $reference)->first();
            if ($tx) {
                $tx->update([
                    'transaction_id' => $result['data']['id'],
                    'status' => 'success',
                    'paystack_response' => $result['data']
                ]);
                // set session flag to allow application form
                Session::put('applicant_paid_application_fee', $tx->applicant_id);

                try {
                    Mail::to($tx->applicant->email)->send(new ApplicantPaymentConfirmation($tx->applicant, $tx));
                } catch (\Exception $e) {
                    \Log::error('Failed to send applicant payment confirmation: ' . $e->getMessage());
                }
            }

            return redirect()->route('application.form')->with('success', 'Payment successful. You may proceed to complete the application.');
        }

        return redirect()->route('application.select-category')->with('error', 'Payment verification failed');
    }
}
