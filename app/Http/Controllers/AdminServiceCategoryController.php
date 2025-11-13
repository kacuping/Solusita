<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminServiceCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $perPage = 15;
        if (Schema::hasTable('service_categories')) {
            $query = ServiceCategory::query()->orderBy('name');
            if ($search !== '') {
                $query->where('name', 'like', "%{$search}%");
            }
            $categories = $query->paginate($perPage)->withQueryString();
        } else {
            $categories = new LengthAwarePaginator([], 0, $perPage, (int) $request->query('page', 1), [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        }

        return view('admin.service_categories.index', [
            'categories' => $categories,
            'search' => $search,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (! Schema::hasTable('service_categories')) {
            return back()->with('status', 'Tabel Kategori belum tersedia di server. Jalankan migrasi: php artisan migrate --force');
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:service_categories,name'],
            'image' => ['nullable', 'image', 'max:2048'],
            'active' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'name' => $data['name'],
            'active' => $request->boolean('active'),
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $payload['image'] = $path;
            $payload['icon'] = null;
        }

        ServiceCategory::create($payload);

        return back()->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, ServiceCategory $service_category): RedirectResponse
    {
        if (! Schema::hasTable('service_categories')) {
            return back()->with('status', 'Tabel Kategori belum tersedia di server. Jalankan migrasi: php artisan migrate --force');
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:service_categories,name,'.$service_category->id],
            'image' => ['nullable', 'image', 'max:2048'],
            'active' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'name' => $data['name'],
            'active' => $request->boolean('active'),
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $payload['image'] = $path;
            $payload['icon'] = null;
        }

        $service_category->update($payload);

        return back()->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(ServiceCategory $service_category): RedirectResponse
    {
        $service_category->delete();

        return back()->with('status', 'Kategori berhasil dihapus.');
    }

    public function image(ServiceCategory $service_category)
    {
        $path = (string) ($service_category->image ?? '');
        if ($path === '' || ! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return response()->file(storage_path('app/public/'.$path));
    }
}
