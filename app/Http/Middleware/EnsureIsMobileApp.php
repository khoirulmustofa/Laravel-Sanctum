<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsMobileApp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek Header Khusus (Contoh: X-Mobile-App)
        // Anda bisa menentukan sendiri nama & value secret key-nya
        $mobileSecret = 'NFBS-Mobile-App-Secret-Key-2026';

        if ($request->header('X-Mobile-App') !== $mobileSecret) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized Access: Mobile App Only'
            ], 403);
        }

        return $next($request);
    }
}
