<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with('student')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('certificates.index', compact('certificates'));
    }

    public function create()
    {
        $students = Student::where('is_active', true)->orderBy('first_name')->get();
        $types = ['Academic Excellence', 'Graduation', 'Merit Award', 'Participation', 'Sports', 'Art & Culture'];
        
        return view('certificates.create', compact('students', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|string',
            'certificate_number' => 'required|unique:certificates',
            'issue_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $certificate = Certificate::create([
                'student_id' => $request->student_id,
                'type' => $request->type,
                'certificate_number' => $request->certificate_number,
                'issue_date' => $request->issue_date,
                'description' => $request->description,
                'status' => 'active',
            ]);

            DB::commit();

            return redirect()->route('certificates.index')
                ->with('success', 'Certificate created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating certificate: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $certificate = Certificate::with('student')->findOrFail($id);
        return view('certificates.show', compact('certificate'));
    }

    public function generate($id)
    {
        $certificate = Certificate::with('student')->findOrFail($id);
        
        $pdf = Pdf::loadView('certificates.pdf', compact('certificate'));
        return $pdf->download('certificate-' . $certificate->certificate_number . '.pdf');
    }

    public function destroy($id)
    {
        try {
            $certificate = Certificate::findOrFail($id);
            $certificate->delete();

            return redirect()->route('certificates.index')
                ->with('success', 'Certificate deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting certificate: ' . $e->getMessage());
        }
    }
}