<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Komunitas;
use App\Models\Pemesanan;
use Illuminate\Auth\Access\Response;

class PemesananPolicy
{
    public function viewAny(User $user)
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('Hanya admin yang bisa melihat semua pemesanan.');
    }

    public function view(User $user, Pemesanan $pemesanan)
    {
        return $user->role === 'admin' || $pemesanan->id_komunitas === $user->id_komunitas
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses ke pemesanan ini.');
    }

    public function update(User $user)
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('Hanya admin yang bisa mengupdate status pemesanan.');
    }
}