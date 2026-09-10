## School Settings & Receipt Verification

The application supports configurable school details that are shown on receipts and public pages. Set these via the admin Settings UI or seed values in the database (table: `system_settings`). Key settings:

- `school_name` — the display name used on receipts.
- `school_tagline` — short tagline shown beneath the name.
- `school_address` — postal address shown on receipts.
- `school_phone` — contact phone number.
- `school_email` — contact email.
- `school_website` — optional website URL.
- `school_logo_url` — absolute URL or path to the logo image. If not set, `public/assets/logo.svg` is used as a fallback.

To add a custom logo, place the image in `public/uploads/` or any public path and set `school_logo_url` to the URL (for example `/uploads/logo.png`).

The payment receipt QR code links to a public verification page: `/verify/receipt/{id}`. Scanning it opens a verification page with minimal receipt details and a link to view the full receipt if needed.

# Authentication and Role-Based Access Control (RBAC) Implementation

## What's Needed for Each Role

Based on your Financial Management System, here's the complete breakdown of what each role should access:

### 1. **Super Admin**
*Full system control with no restrictions*

**Access:**
- ✅ All modules (Students, Fees, Employees, Salaries, Loans, Overdrafts, Expenses, Assets, Incomes)
- ✅ All Reports (Financial, Fee Collection, Salary, P&L)
- ✅ System Settings (Paystack, SMS, Email, School, Backup, Logs)
- ✅ User Management (Create/Edit/Delete all users)
- ✅ Role & Permission Management
- ✅ System Configuration
- ✅ View all data across the system
- ✅ Backup & Restore operations
- ✅ Audit Logs

### 2. **Admin**
*Day-to-day operational management*

**Access:**
- ✅ Students Management (CRUD operations)
- ✅ Fee Payments (View, Collect, Record payments)
- ✅ Fee Structure Management
- ✅ Employees Management (CRUD operations)
- ✅ Salary Processing (Process, View, Edit pending)
- ✅ Staff Loans (Approve, View, Manage)
- ✅ Staff Overdrafts (Approve, View, Manage)
- ✅ Expenses Management (CRUD, Approve/Reject)
- ✅ Assets Management (CRUD, View depreciation)
- ✅ Other Incomes (CRUD)
- ✅ All Reports
- ❌ System Settings (View only, cannot modify)
- ❌ User Management (Cannot create/edit/delete users)
- ❌ Backup & Restore

### 3. **Accountant**
*Financial operations focus*

**Access:**
- ✅ Fee Payments (Collect, View history, Generate receipts)
- ✅ Fee Structure (View only, cannot edit)
- ✅ Salary Processing (Process salaries, View history)
- ✅ Staff Loans (Record payments, View loan status)
- ✅ Staff Overdrafts (Record withdrawals/repayments, View status)
- ✅ Expenses (Create, View, Submit for approval)
- ✅ Other Incomes (Create, View)
- ✅ Financial Reports (View all)
- ❌ Students Management (View only basic info)
- ❌ Employees Management (View only)
- ❌ Assets Management (View only)
- ❌ Cannot approve expenses (submit only)
- ❌ Cannot modify fee structure
- ❌ Cannot approve loans/overdrafts

### 4. **Teacher**
*Academic staff with limited access*

**Access:**
- ✅ View assigned students (class-specific)
- ✅ View student fee status (can see if paid but not amounts)
- ✅ Submit expense requests (classroom materials, activities)
- ✅ View own salary details
- ✅ View own loan/overdraft status
- ✅ View school announcements
- ❌ Cannot process fee payments
- ❌ Cannot manage other employees
- ❌ Cannot access financial reports
- ❌ Cannot manage system settings

### 5. **Frontdesk**
*Student and parent interaction focus*

**Access:**
- ✅ Students Management (Create, View, Edit basic info)
- ✅ Fee Payments (Collect payments, Print receipts)
- ✅ View Fee Structure (for answering parent queries)
- ✅ Generate student reports (fee status, attendance)
- ✅ Parent communication
- ❌ Cannot modify fee structure
- ❌ Cannot process refunds without approval
- ❌ Cannot access employee data
- ❌ Cannot view salary information
- ❌ Cannot access financial reports

### 6. **Parent**
*View their child(ren)'s information only*

