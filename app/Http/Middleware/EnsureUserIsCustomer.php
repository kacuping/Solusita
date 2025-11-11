<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCustomer
{
    /**
     * Pastikan user yang mengakses adalah pelanggan.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'customer') {
            abort(403, 'Anda tidak memiliki akses sebagai pelanggan.');
        }

        return $next($request);
    }
}

