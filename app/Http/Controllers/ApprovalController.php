<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cleaner;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('can:approvals.view')->only(['index']);
        $this->middleware('can:approvals.manage')->only(['approve']);
    }

    /**
     * Tampilkan daftar user yang belum terverifikasi untuk persetujuan admin.
     */
    public function index()
    {
        $pendingUsers = User::whereNull('email_verified_at')
            ->orderByDesc('created_at')
            ->paginate(15);

        // Petugas yang menunggu approval (status pending atau tidak aktif)
        $pendingCleaners = Cleaner::where(function($q){
                $q->where('status', 'pending')
                  ->orWhere('active', false);
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('approvals.index', compact('pendingUsers', 'pendingCleaners'));
    }

    /**
     * Setujui (approve) user: tandai sebagai terverifikasi.
     */
    public function approve(User $user, Request $request)
    {
        if ($user->email_verified_at) {
            return redirect()->route('approvals.index')->with('info', 'User sudah terverifikasi.');
        }

        $user->forceFill(['email_verified_at' => now()])->save();

        return redirect()->route('approvals.index')->with('success', 'User berhasil di-approve dan terverifikasi.');
    }

    /**
     * Setujui (approve) petugas: ubah status ke approved dan aktifkan.
     */
    public function approveCleaner(Cleaner $cleaner, Request $request)
    {
        $this->authorize('approvals.manage');

        if ($cleaner->status === 'approved' && $cleaner->active) {
            return redirect()->route('approvals.index')->with('info', 'Petugas sudah aktif.');
        }

        $cleaner->forceFill([
            'status' => 'approved',
            'active' => true,
        ])->save();

        return redirect()->route('approvals.index')->with('success', 'Petugas berhasil di-approve dan diaktifkan.');
    }
}
