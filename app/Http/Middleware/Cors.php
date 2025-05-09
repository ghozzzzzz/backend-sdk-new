<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    public function handle(Request $request, Closure $next)
    {
        // Daftar origin yang diizinkan
        $allowedOrigins = [
            'http://localhost:5173',
            'http://localhost:8000',
            // Tambahkan origin lainnya jika diperlukan
        ];

        $origin = $request->headers->get('Origin');

        // Izinkan semua origin dalam development
        if (app()->environment('local')) {
            return $this->handleCors($request, $next, $origin);
        }

        // Pada production, hanya izinkan origin yang terdaftar
        if (in_array($origin, $allowedOrigins)) {
            return $this->handleCors($request, $next, $origin);
        }

        return response('Origin tidak diizinkan', 403);
    }

    protected function handleCors($request, $next, $origin)
    {
        // Handle preflight request
        if ($request->isMethod('OPTIONS')) {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $origin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Vary', 'Origin');
        }

        $response = $next($request);

        // Tambahkan headers CORS
        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Vary', 'Origin');

        return $response;
    }
}