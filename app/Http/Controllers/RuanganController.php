<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\Request;

/**
 * @group Manajemen Ruangan
 *
 * Endpoint untuk melihat data ruangan yang tersedia.
 */
class RuanganController extends Controller
{
    /**
     * Ambil semua data ruangan
     *
     * Menampilkan daftar semua ruangan yang tersedia.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "nama_ruangan": "Aula 1",
     *       "lokasi": "Lantai 1",
     *       "kapasitas_min": 10,
     *       "kapasitas_max": 100,
     *       ...
     *     }
     *   ],
     *   "message": "Berhasil mengambil data ruangan"
     * }
     */
    public function index()
    {
        $ruangan = Ruangan::all();
        return response()->json([
            'data' => $ruangan,
            'message' => 'Berhasil mengambil data ruangan'
        ]);
    }

    /**
     * Ambil detail ruangan berdasarkan ID
     *
     * Menampilkan informasi detail satu ruangan berdasarkan ID-nya.
     *
     * @urlParam id integer required ID ruangan yang ingin diambil. Contoh: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "nama_ruangan": "Aula 1",
     *     "lokasi": "Lantai 1",
     *     "kapasitas_min": 10,
     *     "kapasitas_max": 100,
     *     ...
     *   },
     *   "message": "Berhasil mengambil detail ruangan"
     * }
     *
     * @response 404 {
     *   "message": "Ruangan tidak ditemukan"
     * }
     */
    public function show($id)
    {
        $ruangan = Ruangan::find($id);
        
        if (!$ruangan) {
            return response()->json(['message' => 'Ruangan tidak ditemukan'], 404);
        }

        return response()->json([
            'data' => $ruangan,
            'message' => 'Berhasil mengambil detail ruangan'
        ]);
    }
}
