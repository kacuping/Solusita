<?php

namespace App\Http\Controllers;

use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

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
        $nameColumn = Schema::hasColumn('cleaners', 'full_name') ? 'full_name' : 'name';
        $cleaners = Cleaner::orderBy($nameColumn)->paginate(10);
        $file = storage_path('app/cleaner_photos.json');
        $photos = [];
        if (file_exists($file)) {
            $json = file_get_contents($file);
            $photos = json_decode($json, true) ?: [];
        }
        return view('cleaners.index', compact('cleaners', 'photos'));
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
            'photo' => ['nullable', 'image', 'max:4096'],
        ]);

        $data = $validated;
        if (Schema::hasColumn('cleaners', 'name')) {
            $data['name'] = $validated['full_name'] ?? null;
        }
        $data['status'] = 'pending';
        $data['active'] = false;

        $filtered = collect($data)->filter(function ($v, $k) {
            return Schema::hasColumn('cleaners', $k);
        })->all();

        $cleaner = Cleaner::create($filtered);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('cleaners', 'public');
            $url = \Illuminate\Support\Facades\Storage::url($path);
            $file = storage_path('app/cleaner_photos.json');
            $photos = [];
            if (file_exists($file)) {
                $json = file_get_contents($file);
                $photos = json_decode($json, true) ?: [];
            }
            $photos[(string) $cleaner->id] = $url;
            file_put_contents($file, json_encode($photos));
        }

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
            'photo' => ['nullable', 'image', 'max:4096'],
        ]);

        $data = $validated;
        if (Schema::hasColumn('cleaners', 'name')) {
            $data['name'] = $validated['full_name'] ?? $cleaner->name;
        }

        $filtered = collect($data)->filter(function ($v, $k) {
            return Schema::hasColumn('cleaners', $k);
        })->all();

        $cleaner->update($filtered);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('cleaners', 'public');
            $url = \Illuminate\Support\Facades\Storage::url($path);
            $file = storage_path('app/cleaner_photos.json');
            $photos = [];
            if (file_exists($file)) {
                $json = file_get_contents($file);
                $photos = json_decode($json, true) ?: [];
            }
            $photos[(string) $cleaner->id] = $url;
            file_put_contents($file, json_encode($photos));
        }

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
