<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * @var array<string, array<int, string>>
     */
    private array $permissions = [
        'Student Management' => [
            'view-students', 
            'create-students', 
            'edit-students', 
            'delete-students', 
            'view-own-student'
        ],
        'Fee Management' => [
            'view-fees', 
            'collect-payments', 
            'view-fee-structure', 
            'edit-fee-structure', 
            'process-refunds'
        ],
        'Employee Management' => [
            'view-employees', 
            'create-employees', 
            'edit-employees', 
            'delete-employees'
        ],
        'Salary Management' => [
            'view-salaries', 
            'process-salaries', 
            'view-own-salary'
        ],
        'Loan Management' => [
            'view-loans', 
            'approve-loans', 
            'record-loan-payments', 
            'view-own-loan',
            'apply-loans'
        ],
        'Overdraft Management' => [
            'view-overdrafts', 
            'approve-overdrafts', 
            'record-overdraft-transactions',
            'view-own-overdraft',
            'apply-overdrafts'
        ],
        'Expense Management' => [
            'view-expenses', 
            'create-expenses', 
            'approve-expenses', 
            'edit-expenses'
        ],
        'Asset Management' => [
            'view-assets', 
            'create-assets', 
            'edit-assets', 
            'delete-assets'
        ],
        'Income Management' => [
            'view-incomes', 
            'create-incomes'
        ],
        'Class Management' => [
            'view-classes', 
            'create-classes', 
            'edit-classes', 
            'delete-classes'
        ],
        'Subject Management' => [
            'view-subjects', 
            'create-subjects', 
            'edit-subjects', 
            'delete-subjects'
        ],
        'Exam Management' => [
            'view-exams', 
            'create-exams', 
            'edit-exams', 
            'delete-exams', 
            'publish-exams'
        ],
        'Result Management' => [
            'view-results', 
            'enter-results', 
            'publish-results', 
            'view-own-results'
        ],
        'Scores Entry' => [
            'enter-scores', 
            'view-scores', 
            'bulk-enter-scores', 
            'import-scores',
            'calculate-positions',
            'auto-fill-scores'
        ],
        'Report Cards' => [
            'generate-report-cards', 
            'view-report-cards', 
            'print-report-cards', 
            'download-report-cards'
        ],
        'Certificate Management' => [
            'view-certificates', 
            'create-certificates', 
            'delete-certificates', 
            'generate-certificates'
        ],
        'Reports' => [
            'view-financial-reports', 
            'view-fee-reports', 
            'view-salary-reports', 
            'view-pl-reports',
            'export-reports',
            'view-student-reports',
            'view-employee-reports'
        ],
        'Settings' => [
            'view-settings', 
            'edit-settings', 
            'manage-users', 
            'view-logs', 
            'manage-backups',
            'manage-permissions',
            'manage-roles'
        ],
        'Notifications' => [
            'view-notifications', 
            'send-notifications', 
            'manage-notifications'
        ],
        'Attendance' => [
            'view-attendance', 
            'record-attendance', 
            'manage-attendance'
        ],
        'Timetable' => [
            'view-timetables', 
            'create-timetables', 
            'edit-timetables', 
            'delete-timetables'
        ],
        'Dashboard' => [
            'view-dashboard'
        ],
        'Blog' => [
            'view-blog', 'create-blog-posts', 'approve-blog-posts', 'manage-blog'
        ]
    ];

    /**
     * @var array<string, array<int, string>|string>
     */
    private array $rolePermissions = [
        'super_admin' => '*',
        'admin' => [
            'view-students', 'create-students', 'edit-students', 'delete-students',
            'view-fees', 'collect-payments', 'view-fee-structure', 'edit-fee-structure',
            'view-employees', 'create-employees', 'edit-employees', 'delete-employees',
            'view-salaries', 'process-salaries',
            'view-loans', 'approve-loans', 'record-loan-payments',
            'view-overdrafts', 'approve-overdrafts', 'record-overdraft-transactions',
            'view-expenses', 'create-expenses', 'approve-expenses', 'edit-expenses',
            'view-assets', 'create-assets', 'edit-assets', 'delete-assets',
            'view-incomes', 'create-incomes',
            'view-classes', 'create-classes', 'edit-classes', 'delete-classes',
            'view-subjects', 'create-subjects', 'edit-subjects', 'delete-subjects',
            'view-exams', 'create-exams', 'edit-exams', 'delete-exams', 'publish-exams',
            'view-results', 'enter-results', 'publish-results',
            'enter-scores', 'view-scores', 'bulk-enter-scores', 'import-scores',
            'calculate-positions', 'auto-fill-scores',
            'generate-report-cards', 'view-report-cards', 'print-report-cards', 'download-report-cards',
            'view-certificates', 'create-certificates', 'delete-certificates', 'generate-certificates',
            'view-financial-reports', 'view-fee-reports', 'view-salary-reports', 'view-pl-reports',
            'export-reports', 'view-student-reports', 'view-employee-reports',
            'view-settings', 'view-logs',
            'view-notifications', 'send-notifications',
            'view-inquiries', 'create-inquiries', 'respond-inquiries',
            'view-attendance', 'record-attendance', 'manage-attendance',
            'view-timetables', 'create-timetables', 'edit-timetables', 'delete-timetables',
            'view-dashboard', 'view-blog', 'create-blog-posts', 'approve-blog-posts', 'manage-blog'
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
            'view-classes', 'view-subjects',
            'view-exams', 'view-results', 'enter-results',
            'enter-scores', 'view-scores', 'bulk-enter-scores', 'import-scores',
            'calculate-positions', 'auto-fill-scores',
            'generate-report-cards', 'view-report-cards', 'print-report-cards', 'download-report-cards',
            'view-certificates',
            'view-financial-reports', 'view-fee-reports', 'view-salary-reports', 'view-pl-reports',
            'export-reports',
            'view-notifications',
            'view-inquiries', 'respond-inquiries',
            'view-attendance',
            'view-timetables', 'view-blog', 'create-blog-posts', 'approve-blog-posts', 'manage-blog',
            'view-dashboard'
        ],
        'teacher' => [
            'view-own-student',
            'view-fees',
            'create-expenses',
            'view-own-salary',
            'view-own-loan', 'apply-loans', 'view-own-overdraft', 'apply-overdrafts',
            'view-classes',
            'view-subjects',
            'view-exams',
            'view-own-results',
            'enter-results',
            'view-scores',
            'view-report-cards',
            'view-certificates',
            'view-notifications',
            'view-inquiries', 'create-inquiries',
            'view-blog', 'create-blog-posts',
            'view-attendance',
            'view-timetables',
            'view-dashboard'
        ],
        'frontdesk' => [
            'view-students', 'create-students', 'edit-students',
            'view-fees', 'collect-payments', 'view-fee-structure',
            'view-fee-reports',
            'view-classes',
            'view-subjects',
            'view-exams',
            'view-results',
            'view-report-cards',
            'view-certificates',
            'view-notifications',
            'view-inquiries', 'create-inquiries', 'respond-inquiries',
            'view-blog', 'create-blog-posts', 'approve-blog-posts', 'manage-blog',
            'view-attendance',
            'view-timetables',
            'view-dashboard'
        ],
        'parent' => [
            'view-own-student',
            'view-fees',
            'view-fee-structure',
            'view-exams',
            'view-own-results',
            'view-report-cards',
            'view-certificates',
            'view-notifications',
            'view-inquiries', 'create-inquiries',
            'view-dashboard'
        ],
        'student' => [
            'view-own-student',
            'view-fees',
            'view-own-results',
            'view-report-cards',
            'view-certificates',
            'view-notifications',
            'view-inquiries', 'create-inquiries',
            'view-dashboard'
        ],
    ];

    public function run(): void
    {
        $now = now();

        // Insert permissions
        foreach ($this->permissions as $module => $slugs) {
            foreach ($slugs as $slug) {
                DB::table('permissions')->updateOrInsert(
                    ['slug' => $slug],
                    [
                        'name' => $this->formatPermissionName($slug),
                        'module' => $module,
                        'description' => $this->formatPermissionName($slug) . ' permission for ' . $module,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }

        // Get all permission IDs by slug
        $permissionIds = DB::table('permissions')->pluck('id', 'slug');
        $allPermissions = $permissionIds->keys()->all();

        // Assign permissions to roles
        foreach ($this->rolePermissions as $role => $slugs) {
            // Handle wildcard for super_admin
            if ($slugs === '*') {
                $roleSlugs = $allPermissions;
            } else {
                $roleSlugs = $slugs;
            }

            // Ensure we only assign permissions that exist
            $validSlugs = array_intersect($roleSlugs, $allPermissions);

            foreach ($validSlugs as $slug) {
                if (isset($permissionIds[$slug])) {
                    DB::table('role_permissions')->updateOrInsert(
                        [
                            'role' => $role,
                            'permission_id' => $permissionIds[$slug]
                        ],
                        [
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]
                    );
                }
            }
        }

        // Output summary
        $this->command->info('Permissions seeded successfully!');
        $this->command->info('Total permissions: ' . count($allPermissions));
        $this->command->info('Total roles: ' . count($this->rolePermissions));
    }

    /**
     * Format permission name from slug
     */
    private function formatPermissionName(string $slug): string
    {
        return Str::headline(str_replace('-', ' ', $slug));
    }

    /**
     * Get all permissions grouped by module (useful for debugging)
     */
    public function getPermissionsByModule(): array
    {
        $result = [];
        foreach ($this->permissions as $module => $slugs) {
            $result[$module] = $slugs;
        }
        return $result;
    }

    /**
     * Get role permissions (useful for debugging)
     */
    public function getRolePermissions(): array
    {
        return $this->rolePermissions;
    }
}