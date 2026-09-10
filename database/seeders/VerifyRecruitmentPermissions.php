<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VerifyRecruitmentPermissions extends Seeder
{
    public function run(): void
    {
        $this->command?->info('=== VerifyRecruitmentPermissions ===');

        $requiredPermissionSlugs = [
            'manage-recruitment',
            'manage-applications',
            'view-applications',
            'manage-application-config',
            'view-settings',
            'edit-settings',
        ];

        $requiredRoles = ['super_admin', 'admin'];

        $permRows = DB::table('permissions')
            ->whereIn('name', $requiredPermissionSlugs)
            ->get(['id', 'name']);

        $permFound = $permRows->pluck('name')->all();
        $permMap = $permRows->keyBy('name');

        $this->command?->info('\n--- Permissions existence ---');
        foreach ($requiredPermissionSlugs as $slug) {
            $exists = $permMap->has($slug);
            $this->command?->info(sprintf('%s : %s', $slug, $exists ? '✅ FOUND' : '❌ MISSING'));
        }

        $this->command?->info('\n--- Role => Permission assignments ---');
        foreach ($requiredRoles as $roleName) {
            $role = DB::table('roles')->where('name', $roleName)->first(['id', 'name']);
            if (! $role) {
                $this->command?->error("Role not found in roles table: {$roleName}");
                continue;
            }

            $assigned = DB::table('role_permissions')
                ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                ->where('role_permissions.role_id', $role->id)
                ->whereIn('permissions.name', $requiredPermissionSlugs)
                ->pluck('permissions.name')
                ->all();

            $assignedSet = array_flip($assigned);

            $this->command?->info("\nRole: {$roleName} (id={$role->id})");
            foreach ($requiredPermissionSlugs as $slug) {
                $has = isset($assignedSet[$slug]);
                $this->command?->info(sprintf('  %s : %s', $slug, $has ? '✅' : '❌'));
            }
        }

        // Also check what the Gate layer defines (from code this is static, but we can validate that middleware slugs exist in DB)
        $this->command?->info('\n=== VerifyRecruitmentPermissions complete ===');
    }
}