**Access:**
- ✅ View own child's profile
- ✅ View child's fee payment history
- ✅ View fee structure
- ✅ Make online payments (via Paystack)
- ✅ Download receipts
- ✅ View child's academic progress (if available)
- ✅ Submit queries/complaints
- ❌ Cannot view other students' information
- ❌ Cannot access financial data
- ❌ Cannot access employee data
- ❌ Cannot modify system data

### 7. **Student**
*View own information only*

**Access:**
- ✅ View own profile
- ✅ View own fee payment history
- ✅ View own receipts
- ❌ Cannot make payments (should use parent account)
- ❌ Cannot view other students' information
- ❌ Cannot modify any system data

## Implementation Plan

Now I'll implement the complete authentication system with these roles. Here's the structure:

### Database Changes Needed:

1. **Add role column to users table**
2. **Create student_user relationship table** (for parent-student linking)
3. **Create permissions table and role_permission table**
4. **Create activity_logs table** for audit trail

Let me create the complete implementation:

### Step 1: Create Migration for Role and Permissions

```bash
php artisan make:migration add_role_and_permissions_tables
```

**database/migrations/xxxx_add_role_and_permissions_tables.php**:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add role to users table
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'admin', 'accountant', 'teacher', 'frontdesk', 'parent', 'student'])
                  ->default('student')->after('password');
            $table->foreignId('student_id')->nullable()->constrained()->after('role');
            $table->foreignId('parent_id')->nullable()->constrained('users')->after('student_id');
            $table->boolean('is_verified')->default(false)->after('is_active');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
        });

        // Permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('module');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Role permissions table
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['super_admin', 'admin', 'accountant', 'teacher', 'frontdesk', 'parent', 'student']);
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['role', 'permission_id']);
        });

        // Activity logs table
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action');
            $table->string('module');
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('module');
        });

        // Parent-Student relationship table (if not exists)
        if (!Schema::hasTable('parent_students')) {
            Schema::create('parent_students', function (Blueprint $table) {
                $table->id();
                $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->string('relationship')->nullable();
                $table->boolean('is_primary')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'student_id', 'parent_id', 'is_verified', 'last_login_at']);
        });
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('parent_students');
    }
};
```

Run the migration:
```bash
php artisan migrate
```

### Step 2: Create Permission Seeder

```bash
php artisan make:seeder PermissionSeeder
```

**database/seeders/PermissionSeeder.php**:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Student Management
            ['name' => 'View Students', 'slug' => 'view-students', 'module' => 'students'],
            ['name' => 'Create Students', 'slug' => 'create-students', 'module' => 'students'],
            ['name' => 'Edit Students', 'slug' => 'edit-students', 'module' => 'students'],
            ['name' => 'Delete Students', 'slug' => 'delete-students', 'module' => 'students'],
            ['name' => 'View Own Student', 'slug' => 'view-own-student', 'module' => 'students'],
            
            // Fee Management
            ['name' => 'View Fees', 'slug' => 'view-fees', 'module' => 'fees'],
            ['name' => 'Collect Payments', 'slug' => 'collect-payments', 'module' => 'fees'],
            ['name' => 'View Fee Structure', 'slug' => 'view-fee-structure', 'module' => 'fees'],
            ['name' => 'Edit Fee Structure', 'slug' => 'edit-fee-structure', 'module' => 'fees'],
            ['name' => 'Process Refunds', 'slug' => 'process-refunds', 'module' => 'fees'],
            
            // Employee Management
            ['name' => 'View Employees', 'slug' => 'view-employees', 'module' => 'employees'],
            ['name' => 'Create Employees', 'slug' => 'create-employees', 'module' => 'employees'],
            ['name' => 'Edit Employees', 'slug' => 'edit-employees', 'module' => 'employees'],
            ['name' => 'Delete Employees', 'slug' => 'delete-employees', 'module' => 'employees'],
            
            // Salary Management
            ['name' => 'View Salaries', 'slug' => 'view-salaries', 'module' => 'salaries'],
            ['name' => 'Process Salaries', 'slug' => 'process-salaries', 'module' => 'salaries'],
            ['name' => 'View Own Salary', 'slug' => 'view-own-salary', 'module' => 'salaries'],
            
            // Loan Management
            ['name' => 'View Loans', 'slug' => 'view-loans', 'module' => 'loans'],
            ['name' => 'Approve Loans', 'slug' => 'approve-loans', 'module' => 'loans'],
            ['name' => 'Record Loan Payments', 'slug' => 'record-loan-payments', 'module' => 'loans'],
            ['name' => 'View Own Loan', 'slug' => 'view-own-loan', 'module' => 'loans'],
            
            // Overdraft Management
            ['name' => 'View Overdrafts', 'slug' => 'view-overdrafts', 'module' => 'overdrafts'],
            ['name' => 'Approve Overdrafts', 'slug' => 'approve-overdrafts', 'module' => 'overdrafts'],
            ['name' => 'Record Overdraft Transactions', 'slug' => 'record-overdraft-transactions', 'module' => 'overdrafts'],
            
            // Expense Management
            ['name' => 'View Expenses', 'slug' => 'view-expenses', 'module' => 'expenses'],
            ['name' => 'Create Expenses', 'slug' => 'create-expenses', 'module' => 'expenses'],
            ['name' => 'Approve Expenses', 'slug' => 'approve-expenses', 'module' => 'expenses'],
            ['name' => 'Edit Expenses', 'slug' => 'edit-expenses', 'module' => 'expenses'],
            
            // Asset Management
            ['name' => 'View Assets', 'slug' => 'view-assets', 'module' => 'assets'],
            ['name' => 'Create Assets', 'slug' => 'create-assets', 'module' => 'assets'],
            ['name' => 'Edit Assets', 'slug' => 'edit-assets', 'module' => 'assets'],
            ['name' => 'Delete Assets', 'slug' => 'delete-assets', 'module' => 'assets'],
            
            // Income Management
            ['name' => 'View Incomes', 'slug' => 'view-incomes', 'module' => 'incomes'],
            ['name' => 'Create Incomes', 'slug' => 'create-incomes', 'module' => 'incomes'],
            
            // Reports
            ['name' => 'View Financial Reports', 'slug' => 'view-financial-reports', 'module' => 'reports'],
            ['name' => 'View Fee Reports', 'slug' => 'view-fee-reports', 'module' => 'reports'],
            ['name' => 'View Salary Reports', 'slug' => 'view-salary-reports', 'module' => 'reports'],
            ['name' => 'View P&L Reports', 'slug' => 'view-pl-reports', 'module' => 'reports'],
            
            // Settings
            ['name' => 'View Settings', 'slug' => 'view-settings', 'module' => 'settings'],
            ['name' => 'Edit Settings', 'slug' => 'edit-settings', 'module' => 'settings'],
            ['name' => 'Manage Users', 'slug' => 'manage-users', 'module' => 'settings'],
            ['name' => 'View Logs', 'slug' => 'view-logs', 'module' => 'settings'],
            ['name' => 'Manage Backups', 'slug' => 'manage-backups', 'module' => 'settings'],
        ];

        DB::table('permissions')->insert($permissions);

        // Assign permissions to roles
        $rolePermissions = [
            'super_admin' => array_column($permissions, 'slug'),
            'admin' => [
                'view-students', 'create-students', 'edit-students',
                'view-fees', 'collect-payments', 'view-fee-structure',
                'view-employees', 'create-employees', 'edit-employees',
                'view-salaries', 'process-salaries',
                'view-loans', 'approve-loans', 'record-loan-payments',
                'view-overdrafts', 'approve-overdrafts', 'record-overdraft-transactions',
                'view-expenses', 'create-expenses', 'approve-expenses', 'edit-expenses',
                'view-assets', 'create-assets', 'edit-assets',
                'view-incomes', 'create-incomes',
                'view-financial-reports', 'view-fee-reports', 'view-salary-reports', 'view-pl-reports',
                'view-settings',
            ],
            'accountant' => [
                'view-students',
                'view-fees', 'collect-payments', 'view-fee-structure',
                'view-employees',
                'view-salaries', 'process-salaries',
                'view-loans', 'record-loan-payments',
                'view-overdrafts', 'record-overdraft-transactions',
                'view-expenses', 'create-expenses',
                'view-assets',
                'view-incomes', 'create-incomes',
                'view-financial-reports', 'view-fee-reports', 'view-salary-reports', 'view-pl-reports',
            ],
            'teacher' => [
                'view-own-student',
                'view-fee-structure',
                'view-own-salary',
                'view-own-loan',
                'create-expenses',
            ],
            'frontdesk' => [
                'view-students', 'create-students', 'edit-students',
                'view-fees', 'collect-payments', 'view-fee-structure',
                'view-fee-reports',
            ],
            'parent' => [
                'view-own-student',
                'view-fees', 'collect-payments', 'view-fee-structure',
            ],
            'student' => [
                'view-own-student',
                'view-fees',
            ],
        ];

        foreach ($rolePermissions as $role => $permissions) {
            foreach ($permissions as $permissionSlug) {
                $permissionId = DB::table('permissions')->where('slug', $permissionSlug)->value('id');
                if ($permissionId) {
                    DB::table('role_permissions')->insert([
                        'role' => $role,
                        'permission_id' => $permissionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
```

