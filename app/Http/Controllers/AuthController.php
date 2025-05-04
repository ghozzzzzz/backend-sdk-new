<?php

namespace App\Http\Controllers;

use App\Models\Komunitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // Login untuk komunitas (web/session)
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

    // Login untuk komunitas (API)
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

        $token = $komunitas->createToken('komunitas-token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'komunitas' => $komunitas
        ]);
    }

    // Login untuk admin (web/session)
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

    // Register untuk Komunitas (API)
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

// Register untuk Admin
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

    // Logout untuk semua guard
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['message' => 'Logged out successfully']);
    }
}