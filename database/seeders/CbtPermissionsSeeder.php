<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CbtPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // CBT Exam permissions
            'create cbt exams',
            'edit cbt exams',
            'delete cbt exams',
            'view cbt exams',
            'approve cbt exams',
            'publish cbt exams',
            'close cbt exams',
            'convert cbt exams',
            
            // CBT Question permissions
            'create cbt questions',
            'edit cbt questions',
            'delete cbt questions',
            
            // CBT Student permissions
            'take cbt exams',
            'view cbt results',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'super-admin']);
        $adminRole->givePermissionTo(Permission::all());

        $teacherRole = Role::create(['name' => 'teacher']);
        $teacherRole->givePermissionTo([
            'create cbt exams',
            'edit cbt exams',
            'view cbt exams',
            'create cbt questions',
            'edit cbt questions',
            'delete cbt questions',
        ]);

        $studentRole = Role::create(['name' => 'student']);
        $studentRole->givePermissionTo([
            'take cbt exams',
            'view cbt results',
        ]);
    }
}