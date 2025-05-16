<?php

namespace App\Http\Controllers;

use App\Models\Komunitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class KomunitasController extends Controller  // Pastikan extends Controller
{
    public function __construct()
    {
        // Middleware bisa dipindahkan ke routes atau tetap di sini
        $this->middleware('auth:sanctum');
    }

    // Get semua data komunitas
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
public function update(Request $request, $id)
{
    $komunitas = Komunitas::findOrFail($id);
    
    // Verifikasi bahwa yang mengedit adalah pemilik data
    if ($request->user()->tokenCan('komunitas') && 
        $request->user()->id_komunitas == $id) {
        
        $validated = $request->validate([
            'nama_komunitas' => 'sometimes|string|max:255',
            'tipe' => 'sometimes|string|max:255',
            'koordinator' => 'sometimes|string|max:255',
            'telepon' => 'sometimes|string|max:20|unique:komunitas,telepon,'.$id.',id_komunitas',
            'email_komunitas' => 'sometimes|email|unique:komunitas,email_komunitas,'.$id.',id_komunitas',
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

    // Get detail komunitas
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