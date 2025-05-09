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

public function update(User $user, Komunitas $komunitas)
{
    // Admin bisa edit semua data
    if ($user->role === 'admin') {
        return Response::allow();
    }
    
    // Komunitas hanya bisa edit data sendiri
    if ($user->tokenCan('komunitas') && $user->id_komunitas == $komunitas->id_komunitas) {
        return Response::allow();
    }
    
    return Response::deny('Anda tidak memiliki akses untuk mengedit data ini.');
}
    public function view(User $user, Komunitas $komunitas)
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('Hanya admin yang bisa melihat detail komunitas.');
    }
}