### Step 3: Create User Model with Role Methods

Update `app/Models/User.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'student_id', 'parent_id', 'is_active', 'is_verified'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // Role constants
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_ACCOUNTANT = 'accountant';
    const ROLE_TEACHER = 'teacher';
    const ROLE_FRONTDESK = 'frontdesk';
    const ROLE_PARENT = 'parent';
    const ROLE_STUDENT = 'student';

    const ROLES = [
        self::ROLE_SUPER_ADMIN => 'Super Admin',
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_ACCOUNTANT => 'Accountant',
        self::ROLE_TEACHER => 'Teacher',
        self::ROLE_FRONTDESK => 'Front Desk',
        self::ROLE_PARENT => 'Parent',
        self::ROLE_STUDENT => 'Student',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children()
    {
        return $this->belongsToMany(Student::class, 'parent_students', 'parent_id', 'student_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Role checking methods
    public function isSuperAdmin()
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN || $this->isSuperAdmin();
    }

    public function isAccountant()
    {
        return $this->role === self::ROLE_ACCOUNTANT || $this->isAdmin();
    }

    public function isTeacher()
    {
        return $this->role === self::ROLE_TEACHER || $this->isAdmin();
    }

    public function isFrontDesk()
    {
        return $this->role === self::ROLE_FRONTDESK || $this->isAdmin();
    }

    public function isParent()
    {
        return $this->role === self::ROLE_PARENT;
    }

    public function isStudent()
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        return $this->role === $role || $this->isSuperAdmin();
    }

    public function hasPermission($permissionSlug)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return DB::table('role_permissions')
            ->where('role', $this->role)
            ->whereHas('permission', function($q) use ($permissionSlug) {
                $q->where('slug', $permissionSlug);
            })
            ->exists();
    }

    public function getRoleNameAttribute()
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    public function canViewStudent($studentId)
    {
        if ($this->isAdmin() || $this->isAccountant() || $this->isFrontDesk()) {
            return true;
        }

        if ($this->isTeacher()) {
            // Teacher can view students in their class
            $teacherClass = $this->employee->class ?? null;
            $studentClass = Student::find($studentId)->class ?? null;
            return $teacherClass === $studentClass;
        }

        if ($this->isParent()) {
            return $this->children()->where('student_id', $studentId)->exists();
        }

        if ($this->isStudent()) {
            return $this->student_id == $studentId;
        }

        return false;
    }
}
```

