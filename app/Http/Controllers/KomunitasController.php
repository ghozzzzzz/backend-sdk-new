<?php

namespace App\Http\Controllers;

use App\Models\Komunitas;
use Illuminate\Http\Request;

class KomunitasController extends Controller  // Pastikan extends Controller
{
    public function __construct()
    {
        // Middleware bisa dipindahkan ke routes atau tetap di sini
        $this->middleware('auth:sanctum');
    }

    // Get semua data komunitas
    public function index()
    {
        $komunitas = Komunitas::all();
        return response()->json([
            'data' => $komunitas,
            'message' => 'Berhasil mengambil data komunitas'
        ]);
    }

    // Get detail komunitas
    public function show($id)
    {
        $komunitas = Komunitas::find($id);
        
        if (!$komunitas) {
            return response()->json(['message' => 'Komunitas tidak ditemukan'], 404);
        }

        return response()->json([
            'data' => $komunitas,
            'message' => 'Berhasil mengambil detail komunitas'
        ]);
    }
}