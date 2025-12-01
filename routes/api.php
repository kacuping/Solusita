<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'email' => ['required','email'],
        'password' => ['required','string'],
    ]);
    $user = \App\Models\User::where('email', $validated['email'])->first();
    if (! $user || ! Hash::check($validated['password'], $user->getAuthPassword())) {
        return response()->json(['error' => 'invalid_credentials'], 401);
    }
    $token = Str::random(60);
    $user->api_token = $token;
    $user->save();
    return response()->json([
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ],
    ]);
});

// Helper: authenticate via Bearer or X-API-TOKEN
function apiUser(\Illuminate\Http\Request $request): ?\App\Models\User {
    $auth = (string) $request->header('Authorization');
    $bearer = '';
    if ($auth && preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) { $bearer = trim($m[1]); }
    $token = $bearer !== '' ? $bearer : (string) $request->header('X-API-TOKEN');
    if ($token === '') return null;
    return \App\Models\User::where('api_token', $token)->first();
}

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/me', function (\Illuminate\Http\Request $request) {
        $user = apiUser($request);
        if (! $user) return response()->json(['error' => 'unauthorized'], 401);
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    });

    Route::get('/services', function () {
        $items = \App\Models\Service::orderBy('name')->get()->map(function($s){
            return [
                'id' => $s->id,
                'name' => (string) $s->name,
                'description' => (string) ($s->description ?? ''),
                'base_price' => (float) ($s->base_price ?? 0),
                'unit_type' => (string) ($s->unit_type ?? 'Durasi'),
                'duration_minutes' => (int) ($s->duration_minutes ?? 0),
                'display_icon' => (string) ($s->display_icon ?? 'fa-solid fa-broom'),
            ];
        });
        return response()->json(['data' => $items]);
    });

    Route::get('/bookings', function (\Illuminate\Http\Request $request) {
        $user = apiUser($request);
        if (! $user) return response()->json(['error' => 'unauthorized'], 401);
        $customer = \App\Models\Customer::where('user_id', $user->id)->first();
        $list = \App\Models\Booking::where('customer_id', optional($customer)->id)
            ->with(['service','cleaner'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(function($b){
                return [
                    'id' => $b->id,
                    'service' => optional($b->service)->name,
                    'scheduled_at' => optional($b->scheduled_at)->toIso8601String(),
                    'status' => (string) ($b->status ?? ''),
                    'address' => (string) ($b->address ?? ''),
                    'notes' => (string) ($b->notes ?? ''),
                    'total_amount' => (float) ($b->total_amount ?? 0),
                ];
            });
        return response()->json(['data' => $list]);
    });

    Route::post('/bookings', function (\Illuminate\Http\Request $request) {
        $user = apiUser($request);
        if (! $user) return response()->json(['error' => 'unauthorized'], 401);
        $validated = $request->validate([
            'service_id' => ['required','exists:services,id'],
            'date' => ['required','date'],
            'time' => ['required'],
            'duration_minutes' => ['nullable','integer','min:1'],
            'address' => ['required','string','min:6'],
            'notes' => ['nullable','string'],
        ]);
        $service = \App\Models\Service::findOrFail($validated['service_id']);
        $customer = \App\Models\Customer::firstOrCreate(['user_id' => $user->id], [
            'name' => $user->name,
            'email' => $user->email,
        ]);
        $dt = \Carbon\Carbon::parse($validated['date'].' '.$validated['time']);
        $amount = (float) ($service->base_price ?? 0);
        if (strtolower((string) ($service->unit_type ?? 'Durasi')) === 'durasi') {
            $mins = (int) ($validated['duration_minutes'] ?? ($service->duration_minutes ?? 60));
            $factor = max(1, $mins / max(1, (int) ($service->duration_minutes ?? 60)));
            $amount = round($amount * $factor);
        }
        $booking = \App\Models\Booking::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'scheduled_at' => $dt,
            'status' => 'pending',
            'address' => $validated['address'],
            'notes' => (string) ($validated['notes'] ?? ''),
            'duration_minutes' => (int) ($validated['duration_minutes'] ?? ($service->duration_minutes ?? 60)),
            'total_amount' => $amount,
            'payment_status' => 'unpaid',
        ]);
        return response()->json(['data' => ['id' => $booking->id]], 201);
    });

    Route::get('/notifications', function (\Illuminate\Http\Request $request) {
        $user = apiUser($request);
        if (! $user) return response()->json(['error' => 'unauthorized'], 401);
        $customer = \App\Models\Customer::where('user_id', $user->id)->first();
        $query = \App\Models\Booking::where('customer_id', optional($customer)->id);
        $openOrders = (clone $query)->whereIn('status', ['pending','scheduled','in_progress'])->count();
        $completedOrders = (clone $query)->where('status', 'completed')->count();
        $lastChange = (clone $query)->orderByDesc('updated_at')->value('updated_at');
        $lastStatus = (clone $query)->orderByDesc('updated_at')->value('status');
        return response()->json([
            'open_orders' => (int) $openOrders,
            'completed_orders' => (int) $completedOrders,
            'last_change_at' => optional($lastChange)->toIso8601String(),
            'last_status' => (string) $lastStatus,
        ]);
    });
});
