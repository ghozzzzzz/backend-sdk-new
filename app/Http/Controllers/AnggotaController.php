<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnggotaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->role === 'admin') {
            $anggota = Anggota::with('komunitas')->get();
        } else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'data' => $anggota,
            'message' => 'Berhasil mengambil data anggota'
        ]);
    }

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