<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerServiceController extends Controller
{
    /**
     * List all active services for customers.
     */
    public function index(): View
    {
        $services = Service::where('active', true)->orderBy('name')->get();

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
        ]);
    }
}
