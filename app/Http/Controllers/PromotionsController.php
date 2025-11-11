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
            // Accept JSON string or array for segment rules
            'segment_rules' => ['nullable'],
            // Simple segmentation builder fields (optional)
            'sr_new_customer' => ['nullable'],
            'sr_age_mode' => ['nullable','in:max,min'], // max => ≤ N days, min => ≥ N days
            'sr_age_days' => ['nullable','integer','min:1'],
            'sr_min_past_bookings' => ['nullable','integer','min:1'],
        ]);

        // If discount_type is percent, clamp to 0-100
        if ($validated['discount_type'] === 'percent') {
            $validated['discount_value'] = min(max($validated['discount_value'], 0), 100);
        }

        // Build segment_rules from simple fields if provided
        $builtRules = [];
        if (!empty($validated['sr_new_customer'])) {
            $builtRules['new_customer'] = true;
        }
        if (!empty($validated['sr_age_days'])) {
            $mode = $validated['sr_age_mode'] ?? 'max';
            $days = (int) $validated['sr_age_days'];
            if ($days > 0) {
                if ($mode === 'min') {
                    $builtRules['min_days_since_registration'] = $days;
                } else {
                    $builtRules['max_days_since_registration'] = $days; // default ≤ N days
                }
            }
        }
        if (!empty($validated['sr_min_past_bookings'])) {
            $min = (int) $validated['sr_min_past_bookings'];
            if ($min > 0) {
                $builtRules['min_past_bookings'] = $min;
            }
        }

        if (!empty($builtRules)) {
            $validated['segment_rules'] = $builtRules;
        } else {
            // Normalize segment_rules: decode JSON if provided as string
            if (isset($validated['segment_rules'])) {
                if (is_string($validated['segment_rules'])) {
                    try {
                        $decoded = json_decode($validated['segment_rules'], true, 512, JSON_THROW_ON_ERROR);
                        $validated['segment_rules'] = is_array($decoded) ? $decoded : null;
                    } catch (\Throwable $e) {
                        $validated['segment_rules'] = null;
                    }
                } elseif (!is_array($validated['segment_rules'])) {
                    $validated['segment_rules'] = null;
                }
            }
        }

        // Remove builder-only keys from final payload
        unset($validated['sr_new_customer'], $validated['sr_age_mode'], $validated['sr_age_days'], $validated['sr_min_past_bookings']);

        return $validated;
    }
}
