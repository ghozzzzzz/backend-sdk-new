<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group Pemesanan Ruangan
 *
 * Endpoint untuk mengelola pemesanan ruangan oleh komunitas.
 */
class PemesananController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Ambil semua data pemesanan
     *
     * Menampilkan daftar semua pemesanan untuk admin, dan hanya pemesanan milik komunitas untuk pengguna komunitas.
     *
     * @authenticated
     *
     * @response 200 {
     *   "data": [...],
     *   "message": "Berhasil mengambil data pemesanan"
     * }
     */
    public function index()
{
    $user = auth()->user();

    if ($user instanceof \App\Models\Komunitas) {
        $pemesanan = Pemesanan::where('id_komunitas', $user->id_komunitas)
            ->with(['ruangan', 'komunitas']) // <-- tambahkan komunitas
            ->get();
    } else {
        $pemesanan = Pemesanan::with(['ruangan', 'komunitas'])->get(); // <-- tambahkan komunitas
    }

    return response()->json([
        'data' => $pemesanan,
        'message' => 'Berhasil mengambil data pemesanan'
    ]);
}


    /**
     * Buat pemesanan ruangan baru
     *
     * Hanya dapat dilakukan oleh komunitas yang login.
     *
     * @authenticated
     *
     * @bodyParam ruangan_id integer required ID ruangan yang ingin dipesan. Contoh: 1
     * @bodyParam waktu_mulai datetime required Format: Y-m-d H:i:s. Contoh: 2025-07-09 10:00:00
     * @bodyParam waktu_selesai datetime required Format: Y-m-d H:i:s. Contoh: 2025-07-09 12:00:00
     * @bodyParam jumlah_peserta integer required Minimal 1. Contoh: 20
     * @bodyParam kebutuhan_khusus string Kebutuhan tambahan (opsional). Contoh: Projector
     *
     * @response 201 {
     *   "data": {...},
     *   "message": "Pemesanan berhasil dibuat"
     * }
     *
     * @response 422 {
     *   "message": "Jumlah peserta tidak sesuai dengan kapasitas ruangan"
     * }
     *
     * @response 422 {
     *   "message": "Ruangan sudah dipesan pada waktu tersebut"
     * }
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ruangan_id' => 'required|exists:ruangan,id',
            'waktu_mulai' => 'required|date|after:now',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'jumlah_peserta' => 'required|integer|min:1',
            'kebutuhan_khusus' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $ruangan = Ruangan::find($request->ruangan_id);

        if ($request->jumlah_peserta < $ruangan->kapasitas_min ||
            $request->jumlah_peserta > $ruangan->kapasitas_max) {
            return response()->json([
                'message' => 'Jumlah peserta tidak sesuai dengan kapasitas ruangan'
            ], 422);
        }

        // Validasi konflik jadwal
        $isConflict = Pemesanan::where('ruangan_id', $request->ruangan_id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                    ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('waktu_mulai', '<', $request->waktu_mulai)
                            ->where('waktu_selesai', '>', $request->waktu_selesai);
                    });
            })
            ->where('status', '!=', 'rejected')
            ->exists();

        if ($isConflict) {
            return response()->json([
                'message' => 'Ruangan sudah dipesan pada waktu tersebut'
            ], 422);
        }

        $pemesanan = Pemesanan::create([
            'id_komunitas' => auth()->user()->id_komunitas,
            'ruangan_id' => $request->ruangan_id,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'jumlah_peserta' => $request->jumlah_peserta,
            'kebutuhan_khusus' => $request->kebutuhan_khusus,
            'status' => 'pending'
        ]);

        return response()->json([
            'data' => $pemesanan,
            'message' => 'Pemesanan berhasil dibuat'
        ], 201);
    }

    /**
     * Update status pemesanan (hanya admin)
     *
     * Admin dapat menyetujui atau menolak pemesanan yang masuk.
     *
     * @authenticated
     *
     * @urlParam id integer required ID pemesanan. Contoh: 1
     * @bodyParam status string required Pilihan: approved, rejected. Contoh: approved
     * @bodyParam catatan_admin string Catatan tambahan dari admin (opsional). Contoh: Jadwal bentrok
     *
     * @response 200 {
     *   "data": {...},
     *   "message": "Status pemesanan berhasil diupdate"
     * }
     *
     * @response 403 {
     *   "message": "Unauthorized"
     * }
     */
    public function updateStatus(Request $request, $id)
    {
        if (!auth()->user() instanceof \App\Models\User || auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'catatan_admin' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pemesanan = Pemesanan::find($id);
        if (!$pemesanan) {
            return response()->json(['message' => 'Pemesanan tidak ditemukan'], 404);
        }

        $pemesanan->update([
            'status' => $request->status,
            'catatan_admin' => $request->catatan_admin
        ]);

        return response()->json([
            'data' => $pemesanan,
            'message' => 'Status pemesanan berhasil diupdate'
        ]);
    }
}
