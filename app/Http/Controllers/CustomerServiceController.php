<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerServiceController extends Controller
{
    /**
     * Default icon mapping by category for consistent display.
     */
    private array $categoryIcons = [
        'General' => 'fa-broom',
        'Karpet' => 'fa-rug',
        'Sofa' => 'fa-couch',
        'AC' => 'fa-fan',
        'Dapur' => 'fa-utensils',
        'Kamar Mandi' => 'fa-shower',
        'Lantai' => 'fa-broom',
    ];

    /**
     * Compute final icon for a service using its set icon or category default.
     */
    private function computeIconFor(Service $service): string
    {
        return $service->icon ?: ($this->categoryIcons[$service->category] ?? 'fa-broom');
    }
    /**
     * List all active services for customers.
     */
    public function index(): View
    {
        $services = Service::where('active', true)->orderBy('name')->get();
        // Attach computed icon attribute for view consumption
        foreach ($services as $srv) {
            $srv->setAttribute('display_icon', $this->computeIconFor($srv));
        }

        return view('customer.services.index', [
            'services' => $services,
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
