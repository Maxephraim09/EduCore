<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $permissionNames = [
            'view-inquiries',
            'create-inquiries',
            'respond-inquiries',
            'view-visitors',
            'create-visitors',
            'checkout-visitors',
        ];

        foreach ($permissionNames as $name) {
            $exists = DB::table('permissions')->where('slug', $name)->orWhere('name', $name)->exists();
            if ($exists) {
                continue;
            }

            // Your permissions schema seems to use slug/module/description in some places.
            DB::table('permissions')->insert([
                'name' => $name,
                'slug' => $name,
                'module' => 'inquiries-visitors',
                'description' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Attach to roles: frontdesk & admin (best-effort)
        $roleNames = ['frontdesk', 'admin'];
        $permissionSlugs = $permissionNames;

        $roleRows = DB::table('roles')->whereIn('name', $roleNames)->get();
        if ($roleRows->isEmpty()) {
            return;
        }

        $permRows = DB::table('permissions')->whereIn('slug', $permissionSlugs)->get();
        if ($permRows->isEmpty()) {
            return;
        }

        foreach ($roleRows as $role) {
            foreach ($permRows as $perm) {
                $already = DB::table('role_permissions')
                    ->where('role_id', $role->id)
                    ->where('permission_id', $perm->id)
                    ->exists();

                if ($already) {
                    continue;
                }

                DB::table('role_permissions')->insert([
                    'role_id' => $role->id,
                    'permission_id' => $perm->id,
                ]);
            }
        }
    }

    public function down(): void
    {
        $permissionSlugs = [
            'view-inquiries',
            'create-inquiries',
            'respond-inquiries',
            'view-visitors',
            'create-visitors',
            'checkout-visitors',
        ];

        $permIds = DB::table('permissions')->whereIn('slug', $permissionSlugs)->pluck('id');

        DB::table('role_permissions')->whereIn('permission_id', $permIds)->delete();
        DB::table('permissions')->whereIn('slug', $permissionSlugs)->delete();
    }
};

