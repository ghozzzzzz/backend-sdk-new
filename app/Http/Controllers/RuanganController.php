<?php
namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function index()
    {
        $ruangan = Ruangan::all();
        return response()->json([
            'data' => $ruangan,
            'message' => 'Berhasil mengambil data ruangan'
        ]);
    }

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