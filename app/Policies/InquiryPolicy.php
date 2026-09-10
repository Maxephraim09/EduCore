<?php

namespace App\Policies;

use App\Models\Inquiry;
use App\Models\User;

class InquiryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-inquiries');
    }

    public function view(User $user, Inquiry $inquiry): bool
    {
        return $user->hasPermission('view-inquiries');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create-inquiries');
    }

    public function respond(User $user, Inquiry $inquiry): bool
    {
        return $user->hasPermission('respond-inquiries');
    }
}

