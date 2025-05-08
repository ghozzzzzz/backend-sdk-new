<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ruangan;

class RuanganSeeder extends Seeder
{
    public function run()
    {
        $ruangan = [
            [
                'nama_ruangan' => 'Ruang Start-Up',
                'deskripsi' => 'Ruang untuk berkumpulnya para startup dan komunitas digital',
                'kapasitas_min' => 30,
                'kapasitas_max' => 50,
                'fasilitas' => json_encode([
                    'Meja Kerja dengan 10 komputer Desktop',
                    'Meja dan Kursi untuk 10 - 12 Peserta',
                    'Akses Wifi Fullspeed Unlimited',
                    'TV LED',
                    'Toilet'
                ]),
                'tersedia' => true
            ],
            [
                'nama_ruangan' => 'Private & Rest Room',
                'deskripsi' => 'Ruang Private & Ruang Istirahat',
                'kapasitas_min' => 5,
                'kapasitas_max' => 15,
                'fasilitas' => json_encode([
                    'Mushola',
                    'Akses Wifi Fullspeed Unlimited'
                ]),
                'tersedia' => true
            ],
            [
                'nama_ruangan' => 'Ruang UMKM',
                'deskripsi' => 'Ruang UMKM dengan setting lesehan',
                'kapasitas_min' => 25,
                'kapasitas_max' => 30,
                'fasilitas' => json_encode([
                    'Backdrop',
                    'Lighting',
                    'Proyektor/TV LED',
                    'Dispenser Free refill air minum',
                    'Akses Wifi free Unlimited'
                ]),
                'tersedia' => true
            ],
            [
                'nama_ruangan' => 'Meeting Room',
                'deskripsi' => 'Ruang Rapat',
                'kapasitas_min' => 10,
                'kapasitas_max' => 15,
                'fasilitas' => json_encode([
                    'White Board',
                    'Proyektor/TV LED',
                    'Meja dan Kursi',
                    'Akses Wifi Fullspeed Unlimited',
                    'Toilet'
                ]),
                'tersedia' => true
            ]
        ];

        foreach ($ruangan as $data) {
            Ruangan::create($data);
        }
    }
}