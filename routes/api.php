<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

Route::post('/customers/login', function (\Illuminate\Http\Request $request) {
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

Route::post('/customers/register', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'name' => ['required','string','max:255'],
        'email' => ['required','string','lowercase','email','max:255','unique:App\\Models\\User,email'],
        'password' => ['required','string','min:6'],
        'phone' => ['nullable','string','max:30'],
        'address' => ['nullable','string','max:1000'],
    ]);
    $user = \App\Models\User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        'role' => 'customer',
    ]);
    \App\Models\Customer::create([
        'user_id' => $user->id,
        'name' => $validated['name'],
        'email' => $validated['email'],
        'phone' => $request->input('phone'),
        'address' => $request->input('address'),
    ]);
    $token = \Illuminate\Support\Str::random(60);
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
    ], 201);
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
        $customer = \App\Models\Customer::where('user_id', $user->id)->first();
        $avatarUrl = null;
        if ($customer) {
            $av = (string) ($customer->avatar ?? '');
            if ($av !== '') {
                if (preg_match('/^https?:\/\//i', $av)) {
                    $avatarUrl = $av;
                } else {
                    try {
                        $disk = \Illuminate\Support\Facades\Storage::disk('public');
                        $avatarUrl = $disk->exists($av) ? $disk->url($av) : url('/storage/'.$av);
                    } catch (\Throwable $e) {
                        $avatarUrl = url('/storage/'.$av);
                    }
                }
            }
        }
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'customer' => $customer ? [
                'id' => $customer->id,
                'name' => (string) ($customer->name ?? ''),
                'email' => (string) ($customer->email ?? ''),
                'phone' => (string) ($customer->phone ?? ''),
                'address' => (string) ($customer->address ?? ''),
                'avatar' => (string) ($customer->avatar ?? ''),
                'avatar_url' => $avatarUrl,
                'dob' => optional($customer->dob)->toDateString(),
                'notes' => (string) ($customer->notes ?? ''),
            ] : null,
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

    Route::get('/cleaners', function () {
        $nameColumn = \Illuminate\Support\Facades\Schema::hasColumn('cleaners', 'full_name') ? 'full_name' : 'name';
        $items = \App\Models\Cleaner::where('active', true)->orderBy($nameColumn)->get()->map(function($c) use ($nameColumn){
            return [
                'id' => $c->id,
                'name' => (string) ($c->{$nameColumn} ?? ''),
                'phone' => (string) ($c->phone ?? ''),
                'address' => (string) ($c->address ?? ''),
                'active' => (bool) ($c->active ?? true),
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
                    'payment_status' => (string) ($b->payment_status ?? ''),
                    'dp_status' => (string) ($b->dp_status ?? ''),
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

    Route::get('/admin/tables', function (\Illuminate\Http\Request $request) {
        $user = apiUser($request);
        if (! $user) return response()->json(['error' => 'unauthorized'], 401);
        $r = strtolower((string) ($user->role ?? ''));
        if (! in_array($r, ['administrator','admin'], true)) return response()->json(['error' => 'forbidden'], 403);
        $rows = DB::select('SHOW TABLES');
        $tables = [];
        foreach ($rows as $row) {
            foreach ((array) $row as $v) { $tables[] = (string) $v; }
        }
        return response()->json(['tables' => $tables]);
    });

    Route::get('/admin/columns/{name}', function (\Illuminate\Http\Request $request, $name) {
        $user = apiUser($request);
        if (! $user) return response()->json(['error' => 'unauthorized'], 401);
        $r = strtolower((string) ($user->role ?? ''));
        if (! in_array($r, ['administrator','admin'], true)) return response()->json(['error' => 'forbidden'], 403);
        $nm = (string) $name;
        if (! preg_match('/^[A-Za-z0-9_]+$/', $nm)) { return response()->json(['error' => 'invalid_table'], 400); }
        if (! Schema::hasTable($nm)) { return response()->json(['error' => 'not_found'], 404); }
        $columns = Schema::getColumnListing($nm);
        $types = [];
        $meta = [];
        try {
            $dbRow = DB::select('select database() as db');
            $currDb = is_array($dbRow) && isset($dbRow[0]) ? ((array)$dbRow[0])['db'] ?? null : null;
            if ($currDb) {
                $rows = DB::select('SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?', [$currDb, $nm]);
                foreach ($rows as $row) {
                    $arr = (array) $row;
                    $types[(string) $arr['COLUMN_NAME']] = (string) $arr['COLUMN_TYPE'];
                    $meta[(string) $arr['COLUMN_NAME']] = [
                        'type' => (string) $arr['COLUMN_TYPE'],
                        'nullable' => (string) $arr['IS_NULLABLE'] === 'YES',
                        'default' => $arr['COLUMN_DEFAULT'],
                    ];
                }
            }
        } catch (\Throwable $e) { /* ignore */ }
        return response()->json(['table' => $nm, 'columns' => $columns, 'types' => $types, 'meta' => $meta]);
    });

    Route::get('/admin/table/{name}', function (\Illuminate\Http\Request $request, $name) {
        $user = apiUser($request);
        if (! $user) return response()->json(['error' => 'unauthorized'], 401);
        $r = strtolower((string) ($user->role ?? ''));
        if (! in_array($r, ['administrator','admin'], true)) return response()->json(['error' => 'forbidden'], 403);
        $nm = (string) $name;
        if (! preg_match('/^[A-Za-z0-9_]+$/', $nm)) { return response()->json(['error' => 'invalid_table'], 400); }
        try {
            $limit = max(1, (int) $request->query('limit', 500));
            $page = max(1, (int) $request->query('page', 1));
            $offset = max(0, ($page - 1) * $limit);
            $data = DB::table($nm)->offset($offset)->limit($limit)->get();
            $total = null;
            if ($request->boolean('include_total')) { $total = DB::table($nm)->count(); }
            return response()->json(['table' => $nm, 'page' => (int) $page, 'limit' => (int) $limit, 'total' => $total, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'query_failed'], 500);
        }
    });

    Route::get('/admin/export', function (\Illuminate\Http\Request $request) {
        $user = apiUser($request);
        if (! $user) return response()->json(['error' => 'unauthorized'], 401);
        $r = strtolower((string) ($user->role ?? ''));
        if (! in_array($r, ['administrator','admin'], true)) return response()->json(['error' => 'forbidden'], 403);
        $rows = DB::select('SHOW TABLES');
        $tables = [];
        foreach ($rows as $row) { foreach ((array) $row as $v) { $tables[] = (string) $v; } }
        $limit = max(1, (int) $request->query('limit_per_table', 1000));
        $out = [];
        foreach ($tables as $t) {
            try { $out[$t] = DB::table($t)->limit($limit)->get(); } catch (\Throwable $e) { $out[$t] = []; }
        }
        return response()->json(['data' => $out]);
    });

    Route::get('/tables', function (\Illuminate\Http\Request $request) {
        $user = apiUser($request);
        if (! $user) return response()->json(['error' => 'unauthorized'], 401);
        $r = strtolower((string) ($user->role ?? ''));
        if (in_array($r, ['administrator','admin'], true)) {
            $rows = DB::select('SHOW TABLES');
            $tables = [];
            foreach ($rows as $row) { foreach ((array) $row as $v) { $tables[] = (string) $v; } }
            return response()->json(['tables' => $tables]);
        }
        $allow = ['services','service_categories','promotions','popups','cleaners','reviews'];
        $tables = [];
        foreach ($allow as $t) { if (Schema::hasTable($t)) { $tables[] = $t; } }
        return response()->json(['tables' => $tables]);
    });

    Route::get('/table/{name}', function (\Illuminate\Http\Request $request, $name) {
        $user = apiUser($request);
        if (! $user) return response()->json(['error' => 'unauthorized'], 401);
        $r = strtolower((string) ($user->role ?? ''));
        $nm = (string) $name;
        if (! preg_match('/^[A-Za-z0-9_]+$/', $nm)) { return response()->json(['error' => 'invalid_table'], 400); }
        if (! Schema::hasTable($nm)) { return response()->json(['error' => 'not_found'], 404); }
        if (! in_array($r, ['administrator','admin'], true)) {
            $allow = ['services','service_categories','promotions','popups','cleaners','reviews'];
            if (! in_array($nm, $allow, true)) { return response()->json(['error' => 'forbidden'], 403); }
        }
        try {
            $limit = max(1, (int) $request->query('limit', 500));
            $page = max(1, (int) $request->query('page', 1));
            $offset = max(0, ($page - 1) * $limit);
            $data = DB::table($nm)->offset($offset)->limit($limit)->get();
            $total = null;
            if ($request->boolean('include_total')) { $total = DB::table($nm)->count(); }
            return response()->json(['table' => $nm, 'page' => (int) $page, 'limit' => (int) $limit, 'total' => $total, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'query_failed'], 500);
        }
    });

    Route::get('/export', function (\Illuminate\Http\Request $request) {
        $user = apiUser($request);
        if (! $user) return response()->json(['error' => 'unauthorized'], 401);
        $r = strtolower((string) ($user->role ?? ''));
        $tables = [];
        if (in_array($r, ['administrator','admin'], true)) {
            $rows = DB::select('SHOW TABLES');
            foreach ($rows as $row) { foreach ((array) $row as $v) { $tables[] = (string) $v; } }
        } else {
            $allow = ['services','service_categories','promotions','popups','cleaners','reviews'];
            foreach ($allow as $t) { if (Schema::hasTable($t)) { $tables[] = $t; } }
        }
        $limit = max(1, (int) $request->query('limit_per_table', 1000));
        $out = [];
        foreach ($tables as $t) {
            try { $out[$t] = DB::table($t)->limit($limit)->get(); } catch (\Throwable $e) { $out[$t] = []; }
        }
        return response()->json(['data' => $out]);
    });
});
