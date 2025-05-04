<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Komunitas extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'id_komunitas';
    protected $table = 'komunitas';

    protected $fillable = [
        'nama_komunitas',
        'tipe',
        'koordinator',
        'telepon',
        'email_komunitas',
        'jumlah_anggota',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];
}