### Step 4: Create Activity Log Model

```bash
php artisan make:model ActivityLog
```

**app/Models/ActivityLog.php**:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'action', 'module', 'description', 'ip_address', 
        'user_agent', 'old_data', 'new_data'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log($action, $module, $description = null, $oldData = null, $newData = null)
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_data' => $oldData,
            'new_data' => $newData,
        ]);
    }
}
```

### Step 5: Create Role Middleware

```bash
php artisan make:middleware CheckRole
```

**app/Http/Middleware/CheckRole.php**:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!in_array(auth()->user()->role, $roles) && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
```

**app/Http/Middleware/CheckPermission.php**:

```bash
php artisan make:middleware CheckPermission
```

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->hasPermission($permission)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
```

### Step 6: Register Middleware

Update `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    // ... existing middleware
    'role' => \App\Http\Middleware\CheckRole::class,
    'permission' => \App\Http\Middleware\CheckPermission::class,
];
```

### Step 7: Create Auth Controllers

```bash
php artisan make:controller Auth/LoginController
php artisan make:controller Auth/RegisterController
php artisan make:controller Auth/ForgotPasswordController
php artisan make:controller Auth/ResetPasswordController
php artisan make:controller UserController
```

**app/Http/Controllers/Auth/LoginController.php**:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();
            
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account is deactivated.']);
            }

            // Update last login
            $user->update(['last_login_at' => now()]);
            
            // Log activity
            ActivityLog::log('login', 'auth', 'User logged in');

            // Redirect based on role
            if ($user->isSuperAdmin() || $user->isAdmin()) {
                return redirect()->intended(route('dashboard'));
            } elseif ($user->isAccountant()) {
                return redirect()->intended(route('fee-payments.index'));
            } elseif ($user->isTeacher()) {
                return redirect()->intended(route('students.index'));
            } elseif ($user->isFrontDesk()) {
                return redirect()->intended(route('students.index'));
            } elseif ($user->isParent()) {
                return redirect()->intended(route('parent.dashboard'));
            } elseif ($user->isStudent()) {
                return redirect()->intended(route('student.dashboard'));
            }
            
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        ActivityLog::log('logout', 'auth', 'User logged out');
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
```

