<?php

namespace App\Http\Controllers;

use App\Models\Cleaner;
use Illuminate\Http\Request;

class CleanerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('can:cleaners.view')->only(['index']);
        $this->middleware('can:cleaners.manage')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $cleaners = Cleaner::orderBy('full_name')->paginate(10);
        return view('cleaners.index', compact('cleaners'));
    }

    public function create()
    {
        return view('cleaners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_name' => ['nullable', 'string', 'max:255'],
        ]);

        // Untuk kompatibilitas dengan skema lama yang menggunakan kolom 'name'
        $data = $validated;
        $data['name'] = $validated['full_name'] ?? null;
        // Set default status dan aktif untuk alur approval
        $data['status'] = 'pending';
        $data['active'] = false;

        Cleaner::create($data);

        return redirect()->route('cleaners.index')->with('success', 'Petugas berhasil ditambahkan. Menunggu approval agar menjadi aktif.');
    }

    public function edit(Cleaner $cleaner)
    {
        return view('cleaners.edit', compact('cleaner'));
    }

    public function update(Request $request, Cleaner $cleaner)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_name' => ['nullable', 'string', 'max:255'],
        ]);

        // Sinkronkan kolom 'name' untuk kompatibilitas
        $data = $validated;
        $data['name'] = $validated['full_name'] ?? $cleaner->name;

        $cleaner->update($data);

        return redirect()->route('cleaners.index')->with('success', 'Petugas berhasil diperbarui.');
    }

    public function destroy(Cleaner $cleaner)
    {
        $cleaner->delete();
        return redirect()->route('cleaners.index')->with('success', 'Petugas berhasil dihapus.');
    }

    /**
     * Approve petugas: set status approved dan aktifkan.
     */
    public function approve(Cleaner $cleaner)
    {
        $this->authorize('cleaners.manage');

        if ($cleaner->status === 'approved' && $cleaner->active) {
            return redirect()->route('cleaners.index')->with('info', 'Petugas sudah aktif.');
        }

        $cleaner->forceFill([
            'status' => 'approved',
            'active' => true,
        ])->save();

        return redirect()->route('cleaners.index')->with('success', 'Petugas berhasil di-approve dan diaktifkan.');
    }

    /**
     * Reject petugas: set status rejected dan tetap non-aktif.
     */
    public function reject(Cleaner $cleaner)
    {
        $this->authorize('cleaners.manage');

        $cleaner->forceFill([
            'status' => 'rejected',
            'active' => false,
        ])->save();

        return redirect()->route('cleaners.index')->with('success', 'Petugas berhasil di-reject dan tidak diaktifkan.');
    }
}
