<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'student_id',
        'parent_id',
        'is_active',
        'is_verified',
        'last_login_at',
        'avatar',
        'bio',
        'phone',
        'address',
        'two_factor_enabled',
        'notification_preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'last_login_at' => 'datetime',
            'two_factor_enabled' => 'boolean',
            'notification_preferences' => 'array',
        ];
    }

    // ==================== ROLE CONSTANTS ====================
    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_ACCOUNTANT = 'accountant';
    public const ROLE_TEACHER = 'teacher';
    public const ROLE_FRONTDESK = 'frontdesk';
    public const ROLE_PARENT = 'parent';
    public const ROLE_STUDENT = 'student';

    public const ROLES = [
        self::ROLE_SUPER_ADMIN => 'Super Admin',
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_ACCOUNTANT => 'Accountant',
        self::ROLE_TEACHER => 'Teacher',
        self::ROLE_FRONTDESK => 'Front Desk',
        self::ROLE_PARENT => 'Parent',
        self::ROLE_STUDENT => 'Student',
    ];

    public const ROLE_LIST = [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_ADMIN,
        self::ROLE_ACCOUNTANT,
        self::ROLE_TEACHER,
        self::ROLE_FRONTDESK,
        self::ROLE_PARENT,
        self::ROLE_STUDENT,
    ];

    public static function getAvailableRoles(): array
    {
        return self::ROLE_LIST;
    }

    public static function getRoleLabel(string $role): string
    {
        return self::ROLES[$role] ?? ucfirst(str_replace('_', ' ', $role));
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the student associated with this user (for student role)
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    /**
     * Get the parent user (for student role)
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the children students (for parent role)
     */
    public function children()
    {
        return $this->belongsToMany(Student::class, 'parent_students', 'parent_id', 'student_id')
            ->withPivot(['relationship', 'is_primary'])
            ->withTimestamps();
    }

    /**
     * Get all activity logs for this user
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get the notifications for this user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadNotificationsCountAttribute()
    {
        return $this->notifications()->where('is_read', false)->count();
    }

    // ==================== ROLE CHECKING METHODS ====================

    /**
     * Check if user has a specific role
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles, true);
        }
        return $this->role === $roles;
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Check if user is admin or higher
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN || $this->isSuperAdmin();
    }

    /**
     * Check if user is accountant or higher
     */
    public function isAccountant(): bool
    {
        return $this->role === self::ROLE_ACCOUNTANT || $this->isAdmin();
    }

    /**
     * Check if user is teacher or higher
     */
    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER || $this->isAdmin();
    }

    /**
     * Check if user is front desk or higher
     */
    public function isFrontDesk(): bool
    {
        return $this->role === self::ROLE_FRONTDESK || $this->isAdmin();
    }

    /**
     * Check if user is a parent
     */
    public function isParent(): bool
    {
        return $this->role === self::ROLE_PARENT;
    }

    /**
     * Check if user is a student
     */
    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if user is verified
     */
    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    // ==================== PERMISSION CHECKING METHODS ====================

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return DB::table('role_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('role_permissions.role', $this->role)
            ->where('permissions.slug', $permission)
            ->exists();
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all permissions for the user's role
     */
    public function getPermissions(): array
    {
        if ($this->isSuperAdmin()) {
            return DB::table('permissions')->pluck('slug')->toArray();
        }

        return DB::table('role_permissions')
            ->where('role', $this->role)
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->pluck('permissions.slug')
            ->toArray();
    }

    /**
     * Get all permissions grouped by module for the user's role
     */
    public function getPermissionsByModule(): array
    {
        if ($this->isSuperAdmin()) {
            $permissions = DB::table('permissions')->get();
            return $permissions->groupBy('module')->map(function ($items) {
                return $items->pluck('slug')->toArray();
            })->toArray();
        }

        $permissions = DB::table('role_permissions')
            ->where('role', $this->role)
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->get();

        return $permissions->groupBy('module')->map(function ($items) {
            return $items->pluck('slug')->toArray();
        })->toArray();
    }

    // ==================== STUDENT VIEW AUTHORIZATION ====================

    /**
     * Check if user can view a specific student
     */
    public function canViewStudent(int $studentId): bool
    {
        // Admin and front desk can view all students
        if ($this->isAdmin() || $this->isFrontDesk() || $this->isAccountant()) {
            return true;
        }

        // Teacher can view students in their class (implement based on your teacher-class relationship)
        if ($this->isTeacher()) {
            return true;
        }

        // Parent can view their own children
        if ($this->isParent()) {
            return $this->children()->where('student_id', $studentId)->exists();
        }

        // Student can view only themselves
        if ($this->isStudent()) {
            return $this->student_id == $studentId;
        }

        return false;
    }

    /**
     * Check if user can edit a specific student
     */
    public function canEditStudent(int $studentId): bool
    {
        // Only admin and front desk can edit students
        if ($this->isAdmin() || $this->isFrontDesk()) {
            return true;
        }

        return false;
    }

    // ==================== ACCESSORS ====================

    /**
     * Get the user's role name
     */
    public function getRoleNameAttribute(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    /**
     * Get the user's initials
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return substr($initials, 0, 2);
    }

    /**
     * Get the user's avatar URL
     * Safe version that checks if avatar column exists
     */
    public function getAvatarAttribute(): string
    {
        // Check if the avatar column exists in the table
        $hasAvatarColumn = Schema::hasColumn('users', 'avatar');
        
        // If column exists and user has an avatar, use it
        if ($hasAvatarColumn && isset($this->attributes['avatar']) && !empty($this->attributes['avatar'])) {
            $avatar = $this->attributes['avatar'];
            // Check if it's a full URL or path
            if (filter_var($avatar, FILTER_VALIDATE_URL)) {
                return $avatar;
            }
            return asset('storage/' . $avatar);
        }

        // Generate UI avatar with initials as fallback
        $color = $this->getAvatarColor($this->id ?? 1);
        $name = urlencode($this->name ?? 'User');
        return "https://ui-avatars.com/api/?name={$name}&size=100&background=667eea&color=fff&rounded=true&bold=true";
    }

    /**
     * Get the user's avatar URL (alias for consistency)
     */
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar;
    }

    /**
     * Check if user has an avatar
     */
    public function hasAvatar(): bool
    {
        $hasAvatarColumn = Schema::hasColumn('users', 'avatar');
        return $hasAvatarColumn && isset($this->attributes['avatar']) && !empty($this->attributes['avatar']);
    }

    /**
     * Get a color based on user ID for avatar
     */
    private function getAvatarColor(int $id): string
    {
        $colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b', '#fa709a', '#fee140', '#11998e'];
        return $colors[$id % count($colors)];
    }

    // ==================== SCOPES ====================

    /**
     * Scope a query to only include active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include verified users
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to only include users by role
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope a query to only include users by roles
     */
    public function scopeByRoles($query, array $roles)
    {
        return $query->whereIn('role', $roles);
    }

    // ==================== UTILITY METHODS ====================

    /**
     * Get all users by role
     */
    public static function getUsersByRole(string $role)
    {
        return self::where('role', $role)->active()->get();
    }

    /**
     * Get all users by roles
     */
    public static function getUsersByRoles(array $roles)
    {
        return self::whereIn('role', $roles)->active()->get();
    }

    /**
     * Get the redirect route based on user role after login
     */
    public function getRedirectRoute(): string
    {
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return route('dashboard');
        } elseif ($this->isAccountant()) {
            return route('fee-payments.index');
        } elseif ($this->isTeacher()) {
            return route('students.index');
        } elseif ($this->isFrontDesk()) {
            return route('students.index');
        } elseif ($this->isParent()) {
            return route('dashboard.parent');
        } elseif ($this->isStudent()) {
            return route('dashboard.student');
        }

        return route('dashboard');
    }

    /**
     * Get dashboard route name for the user's role
     */
    public function getDashboardRoute(): string
    {
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return 'dashboard';
        } elseif ($this->isAccountant()) {
            return 'fee-payments.index';
        } elseif ($this->isTeacher()) {
            return 'students.index';
        } elseif ($this->isFrontDesk()) {
            return 'students.index';
        } elseif ($this->isParent()) {
            return 'dashboard.parent';
        } elseif ($this->isStudent()) {
            return 'dashboard.student';
        }

        return 'dashboard';
    }

    /**
     * Get the user's full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get the user's display name with role
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name . ' (' . $this->role_name . ')';
    }
}
