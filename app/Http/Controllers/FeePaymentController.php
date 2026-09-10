<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\PaystackTransaction;
use App\Models\SystemSetting;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FeePaymentController extends Controller
{
    protected $paystack;

    public function __construct(PaystackService $paystack)
    {
        $this->paystack = $paystack;
    }

    public function index()
    {
        $payments = FeePayment::with(['student', 'collector'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('fee-payments.index', compact('payments'));
    }

    public function create()
    {
        $students = Student::where('is_active', true)->orderBy('first_name')->get();
        $feeStructures = FeeStructure::where('is_active', true)->get();
        
        return view('fee-payments.collect', compact('students', 'feeStructures'));
    }

    public function initializePayment(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:100',
            'fee_type' => 'required|string',
            'term' => 'required|string',
            'academic_year' => 'required|string',
        ]);

        $student = Student::findOrFail($request->student_id);

        // If fee_structure_id is provided, prefer it for exact lookup
        $feeStructure = null;
        if ($request->filled('fee_structure_id')) {
            $feeStructure = FeeStructure::where('id', $request->fee_structure_id)
                ->where('is_active', true)
                ->first();

            if (! $feeStructure) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Selected fee structure is invalid or not available.'
                ], 422);
            }

            // Optionally ensure it applies to the student's class
            if ($student->class_id && $feeStructure->class_id && $feeStructure->class_id != $student->class_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Selected fee structure does not apply to the selected student.'
                ], 422);
            }

            $amount = (float) $feeStructure->amount;
            $term = $feeStructure->term ?? $request->term;
            $academicYear = $feeStructure->academic_year ?? $request->academic_year;
        } else {
            // Ensure the selected fee_type corresponds to an active FeeStructure
            $feeQuery = FeeStructure::where('is_active', true)
                ->where(function($q) use ($request) {
                    $q->where('fee_name', $request->fee_type)
                      ->orWhere('fee_type', $request->fee_type);
                });

            // Prefer matching term/academic_year if provided
            if ($request->filled('term')) {
                $feeQuery->where('term', $request->term);
            }
            if ($request->filled('academic_year')) {
                $feeQuery->where('academic_year', $request->academic_year);
            }

            // Try to match by student's class if available
            if ($student->class_id) {
                $feeQuery->where(function($q) use ($student) {
                    $q->where('class_id', $student->class_id)
                      ->orWhere('class', $student->class);
                });
            }

            $feeStructure = $feeQuery->first();

            if (! $feeStructure) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Selected fee type is invalid or not available for this student/term/year.'
                ], 422);
            }

            // Override amount and term/academic_year from fee structure to ensure consistency
            $amount = (float) $feeStructure->amount;
            $term = $feeStructure->term ?? $request->term;
            $academicYear = $feeStructure->academic_year ?? $request->academic_year;
        }

        $reference = 'PAY_' . time() . '_' . uniqid();

        try {
            $response = $this->paystack->initializeTransaction(
                $student->email ?? $student->parent_email,
                $amount,
                $reference,
                [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'fee_type' => $feeStructure->fee_name ?? $feeStructure->fee_type,
                    'fee_structure_id' => $feeStructure->id,
                    'term' => $term,
                    'academic_year' => $academicYear,
                ]
            );

            // Store payment record as pending
                $payment = FeePayment::create([
                'student_id' => $student->id,
                'receipt_number' => 'RCP_' . time() . '_' . uniqid(),
                'transaction_ref' => $reference,
                'amount' => $amount,
                'fee_type' => $feeStructure->fee_name ?? $feeStructure->fee_type,
                'amount_paid' => 0,
                'balance' => $amount,
                'payment_date' => now(),
                'payment_method' => 'paystack',
                'payment_status' => 'pending',
                'term' => $term,
                'academic_year' => $academicYear,
                'payment_details' => ['fee_type' => $feeStructure->fee_name ?? $feeStructure->fee_type, 'fee_structure_id' => $feeStructure->id],
                'collected_by' => Auth::id(),
                'received_by' => Auth::id(),
            ]);

            // Store paystack transaction reference
            PaystackTransaction::create([
                'reference' => $reference,
                'amount' => $amount,
                'status' => 'pending',
                'payment_id' => $payment->id,
            ]);

            return response()->json([
                'status' => 'success',
                'authorization_url' => $response['data']['authorization_url'],
                'reference' => $reference
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function handleCallback(Request $request)
    {
        $reference = $request->query('reference');
        
        if (!$reference) {
            return redirect()->route('fee-payments.create')
                ->with('error', 'Invalid payment reference');
        }

        try {
            $response = $this->paystack->verifyTransaction($reference);
            
            if ($response['data']['status'] === 'success') {
                DB::beginTransaction();
                
                $payment = FeePayment::where('transaction_ref', $reference)->firstOrFail();
                $amount = $response['data']['amount'] / 100;
                
                // Update payment record
                $payment->update([
                    'amount_paid' => $amount,
                    'balance' => 0,
                    'payment_status' => 'success',
                    'payment_details' => array_merge($payment->payment_details ?? [], [
                        'paystack_response' => $response['data']
                    ])
                ]);
                
                // Update paystack transaction record
                PaystackTransaction::where('reference', $reference)->update([
                    'transaction_id' => $response['data']['id'],
                    'status' => 'success',
                    'gateway_response' => $response['data']['gateway_response'],
                    'channel' => $response['data']['channel'],
                    'paystack_response' => $response['data']
                ]);
                
                // Update student's fee status
                $student = $payment->student;
                $student->paid_fees += $amount;
                $student->due_fees = $student->total_fees - $student->paid_fees;
                $student->save();
                
                DB::commit();
                
                return redirect()->route('fee-payments.receipt', $payment->id)
                    ->with('success', 'Payment successful!');
            } else {
                // Payment failed
                FeePayment::where('transaction_ref', $reference)->update([
                    'payment_status' => 'failed'
                ]);
                
                PaystackTransaction::where('reference', $reference)->update([
                    'status' => 'failed'
                ]);
                
                return redirect()->route('fee-payments.create')
                    ->with('error', 'Payment verification failed');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('fee-payments.create')
                ->with('error', 'Payment verification error: ' . $e->getMessage());
        }
    }

    public function showReceipt($id)
    {
        $payment = FeePayment::with(['student', 'paystackTransaction'])->findOrFail($id);
        return view('fee-payments.receipt', compact('payment'));
    }

    /**
     * Public verification endpoint for a receipt. Returns minimal details suitable for public verification.
     */
    public function verifyReceipt($id)
    {
        $payment = FeePayment::with(['student', 'paystackTransaction', 'receiver'])->find($id);

        if (! $payment) {
            return response()->view('fee-payments.verify', ['status' => 'not_found', 'payment' => null], 404);
        }

        $schoolName = SystemSetting::getValue('school_name', 'Bright Future School');
        $schoolAddress = SystemSetting::getValue('school_address', '');
        $payment->school_name = $schoolName;
        $payment->school_address = $schoolAddress;

        return view('fee-payments.verify', ['status' => 'found', 'payment' => $payment]);
    }

    public function paymentHistory($studentId = null)
    {
        if ($studentId) {
            $payments = FeePayment::where('student_id', $studentId)
                ->with('student')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            $payments = FeePayment::with('student')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }
        
        $students = Student::where('is_active', true)->orderBy('first_name')->get();
        
        return view('fee-payments.history', compact('payments', 'students', 'studentId'));
    }
}
