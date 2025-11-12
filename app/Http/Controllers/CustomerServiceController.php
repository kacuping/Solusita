<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerServiceController extends Controller
{
    /**
     * Default icon mapping by category for consistent display.
     */
    private array $categoryIcons = [
        'General' => 'fa-broom',
        'Karpet' => 'fa-brush',
        'Sofa' => 'fa-couch',
        'AC' => 'fa-wind',
        'Dapur' => 'fa-utensils',
        'Kamar Mandi' => 'fa-shower',
        'Lantai' => 'fa-broom',
    ];

    /**
     * Compute final icon for a service using its set icon or category default.
     */
    private function computeIconFor(Service $service): string
    {
        $categoryIcon = null;
        if ($service->category) {
            $cat = ServiceCategory::where('name', $service->category)->first();
            $categoryIcon = $cat?->icon;
        }
        return $service->icon ?: ($categoryIcon ?: ($this->categoryIcons[$service->category] ?? 'fa-broom'));
    }
    /**
     * List all active services for customers.
     */
    public function index(Request $request): View
    {
        $category = trim((string) $request->query('category', ''));
        $query = Service::where('active', true);
        if ($category !== '') {
            $query->where('category', $category);
        }
        $services = $query->orderBy('name')->get();
        foreach ($services as $srv) {
            $srv->setAttribute('display_icon', $this->computeIconFor($srv));
        }

        return view('customer.services.index', [
            'services' => $services,
            'selectedCategory' => $category,
        ]);
    }

    /**
     * Show service detail page for customers.
     */
    public function show(string $slug): View
    {
        $service = Service::where('slug', $slug)->where('active', true)->firstOrFail();

        return view('customer.services.show', [
            'service' => $service,
            'iconClass' => $this->computeIconFor($service),
        ]);
    }
}
