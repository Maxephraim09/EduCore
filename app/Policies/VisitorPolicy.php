<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Visitor;

class VisitorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-visitors');
    }

    public function view(User $user, Visitor $visitor): bool
    {
        return $user->hasPermission('view-visitors');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create-visitors');
    }

    public function checkout(User $user, Visitor $visitor): bool
    {
        return $user->hasPermission('checkout-visitors');
    }
}

