<?php

namespace App\Mail;

use App\Models\Applicant;
use App\Models\PaystackTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicantPaymentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;
    public $transaction;

    public function __construct(Applicant $applicant, PaystackTransaction $transaction)
    {
        $this->applicant = $applicant;
        $this->transaction = $transaction;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Application Fee Payment Confirmed',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.applicant-payment-confirmation',
            with: [
                'applicant' => $this->applicant,
                'transaction' => $this->transaction,
            ]
        );
    }
}
