<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;

    protected $table = 'pemesanan';

    protected $fillable = [
        'id_komunitas',
        'ruangan_id',
        'waktu_mulai',
        'waktu_selesai',
        'jumlah_peserta',
        'kebutuhan_khusus',
        'status',
        'catatan_admin'
    ];

    protected $dates = [
        'waktu_mulai',
        'waktu_selesai'
    ];

    public function komunitas()
    {
        return $this->belongsTo(Komunitas::class, 'id_komunitas');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }
}