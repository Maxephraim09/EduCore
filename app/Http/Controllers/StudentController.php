<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index()
    {
        $query = Student::orderBy('created_at', 'desc');

        if (auth()->user()?->role === 'teacher') {
            $teacherId = getCurrentEmployeeId();
            $query->whereHas('class', function ($classQuery) use ($teacherId) {
                $classQuery->where('class_teacher_id', $teacherId ?? 0);
            });
        }

        $students = $query->paginate(10);
        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:students',
            'admission_number' => 'required|unique:students',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'class' => 'required|string',
            'father_name' => 'required|string|max:255',
            'father_phone' => 'required|string|max:20',
            'mother_name' => 'required|string|max:255',
            'mother_phone' => 'required|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('student_photos', 'public');
            }

            $studentData = array_merge($request->except('_token', 'photo'), [
                'photo' => $photoPath,
                'email' => $request->email ?: strtolower($request->admission_number).'@students.local',
                'phone' => $request->phone_number ?: $request->father_phone,
                'address' => $request->home_address ?: 'Not provided',
                'parent_name' => $request->guardian_name ?: $request->father_name,
                'parent_phone' => $request->guardian_phone ?: $request->father_phone,
                'parent_email' => $request->guardian_email ?: ($request->father_email ?: ($request->mother_email ?: strtolower($request->admission_number).'.parent@students.local')),
            ]);

            $student = Student::create($studentData);

            DB::commit();

            return redirect()->route('students.show', $student->id)
                ->with('success', 'Student added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error adding student: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $student = Student::findOrFail($id);

        if (auth()->user()?->role === 'teacher' && $student->class?->class_teacher_id !== getCurrentEmployeeId()) {
            abort(403);
        }

        $feePayments = $student->feePayments()->orderBy('payment_date', 'desc')->get();
        return view('students.show', compact('student', 'feePayments'));
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:students,email,' . $id,
            'admission_number' => 'required|unique:students,admission_number,' . $id,
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'class' => 'required|string',
            'father_name' => 'required|string|max:255',
            'father_phone' => 'required|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            // Handle photo upload
            if ($request->hasFile('photo')) {
                if ($student->photo) {
                    Storage::disk('public')->delete($student->photo);
                }
                $photoPath = $request->file('photo')->store('student_photos', 'public');
                $request->merge(['photo' => $photoPath]);
            }

            $student->update($request->except('_token', '_method', 'photo'));

            DB::commit();

            return redirect()->route('students.show', $student->id)
                ->with('success', 'Student updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating student: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $student->delete();
            
            return redirect()->route('students.index')
                ->with('success', 'Student deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting student: ' . $e->getMessage());
        }
    }
}
