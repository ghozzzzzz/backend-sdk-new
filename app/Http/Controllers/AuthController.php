<?php

namespace App\Http\Controllers;

use App\Models\Komunitas;
use App\Models\User;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * @group Autentikasi
 * 
 * Endpoint untuk login, register, logout dan akses data berbasis token untuk admin dan komunitas.
 */
class AuthController extends Controller
{
    /**
     * Login Komunitas (Session)
     * 
     * @bodyParam login string required Email atau telepon komunitas. Contoh: komunitas@example.com
     * @bodyParam password string required Password. Contoh: rahasia123
     * 
     * @response 200 {
     *  "access_token": "1|abc123...",
     *  "token_type": "Bearer",
     *  "komunitas": {...}
     * }
     * 
     * @response 401 {
     *  "message": "Unauthorized"
     * }
     */
    public function komunitasLogin(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string'
        ]);

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) 
            ? 'email_komunitas' 
            : 'telepon';

        if (Auth::guard('komunitas')->attempt([
            $field => $credentials['login'],
            'password' => $credentials['password']
        ])) {
            $komunitas = Auth::guard('komunitas')->user();
            $token = $komunitas->createToken('komunitas-token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'komunitas' => $komunitas
            ]);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    /**
     * Login Komunitas (API)
     * 
     * @bodyParam login string required Email atau telepon komunitas.
     * @bodyParam password string required Password.
     * 
     * @response 200 {
     *  "access_token": "1|abc123...",
     *  "token_type": "Bearer",
     *  "komunitas": {...}
     * }
     */
    public function komunitasApiLogin(Request $request)
{
    $credentials = $request->validate([
        'login' => 'required|string',
        'password' => 'required|string'
    ]);

    $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) 
        ? 'email_komunitas' 
        : 'telepon';

    $komunitas = Komunitas::where($field, $credentials['login'])->first();

    if (!$komunitas || !Hash::check($credentials['password'], $komunitas->password)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // Clear existing tokens
    $komunitas->tokens()->delete();
    
    $token = $komunitas->createToken('komunitas-token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
        'komunitas' => $komunitas
    ]);
}

    /**
     * Login Admin
     * 
     * @bodyParam email string required Email admin. Contoh: admin@example.com
     * @bodyParam password string required Password admin.
     * 
     * @response 200 {
     *  "access_token": "1|xyz123...",
     *  "token_type": "Bearer",
     *  "user": {...}
     * }
     * 
     * @response 403 {
     *  "message": "Forbidden"
     * }
     */
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();

            if ($user->role !== 'admin') {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            $token = $user->createToken('admin-token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ]);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    /**
     * Register Komunitas
     * 
     * @bodyParam nama_komunitas string required Nama komunitas
     * @bodyParam tipe string required Tipe komunitas
     * @bodyParam koordinator string required Nama koordinator
     * @bodyParam telepon string required Nomor telepon unik
     * @bodyParam email_komunitas string required Email unik
     * @bodyParam jumlah_anggota integer required Jumlah anggota minimal 1
     * @bodyParam password string required Password
     * @bodyParam password_confirmation string required Konfirmasi password
     * 
     * @response 201 {
     *  "access_token": "...",
     *  "token_type": "Bearer",
     *  "komunitas": {...}
     * }
     */
    public function komunitasRegister(Request $request)
    {
        $validated = $request->validate([
            'nama_komunitas' => 'required|string|max:255',
            'tipe' => 'required|string|max:255',
            'koordinator' => 'required|string|max:255',
            'telepon' => 'required|string|max:20|unique:komunitas,telepon',
            'email_komunitas' => 'required|email|unique:komunitas,email_komunitas',
            'jumlah_anggota' => 'required|integer|min:1',
            'password' => ['required', 'confirmed', Password::min(8)]
        ]);

        $komunitas = Komunitas::create([
            'nama_komunitas' => $validated['nama_komunitas'],
            'tipe' => $validated['tipe'],
            'koordinator' => $validated['koordinator'],
            'telepon' => $validated['telepon'],
            'email_komunitas' => $validated['email_komunitas'],
            'jumlah_anggota' => $validated['jumlah_anggota'],
            'password' => Hash::make($validated['password'])
        ]);

        $token = $komunitas->createToken('komunitas-token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'komunitas' => $komunitas
        ], 201);
    }

    /**
     * Register Admin
     * 
     * @bodyParam name string required Nama admin
     * @bodyParam email string required Email unik
     * @bodyParam password string required Password
     * @bodyParam password_confirmation string required Konfirmasi password
     * @bodyParam role string Role admin/user (default user)
     * 
     * @response 201 {
     *  "access_token": "...",
     *  "token_type": "Bearer",
     *  "user": {...}
     * }
     */
    public function adminRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'sometimes|string|in:admin,user'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'user'
        ]);

        $token = $user->createToken('admin-token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    /**
     * Ambil daftar anggota berdasarkan komunitas yang login
     * 
     * @authenticated
     * 
     * @response 200 {
     *  "data": [...],
     *  "message": "Berhasil mengambil data anggota"
     * }
     * 
     * @response 403 {
     *  "message": "Unauthorized"
     * }
     */
    public function getAnggotaKomunitas(Request $request)
    {
        $user = $request->user();

        if (!$user instanceof Komunitas) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $anggota = Anggota::where('id_komunitas', $user->id_komunitas)->get();

        return response()->json([
            'data' => $anggota,
            'message' => 'Berhasil mengambil data anggota'
        ]);
    }

    /**
     * Logout user saat ini
     * 
     * @authenticated
     * 
     * @response 200 {
     *  "message": "Logged out successfully"
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
