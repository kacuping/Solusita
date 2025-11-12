<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminServiceCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $query = ServiceCategory::query()->orderBy('name');

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->paginate(15)->withQueryString();

        return view('admin.service_categories.index', [
            'categories' => $categories,
            'search' => $search,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:service_categories,name'],
            'icon' => ['nullable', 'string', 'max:100'],
            'active' => ['nullable', 'boolean'],
        ]);

        $data['active'] = $request->boolean('active');

        ServiceCategory::create($data);

        return back()->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, ServiceCategory $service_category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:service_categories,name,'.$service_category->id],
            'icon' => ['nullable', 'string', 'max:100'],
            'active' => ['nullable', 'boolean'],
        ]);

        $data['active'] = $request->boolean('active');

        $service_category->update($data);

        return back()->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(ServiceCategory $service_category): RedirectResponse
    {
        $service_category->delete();
        return back()->with('status', 'Kategori berhasil dihapus.');
    }
}

