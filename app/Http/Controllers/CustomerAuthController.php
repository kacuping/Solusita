<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAuthController extends Controller
{
    /**
     * Tampilkan halaman login khusus pelanggan (mobile-friendly).
     */
    public function create()
    {
        return view('customer.auth.login');
    }

    /**
     * Proses login pelanggan.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = true;

        if (! Auth::attempt($credentials, $remember)) {
            return back()->withErrors([
                'email' => __('Email atau password tidak valid.'),
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        // Pastikan hanya akun dengan role "customer" yang bisa masuk dari halaman ini
        $user = Auth::user();
        if (! $user || $user->role !== 'customer') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => __('Akun ini bukan pelanggan. Silakan gunakan halaman login yang sesuai.'),
            ])->onlyInput('email');
        }

        return redirect()->route('customer.home');
    }
}
