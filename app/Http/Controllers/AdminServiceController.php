<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class AdminServiceController extends Controller
{
    /**
     * Display a listing of the services.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $query = Service::query()->orderBy('name');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $services = $query->paginate(15)->withQueryString();
        $categoryOptions = Schema::hasTable('service_categories')
            ? ServiceCategory::where('active', true)->orderBy('name')->get()
            : collect();

        // Count distinct active categories to inform admin if below minimum
        $distinctCategoryCount = Service::where('active', true)
            ->whereNotNull('category')
            ->distinct()
            ->count('category');

        $unitTypes = ['M2', 'Buah/Seater', 'Durasi', 'Satuan'];

        return view('admin.services.index', [
            'services' => $services,
            'search' => $search,
            'distinctCategoryCount' => $distinctCategoryCount,
            'categoryOptions' => $categoryOptions,
            'unitTypes' => $unitTypes,
        ]);
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'duration_hours' => [Rule::requiredIf($request->input('unit_type') === 'Durasi'), 'integer', 'min:1'],
            'category' => ['required', 'string', 'max:100', 'exists:service_categories,name'],
            'unit_type' => ['required', Rule::in(['M2', 'Buah/Seater', 'Durasi', 'Satuan'])],
            'icon' => ['nullable', 'string', 'max:100'],
            'active' => ['nullable', 'boolean'],
        ]);

        $data['active'] = $request->boolean('active');
        $data['slug'] = Str::slug($data['name']);
        if (($data['unit_type'] ?? null) === 'Durasi') {
            $hours = (int) $request->input('duration_hours', 0);
            $data['duration_minutes'] = max($hours * 60, 0);
        } else {
            $data['duration_minutes'] = 0;
        }
        unset($data['duration_hours']);

        Service::create($data);

        return back()->with('status', 'Layanan berhasil ditambahkan.');
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'duration_hours' => [Rule::requiredIf($request->input('unit_type') === 'Durasi'), 'integer', 'min:1'],
            'category' => ['required', 'string', 'max:100', 'exists:service_categories,name'],
            'unit_type' => ['required', Rule::in(['M2', 'Buah/Seater', 'Durasi', 'Satuan'])],
            'icon' => ['nullable', 'string', 'max:100'],
            'active' => ['nullable', 'boolean'],
        ]);

        $data['active'] = $request->boolean('active');
        $data['slug'] = Str::slug($data['name']);
        if (($data['unit_type'] ?? null) === 'Durasi') {
            $hours = (int) $request->input('duration_hours', 0);
            $data['duration_minutes'] = max($hours * 60, 0);
        } else {
            $data['duration_minutes'] = 0;
        }
        unset($data['duration_hours']);

        $service->update($data);

        return back()->with('status', 'Layanan berhasil diperbarui.');
    }

    /**
     * Remove the specified service.
     */
    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();
        return back()->with('status', 'Layanan berhasil dihapus.');
    }
}
