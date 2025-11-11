<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromotionsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        // Optional: add permission gates if available
        // $this->middleware('can:promotions.view')->only(['index']);
        // $this->middleware('can:promotions.manage')->except(['index']);
    }

    public function index()
    {
        $promotions = Promotion::orderByDesc('created_at')->paginate(15);
        return view('promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('promotions.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $data['used_count'] = 0;

        Promotion::create($data);

        return redirect()->route('promotions.index')->with('success', 'Promo berhasil dibuat.');
    }

    public function edit(Promotion $promotion)
    {
        return view('promotions.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $data = $this->validateData($request, $promotion->id);

        $promotion->update($data);

        return redirect()->route('promotions.index')->with('success', 'Promo berhasil diperbarui.');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return redirect()->route('promotions.index')->with('success', 'Promo berhasil dihapus.');
    }

    protected function validateData(Request $request, $ignoreId = null): array
    {
        $validated = $request->validate([
            'code' => ['required','string','max:50', Rule::unique('promotions','code')->ignore($ignoreId)],
            'title' => ['required','string','max:100'],
            'description' => ['nullable','string','max:1000'],
            'discount_type' => ['required', Rule::in(['percent','amount'])],
            'discount_value' => ['required','numeric','min:0.01'],
            'starts_at' => ['nullable','date'],
            'ends_at' => ['nullable','date','after_or_equal:starts_at'],
            'active' => ['required','boolean'],
            'usage_limit' => ['nullable','integer','min:1'],
            'segment_rules' => ['nullable','array'],
        ]);

        // If discount_type is percent, clamp to 0-100
        if ($validated['discount_type'] === 'percent') {
            $validated['discount_value'] = min(max($validated['discount_value'], 0), 100);
        }

        return $validated;
    }
}

