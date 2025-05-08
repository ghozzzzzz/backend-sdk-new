<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Komunitas;
use Illuminate\Auth\Access\Response;

class KomunitasPolicy
{
    public function viewAny(User $user)
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('Hanya admin yang bisa melihat data komunitas.');
    }

    public function view(User $user, Komunitas $komunitas)
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('Hanya admin yang bisa melihat detail komunitas.');
    }
}