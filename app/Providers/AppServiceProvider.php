<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function (User $user): ?bool {
            return $user->role === 'super_admin' ? true : null;
        });

        foreach ([
            'view-students', 'create-students', 'edit-students', 'delete-students', 'view-own-student',
            'view-fees', 'collect-payments', 'view-fee-structure', 'edit-fee-structure', 'process-refunds',
            'view-employees', 'create-employees', 'edit-employees', 'delete-employees',
            'view-salaries', 'process-salaries', 'view-own-salary',
            'view-loans', 'approve-loans', 'record-loan-payments', 'view-own-loan',
            'view-overdrafts', 'approve-overdrafts', 'record-overdraft-transactions',
            'view-expenses', 'create-expenses', 'approve-expenses', 'edit-expenses',
            'view-assets', 'create-assets', 'edit-assets', 'delete-assets',
            'view-incomes', 'create-incomes',
            'view-financial-reports', 'view-fee-reports', 'view-salary-reports', 'view-pl-reports',
            'view-settings', 'edit-settings', 'manage-users', 'view-logs', 'manage-backups',
            // inquiries/visitors
            'view-inquiries', 'create-inquiries', 'respond-inquiries',
            'view-visitors', 'create-visitors', 'checkout-visitors',
        ] as $permission) {
            Gate::define($permission, fn (User $user): bool => $user->hasPermission($permission));
        }
    }
}
