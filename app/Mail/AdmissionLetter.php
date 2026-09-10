<?php

namespace App\Mail;

use App\Models\Application;
use App\Models\Student;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdmissionLetter extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $student;
    public $user;
    public $password;

    public function __construct(Application $application, Student $student, User $user, $password)
    {
        $this->application = $application;
        $this->student = $student;
        $this->user = $user;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Admission Letter - ' . $this->application->admission_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admission-letter',
            with: [
                'application' => $this->application,
                'student' => $this->student,
                'user' => $this->user,
                'password' => $this->password,
            ]
        );
    }
}