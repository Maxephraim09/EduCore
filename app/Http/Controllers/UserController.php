<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', (bool) $request->status);
        }

        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $users = $query->paginate(15)->appends($request->query());
        $roles = User::getAvailableRoles();
        $stats = $this->getUserStats();

        return view('user-management.index', compact('users', 'roles', 'stats'));
    }

    public function create()
    {
        $roles = User::getAvailableRoles();

        return view('user-management.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:' . implode(',', User::getAvailableRoles()),
            'address' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
            'send_welcome' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $password = $request->filled('password') ? $request->password : Str::random(10);
            $validated['password'] = Hash::make($password);
            $validated['is_active'] = $request->boolean('is_active', true);
            $validated['address'] = $request->address;
            $validated['phone'] = $request->phone;

            if ($request->hasFile('avatar')) {
                $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $user = User::create($validated);
            $this->createRelatedProfile($user, $request);

            if ($request->boolean('send_welcome')) {
                $this->sendWelcomeEmail($user, $password);
            }

            DB::commit();

            return redirect()->route('users.index')->with('success', 'User created successfully. Password: ' . $password);
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    public function show(User $user)
    {
        $relatedProfile = $this->getRelatedProfile($user);
        $activityLogs = $user->activityLogs()->latest()->limit(10)->get();

        return view('user-management.show', compact('user', 'relatedProfile', 'activityLogs'));
    }

    public function edit(User $user)
    {
        $roles = User::getAvailableRoles();
        $relatedProfile = $this->getRelatedProfile($user);

        return view('user-management.edit', compact('user', 'roles', 'relatedProfile'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->id === auth()->id() && $request->role !== $user->role) {
            return redirect()->back()->with('error', 'You cannot change your own role.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:' . implode(',', User::getAvailableRoles()),
            'address' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $validated['is_active'] = $request->boolean('is_active', $user->is_active);
            $user->update($validated);
            $this->updateRelatedProfile($user, $request);

            DB::commit();

            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    public function deactivate(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        if ($user->role === 'super_admin') {
            return redirect()->back()->with('error', 'Cannot deactivate Super Admin.');
        }

        $user->update(['is_active' => false]);

        return redirect()->route('users.index')->with('success', 'User deactivated successfully.');
    }

    public function activate(User $user)
    {
        $user->update(['is_active' => true]);

        return redirect()->route('users.index')->with('success', 'User activated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->role === 'super_admin') {
            return redirect()->back()->with('error', 'Cannot delete Super Admin.');
        }

        DB::beginTransaction();

        try {
            $this->deleteRelatedProfile($user);
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->delete();

            DB::commit();

            return redirect()->route('users.index')->with('success', 'User deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user->trashed()) {
            $user->restore();

            return redirect()->route('users.index')->with('success', 'User restored successfully.');
        }

        return redirect()->route('users.index')->with('error', 'User is not deleted.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'action' => 'required|in:activate,deactivate,delete',
        ]);

        $count = 0;
        foreach ($request->user_ids as $id) {
            $user = User::find($id);
            if (! $user || $user->id === auth()->id() || $user->role === 'super_admin') {
                continue;
            }

            switch ($request->action) {
                case 'activate':
                    $user->update(['is_active' => true]);
                    break;
                case 'deactivate':
                    $user->update(['is_active' => false]);
                    break;
                case 'delete':
                    $user->delete();
                    break;
            }
            $count++;
        }

        return redirect()->route('users.index')->with('success', "{$count} users processed successfully.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update(['password' => Hash::make($request->password)]);
        $this->sendPasswordResetEmail($user, $request->password);

        return redirect()->route('users.index')->with('success', 'Password reset successfully.');
    }

    public function profile()
    {
        $user = auth()->user();

        return view('user-management.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();
        if (! Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('profile')->with('success', 'Password updated successfully.');
    }

    public function updateSecurity(Request $request)
    {
        $user = auth()->user();
        $user->update([
            'two_factor_enabled' => $request->boolean('two_factor_enabled'),
            'notification_preferences' => $request->input('notification_preferences', []),
        ]);

        return redirect()->route('profile')->with('success', 'Security settings updated successfully.');
    }

    private function getUserStats()
    {
        return [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'by_role' => User::select('role', DB::raw('count(*) as count'))->groupBy('role')->pluck('count', 'role')->toArray(),
        ];
    }

    private function createRelatedProfile(User $user, Request $request)
    {
        switch ($user->role) {
            case 'student':
                $student = Student::where('email', $user->email)->first();
                if ($student) {
                    $student->update(['user_id' => $user->id]);
                }
                break;
            case 'teacher':
            case 'admin':
            case 'accountant':
            case 'frontdesk':
                Employee::create([
                    'user_id' => $user->id,
                    'first_name' => explode(' ', $user->name)[0] ?? '',
                    'last_name' => explode(' ', $user->name)[1] ?? '',
                    'email' => $user->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'position' => ucfirst($user->role),
                    'is_active' => true,
                ]);
                break;
        }
    }

    private function updateRelatedProfile(User $user, Request $request)
    {
        if ($user->role === 'student') {
            $student = $user->student()->first();
            if ($student) {
                $student->update([
                    'email' => $user->email,
                    'phone' => $request->phone ?? $student->phone,
                    'address' => $request->address ?? $student->address,
                ]);
            }
        }

        if (in_array($user->role, ['teacher', 'admin', 'accountant', 'frontdesk'])) {
            $employee = $user->employee()->first();
            if ($employee) {
                $employee->update([
                    'email' => $user->email,
                    'phone' => $request->phone ?? $employee->phone,
                    'address' => $request->address ?? $employee->address,
                ]);
            }
        }
    }

    private function deleteRelatedProfile(User $user)
    {
        switch ($user->role) {
            case 'student':
                if ($user->student) {
                    $user->student->delete();
                }
                break;
            case 'teacher':
            case 'admin':
            case 'accountant':
            case 'frontdesk':
                if ($user->employee) {
                    $user->employee->delete();
                }
                break;
        }
    }

    private function getRelatedProfile(User $user)
    {
        switch ($user->role) {
            case 'student':
                return $user->student;
            case 'teacher':
            case 'admin':
            case 'accountant':
            case 'frontdesk':
                return $user->employee;
            default:
                return null;
        }
    }

    private function sendWelcomeEmail(User $user, string $password): void
    {
        try {
            Mail::to($user->email)->send(new \App\Mail\UserWelcome($user, $password));
        } catch (\Throwable $e) {
            \Log::error('Failed to send welcome email: ' . $e->getMessage());
        }
    }

    private function sendPasswordResetEmail(User $user, string $password): void
    {
        try {
            Mail::to($user->email)->send(new \App\Mail\PasswordReset($user, $password));
        } catch (\Throwable $e) {
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
        }
    }
}
