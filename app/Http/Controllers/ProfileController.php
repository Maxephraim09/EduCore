<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profileData = $this->getProfileData($user);
        
        return view('profile.index', compact('user', 'profileData'));
    }

    public function edit()
    {
        $user = Auth::user();
        $profileData = $this->getProfileData($user);
        
        return view('profile.edit', compact('user', 'profileData'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Update user
            $user->name = $request->name;
            $user->email = $request->email;
            
            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $avatarPath;
            }
            
            $user->save();

            // Update related profile data
            $this->updateProfileData($user, $request);

            return redirect()->route('profile.index')
                ->with('success', 'Profile updated successfully!');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating profile: ' . $e->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        try {
            $user->password = Hash::make($request->password);
            $user->save();

            return back()->with('success', 'Password updated successfully!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating password: ' . $e->getMessage());
        }
    }

    public function updateSecurity(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'two_factor_enabled' => 'boolean',
            'notification_preferences' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Update security settings
            $user->two_factor_enabled = $request->has('two_factor_enabled');
            $user->notification_preferences = $request->notification_preferences ?? [];
            $user->save();

            return back()->with('success', 'Security settings updated successfully!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating security settings: ' . $e->getMessage());
        }
    }

    private function getProfileData($user)
    {
        $data = [
            'user' => $user,
            'role' => $user->role,
            'student' => null,
            'employee' => null,
            'stats' => [
                'total_students' => 0,
                'total_employees' => 0,
                'total_classes' => 0,
                'total_subjects' => 0,
            ],
            'recent_activities' => [],
        ];

        // Get student data if role is student
        if ($user->role === 'student') {
            $student = Student::where('user_id', $user->id)->first();
            if ($student) {
                $data['student'] = $student;
                $data['stats']['total_students'] = 1;
            }
        }

        // Get employee data if role is teacher, admin, etc.
        if (in_array($user->role, ['teacher', 'admin', 'super-admin', 'accountant', 'frontdesk'])) {
            $employee = Employee::where('user_id', $user->id)->first();
            if ($employee) {
                $data['employee'] = $employee;
                $data['stats']['total_employees'] = 1;
            }
        }

        // Get statistics based on role
        if ($user->role === 'super-admin' || $user->role === 'admin') {
            $data['stats']['total_students'] = \App\Models\Student::count();
            $data['stats']['total_employees'] = \App\Models\Employee::count();
            $data['stats']['total_classes'] = \App\Models\ClassModel::count();
            $data['stats']['total_subjects'] = \App\Models\Subject::count();
        } elseif ($user->role === 'teacher' && $data['employee']) {
            $teacherId = $data['employee']->id;
            $data['stats']['total_classes'] = \App\Models\ClassModel::where('class_teacher_id', $teacherId)->count();
            $data['stats']['total_students'] = \App\Models\Student::whereHas('class', function($q) use ($teacherId) {
                $q->where('class_teacher_id', $teacherId);
            })->count();
        }

        return $data;
    }

    private function updateProfileData($user, $request)
    {
        // Update student data if role is student
        if ($user->role === 'student') {
            $student = Student::where('user_id', $user->id)->first();
            if ($student) {
                $student->phone = $request->phone ?? $student->phone;
                $student->address = $request->address ?? $student->address;
                $student->save();
            }
        }

        // Update employee data if applicable
        if (in_array($user->role, ['teacher', 'admin', 'super-admin', 'accountant', 'frontdesk'])) {
            $employee = Employee::where('user_id', $user->id)->first();
            if ($employee) {
                $employee->phone = $request->phone ?? $employee->phone;
                $employee->address = $request->address ?? $employee->address;
                $employee->save();
            }
        }
    }
}