**app/Http/Controllers/UserController.php**:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('manage-users');
        
        $users = User::with('student')->orderBy('created_at', 'desc')->paginate(15);
        $roles = User::ROLES;
        
        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $this->authorize('manage-users');
        
        $roles = User::ROLES;
        $students = Student::where('is_active', true)->get();
        
        return view('users.create', compact('roles', 'students'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage-users');
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:' . implode(',', array_keys(User::ROLES)),
            'student_id' => 'required_if:role,student|exists:students,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'student_id' => $request->role == 'student' ? $request->student_id : null,
            'is_active' => true,
            'is_verified' => true,
        ]);

        ActivityLog::log('create', 'users', "Created user: {$user->name}", null, $user->toArray());

        return redirect()->route('users.index')
            ->with('success', 'User created successfully!');
    }

    public function edit($id)
    {
        $this->authorize('manage-users');
        
        $user = User::findOrFail($id);
        $roles = User::ROLES;
        $students = Student::where('is_active', true)->get();
        
        return view('users.edit', compact('user', 'roles', 'students'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('manage-users');
        
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:' . implode(',', array_keys(User::ROLES)),
            'is_active' => 'boolean',
        ]);

        $oldData = $user->toArray();
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => $request->is_active ?? $user->is_active,
        ]);

        if ($request->password) {
            $request->validate(['password' => 'min:8|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        ActivityLog::log('update', 'users', "Updated user: {$user->name}", $oldData, $user->toArray());

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        $this->authorize('manage-users');
        
        $user = User::findOrFail($id);
        
        if ($user->id == auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        $user->delete();
        
        ActivityLog::log('delete', 'users', "Deleted user: {$user->name}", $user->toArray(), null);

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully!');
    }

    public function activityLogs()
    {
        $this->authorize('view-logs');
        
        $logs = ActivityLog::with('user')->orderBy('created_at', 'desc')->paginate(50);
        
        return view('users.activity-logs', compact('logs'));
    }
}
```

### Step 8: Create Authentication Views

**resources/views/auth/login.blade.php**:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Financial Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }
        .login-card {
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .login-body {
            padding: 30px;
            background: white;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card login-card">
                    <div class="login-header">
                        <i class="fas fa-chart-line fa-3x mb-3"></i>
                        <h3>Financial Management System</h3>
                        <p class="mb-0">Login to your account</p>
                    </div>
                    <div class="login-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="remember" id="remember">
                                <label class="form-check-label" for="remember">Remember Me</label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-login w-100">
                                <i class="fas fa-sign-in-alt me-2"></i> Login
                            </button>
                        </form>
                        <hr class="my-4">
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Demo credentials: admin@financialsystem.com / password
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
```

### Step 9: Update Routes

Update `routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // User Management (Super Admin only)
    Route::middleware(['role:super_admin'])->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/activity-logs', [UserController::class, 'activityLogs'])->name('activity-logs');
    });
    
    // Include all other module routes here with appropriate middleware
    // Students - Admin, Accountant, Front Desk
    Route::middleware(['permission:view-students'])->resource('students', StudentController::class);
    
    // Fee Payments - Admin, Accountant, Front Desk
    Route::middleware(['permission:collect-payments'])->prefix('fee-payments')->name('fee-payments.')->group(function () {
        Route::get('/', [FeePaymentController::class, 'index'])->name('index');
        Route::get('/create', [FeePaymentController::class, 'create'])->name('create');
        Route::post('/initialize', [FeePaymentController::class, 'initializePayment'])->name('initialize');
        Route::get('/callback', [FeePaymentController::class, 'handleCallback'])->name('callback');
        Route::get('/receipt/{id}', [FeePaymentController::class, 'showReceipt'])->name('receipt');
        Route::get('/history/{studentId?}', [FeePaymentController::class, 'paymentHistory'])->name('history');
    });
    
    // Expenses - All staff can create, only admin can approve
    Route::middleware(['permission:view-expenses'])->resource('expenses', ExpenseController::class);
    Route::middleware(['permission:approve-expenses'])->get('/expenses/{id}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
    
    // Reports - Accountants and above
    Route::middleware(['permission:view-financial-reports'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/fee-collection', [ReportController::class, 'feeCollection'])->name('fee-collection');
        Route::get('/salary', [ReportController::class, 'salaryReport'])->name('salary');
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
    });
    
    // Settings - Super Admin only
    Route::middleware(['permission:edit-settings'])->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/update-paystack', [SettingsController::class, 'updatePaystackKeys'])->name('update-paystack');
        Route::post('/update-sms', [SettingsController::class, 'updateSmsSettings'])->name('update-sms');
        Route::post('/update-email', [SettingsController::class, 'updateEmailSettings'])->name('update-email');
        Route::post('/update-school', [SettingsController::class, 'updateSchoolSettings'])->name('update-school');
        Route::get('/backup', [SettingsController::class, 'backup'])->name('backup');
        Route::post('/restore', [SettingsController::class, 'restore'])->name('restore');
        Route::get('/logs', [SettingsController::class, 'logs'])->name('logs');
    });
});
```

### Step 10: Run Seeders

```bash
php artisan db:seed --class=PermissionSeeder
```

### Step 11: Create Default Users

```bash
php artisan tinker
```

```php
// Create Super Admin
DB::table('users')->insert([
    'name' => 'Super Admin',
    'email' => 'superadmin@financialsystem.com',
    'password' => Hash::make('password'),
    'role' => 'super_admin',
    'is_active' => true,
    'is_verified' => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

// Create Admin
DB::table('users')->insert([
    'name' => 'Admin User',
    'email' => 'admin@financialsystem.com',
    'password' => Hash::make('password'),
    'role' => 'admin',
    'is_active' => true,
    'is_verified' => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

// Create Accountant
DB::table('users')->insert([
    'name' => 'Accountant User',
    'email' => 'accountant@financialsystem.com',
    'password' => Hash::make('password'),
    'role' => 'accountant',
    'is_active' => true,
    'is_verified' => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

// Create Teacher
DB::table('users')->insert([
    'name' => 'Teacher User',
    'email' => 'teacher@financialsystem.com',
    'password' => Hash::make('password'),
    'role' => 'teacher',
    'is_active' => true,
    'is_verified' => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

// Create Front Desk
DB::table('users')->insert([
    'name' => 'Front Desk User',
    'email' => 'frontdesk@financialsystem.com',
    'password' => Hash::make('password'),
    'role' => 'frontdesk',
    'is_active' => true,
    'is_verified' => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "Default users created successfully!\n";
exit;
```

### Step 12: Update Layout for Role-Based Menu

Update the sidebar in `resources/views/layouts/app.blade.php` to show menus based on role:

```blade
@php
    $user = Auth::user();
@endphp

@if($user->isAdmin() || $user->isAccountant() || $user->isFrontDesk())
    <!-- Show Students menu -->
@endif

@if($user->isAdmin() || $user->isAccountant())
    <!-- Show Financial menus -->
@endif

@if($user->isSuperAdmin())
    <!-- Show User Management menu -->
@endif
```

Now run:
```bash
php artisan migrate:fresh --seed
php artisan serve
```

The complete authentication system with Role-Based Access Control is now implemented! Each role has specific permissions as outlined above.


implemente a blog system ito the existing proect
* admin and front dessk officer 
- can create blog actegories
- create a blog post
- approve a blog post (post by teachers)
- manage blog
- comments
- views
- tags
* teachers can 
- submit blog post
- view submited blog (can see the views and total comment if approved)

crate a public blog page and a sinle blog page 