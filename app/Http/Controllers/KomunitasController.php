<?php

namespace App\Http\Controllers;

use App\Models\Komunitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * @group Manajemen Komunitas
 * 
 * Endpoint untuk mengelola data komunitas (CRUD).
 * Akses dibatasi hanya untuk admin atau komunitas yang sesuai.
 */
class KomunitasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Ambil semua data komunitas
     * 
     * @authenticated
     * 
     * @response 200 {
     *  "data": [...],
     *  "message": "Berhasil mengambil data komunitas"
     * }
     * 
     * @response 403 {
     *  "message": "Forbidden"
     * }
     */
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $komunitas = Komunitas::all();
        return response()->json([
            'data' => $komunitas,
            'message' => 'Berhasil mengambil data komunitas'
        ]);
    }

    /**
     * Tambah komunitas baru
     * 
     * @authenticated
     * 
     * @bodyParam nama_komunitas string required Nama komunitas
     * @bodyParam koordinator string required Nama koordinator
     * @bodyParam telepon string required Nomor telepon komunitas (unik)
     * @bodyParam email_komunitas string required Email komunitas (unik)
     * @bodyParam jumlah_anggota integer required Minimal 1
     * @bodyParam password string required Minimal 8 karakter
     * @bodyParam password_confirmation string required Konfirmasi password
     * 
     * @response 201 {
     *  "data": {...},
     *  "message": "Komunitas created successfully"
     * }
     */
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'nama_komunitas' => 'required|string|max:255',
            'koordinator' => 'required|string|max:255',
            'telepon' => 'required|string|max:20|unique:komunitas',
            'email_komunitas' => 'required|email|unique:komunitas',
            'jumlah_anggota' => 'required|integer|min:1',
            'password' => ['required', 'confirmed', Password::min(8)]
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $komunitas = Komunitas::create($validated);

        return response()->json([
            'data' => $komunitas,
            'message' => 'Komunitas created successfully'
        ], 201);
    }

    /**
     * Hapus komunitas
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID komunitas. Contoh: 1
     * 
     * @response 200 {
     *  "message": "Komunitas deleted successfully"
     * }
     * 
     * @response 403 {
     *  "message": "Forbidden"
     * }
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $komunitas = Komunitas::findOrFail($id);
        $komunitas->delete();

        return response()->json([
            'message' => 'Komunitas deleted successfully'
        ]);
    }

    /**
     * Perbarui data komunitas
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID komunitas
     * 
     * @bodyParam nama_komunitas string Nama komunitas
     * @bodyParam tipe string Tipe komunitas
     * @bodyParam koordinator string Nama koordinator
     * @bodyParam telepon string Nomor telepon unik
     * @bodyParam email_komunitas string Email komunitas
     * @bodyParam jumlah_anggota integer Jumlah anggota
     * @bodyParam password string Password baru (optional)
     * @bodyParam password_confirmation string Konfirmasi password
     * 
     * @response 200 {
     *  "data": {...},
     *  "message": "Data komunitas berhasil diperbarui"
     * }
     * 
     * @response 403 {
     *  "message": "Unauthorized"
     * }
     */
    public function update(Request $request, $id)
    {
        $komunitas = Komunitas::findOrFail($id);

        if ($request->user()->tokenCan('komunitas') && 
            $request->user()->id_komunitas == $id) {

            $validated = $request->validate([
                'nama_komunitas' => 'sometimes|string|max:255',
                'tipe' => 'sometimes|string|max:255',
                'koordinator' => 'sometimes|string|max:255',
                'telepon' => 'sometimes|string|max:20|unique:komunitas,telepon,' . $id . ',id_komunitas',
                'email_komunitas' => 'sometimes|email|unique:komunitas,email_komunitas,' . $id . ',id_komunitas',
                'jumlah_anggota' => 'sometimes|integer|min:1',
                'password' => ['sometimes', 'confirmed', Password::min(8)]
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $komunitas->update($validated);

            return response()->json([
                'data' => $komunitas,
                'message' => 'Data komunitas berhasil diperbarui'
            ]);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }

    /**
     * Ambil detail komunitas
     * 
     * @urlParam id integer required ID komunitas
     * 
     * @response 200 {
     *  "success": true,
     *  "data": {...},
     *  "message": "Berhasil mengambil detail komunitas"
     * }
     * 
     * @response 404 {
     *  "success": false,
     *  "message": "Komunitas tidak ditemukan"
     * }
     */
    public function show($id)
    {
        $komunitas = Komunitas::find($id);

        if (!$komunitas) {
            return response()->json([
                'success' => false,
                'message' => 'Komunitas tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $komunitas,
            'message' => 'Berhasil mengambil detail komunitas'
        ]);
    }
}
