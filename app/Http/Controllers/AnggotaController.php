<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group Anggota
 * 
 * API untuk mengelola data anggota komunitas. Hanya admin yang bisa mengakses endpoint ini.
 */
class AnggotaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Ambil semua data anggota
     * 
     * @authenticated
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "nama": "Budi",
     *       "no_telepon": "081234567890",
     *       "email": "budi@example.com",
     *       "id_komunitas": 1,
     *       "created_at": "2025-07-08T00:00:00.000000Z",
     *       "updated_at": "2025-07-08T00:00:00.000000Z"
     *     }
     *   ],
     *   "message": "Berhasil mengambil data anggota"
     * }
     * 
     * @response 403 {
     *   "message": "Unauthorized"
     * }
     */
    public function index(Request $request)
{
    $user = $request->user();

    // Cek role, hanya admin yang boleh akses
    if ($user->role !== 'admin') {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Ambil data anggota dengan relasi komunitas
    $query = Anggota::with('komunitas');

    // Jika ada query komunitas_id, lakukan filter
    if ($request->has('komunitas_id')) {
        $query->where('id_komunitas', $request->komunitas_id);
    }

    // Ambil hasil query
    $anggota = $query->get();

    return response()->json([
        'data' => $anggota,
        'message' => 'Berhasil mengambil data anggota'
    ]);
}


    /**
     * Tambah anggota baru
     * 
     * @authenticated
     * 
     * @bodyParam nama string required Nama anggota. Contoh: Budi
     * @bodyParam no_telepon string required Nomor telepon unik. Contoh: 081234567890
     * @bodyParam email string required Email unik. Contoh: budi@example.com
     * @bodyParam id_komunitas integer required ID komunitas. Contoh: 1
     * 
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "nama": "Budi",
     *     "no_telepon": "081234567890",
     *     "email": "budi@example.com",
     *     "id_komunitas": 1,
     *     "created_at": "2025-07-08T00:00:00.000000Z",
     *     "updated_at": "2025-07-08T00:00:00.000000Z"
     *   },
     *   "message": "Anggota berhasil ditambahkan"
     * }
     * 
     * @response 422 {
     *   "email": [
     *     "The email has already been taken."
     *   ]
     * }
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20|unique:anggota,no_telepon',
            'email' => 'required|email|unique:anggota,email',
            'id_komunitas' => 'required|exists:komunitas,id_komunitas'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $anggota = Anggota::create($validator->validated());

        return response()->json([
            'data' => $anggota,
            'message' => 'Anggota berhasil ditambahkan'
        ], 201);
    }

    /**
     * Lihat detail anggota berdasarkan ID
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID anggota. Contoh: 1
     * 
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "nama": "Budi",
     *     "no_telepon": "081234567890",
     *     "email": "budi@example.com",
     *     "id_komunitas": 1
     *   },
     *   "message": "Berhasil mengambil detail anggota"
     * }
     * 
     * @response 404 {
     *   "message": "Anggota tidak ditemukan"
     * }
     */
    public function show($id)
    {
        $anggota = Anggota::with('komunitas')->find($id);
        
        if (!$anggota) {
            return response()->json(['message' => 'Anggota tidak ditemukan'], 404);
        }

        return response()->json([
            'data' => $anggota,
            'message' => 'Berhasil mengambil detail anggota'
        ]);
    }

    /**
     * Update anggota
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID anggota. Contoh: 1
     * 
     * @bodyParam nama string Nama anggota. Contoh: Budi
     * @bodyParam no_telepon string Nomor telepon unik. Contoh: 081234567891
     * @bodyParam email string Email unik. Contoh: budi_new@example.com
     * @bodyParam id_komunitas integer ID komunitas. Contoh: 2
     * 
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "nama": "Budi",
     *     "no_telepon": "081234567891",
     *     "email": "budi_new@example.com",
     *     "id_komunitas": 2
     *   },
     *   "message": "Anggota berhasil diperbarui"
     * }
     */
    public function update(Request $request, $id)
    {
        $anggota = Anggota::find($id);
        if (!$anggota) {
            return response()->json(['message' => 'Anggota tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|string|max:255',
            'no_telepon' => 'sometimes|string|max:20|unique:anggota,no_telepon,'.$id,
            'email' => 'sometimes|email|unique:anggota,email,'.$id,
            'id_komunitas' => 'sometimes|exists:komunitas,id_komunitas'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $anggota->update($validator->validated());

        return response()->json([
            'data' => $anggota,
            'message' => 'Anggota berhasil diperbarui'
        ]);
    }

    /**
     * Hapus anggota
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID anggota. Contoh: 1
     * 
     * @response 200 {
     *   "message": "Anggota berhasil dihapus"
     * }
     * 
     * @response 404 {
     *   "message": "Anggota tidak ditemukan"
     * }
     */
    public function getByKomunitas($id)
{
    $anggota = \App\Models\Anggota::where('id_komunitas', $id)->get();

    if ($anggota->isEmpty()) {
        return response()->json(['message' => 'Tidak ada anggota dalam komunitas ini'], 404);
    }

    return response()->json($anggota);
}

    public function destroy($id)
    {
        $anggota = Anggota::find($id);
        if (!$anggota) {
            return response()->json(['message' => 'Anggota tidak ditemukan'], 404);
        }

        $anggota->delete();

        return response()->json([
            'message' => 'Anggota berhasil dihapus'
        ]);
    }
}
