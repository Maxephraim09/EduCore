<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaystackTransaction;
use Illuminate\Support\Facades\Mail;

class PaymentVerificationController extends Controller
{
    public function index()
    {
        $transactions = PaystackTransaction::whereIn('status', ['pending_manual','awaiting_manual_verification'])->orderBy('created_at','desc')->get();
        return view('admin.payments.manual-transactions', compact('transactions'));
    }

    public function verify($id)
    {
        $tx = PaystackTransaction::findOrFail($id);
        $tx->status = 'success_manual';
        $tx->save();

        try {
            Mail::to($tx->applicant->email)->send(new \App\Mail\ApplicantPaymentConfirmation($tx->applicant, $tx));
        } catch (\Exception $e) {
            \Log::error('Failed to send manual payment verification email: ' . $e->getMessage());
        }

        return redirect()->back()->with('success','Transaction marked as verified.');
    }

    public function reject($id)
    {
        $tx = PaystackTransaction::findOrFail($id);
        $tx->status = 'rejected_manual';
        $tx->save();

        return redirect()->back()->with('success','Transaction marked as rejected.');
    }
}
