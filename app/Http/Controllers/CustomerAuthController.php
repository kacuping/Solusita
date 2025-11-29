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

        $user = Auth::user();
        if ($user && ($user->role === 'administrator' || $user->role === 'admin')) {
            return redirect()->route('customer.admin.home');
        }
        if ($user && $user->role === 'customer') {
            return redirect()->route('customer.home');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return back()->withErrors([
            'email' => __('Akun ini tidak memiliki akses pada halaman ini.'),
        ])->onlyInput('email');
    }
}
