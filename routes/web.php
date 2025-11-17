<?php

use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerHelpController;
use App\Http\Controllers\CustomerHomeController;
use App\Http\Controllers\CustomerRegistrationController;
use App\Http\Controllers\CustomerServiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Solusita: jadikan root sebagai Dashboard terproteksi
Route::get('/', function () {
    $user = auth()->user();
    if ($user && ($user->role ?? null) === 'customer') {
        return redirect()->route('customer.home');
    }

    $bookingsCount = \App\Models\Booking::count();
    $pendingCount = \App\Models\Booking::where('status', 'pending')->count();
    $scheduledToday = \App\Models\Booking::whereDate('scheduled_at', now()->toDateString())->count();
    $paidTotal = \App\Models\Booking::where('payment_status', 'paid')->sum('total_amount');
    $unpaidTotal = \App\Models\Booking::where('payment_status', 'unpaid')->sum('total_amount');
    $recentBookings = \App\Models\Booking::with(['customer', 'service'])
        ->orderByDesc('created_at')
        ->limit(10)
        ->get();

    return view('dashboard', compact('bookingsCount', 'pendingCount', 'scheduledToday', 'paidTotal', 'unpaidTotal', 'recentBookings'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Alihkan /dashboard ke route bernama dashboard (root)
Route::get('/dashboard', function () {
    return redirect()->route('dashboard');
});

Route::get('/icons/pwa/{size}.png', function ($size) {
    $size = (int) $size;
    if (! in_array($size, [192, 512], true)) {
        abort(404);
    }
    $src = public_path('icons/pic.png');
    if (! file_exists($src)) {
        abort(404);
    }
    $img = @imagecreatefrompng($src);
    if ($img === false) {
        $img = @imagecreatefromjpeg($src);
        if ($img === false) {
            abort(404);
        }
    }
    $w = imagesx($img);
    $h = imagesy($img);
    $canvas = imagecreatetruecolor($size, $size);
    imagesavealpha($canvas, true);
    $alpha = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
    imagefill($canvas, 0, 0, $alpha);
    $scale = min($size / max($w, 1), $size / max($h, 1));
    $nw = (int) max(1, round($w * $scale));
    $nh = (int) max(1, round($h * $scale));
    $dx = (int) floor(($size - $nw) / 2);
    $dy = (int) floor(($size - $nh) / 2);
    imagecopyresampled($canvas, $img, $dx, $dy, 0, 0, $nw, $nh, $w, $h);
    ob_start();
    imagepng($canvas);
    $data = ob_get_clean();
    imagedestroy($canvas);
    imagedestroy($img);
    return response($data, 200, [
        'Content-Type' => 'image/png',
        'Cache-Control' => 'public, max-age=31536000',
    ]);
});

// Halaman login & registrasi khusus pelanggan (mobile-friendly)
Route::prefix('customer')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'create'])->middleware('guest')->name('customer.login');
    Route::post('/login', [CustomerAuthController::class, 'store'])->middleware('guest')->name('customer.login.store');

    Route::get('/register', [CustomerRegistrationController::class, 'create'])->middleware('guest')->name('customer.register');
    Route::post('/register', [CustomerRegistrationController::class, 'store'])->middleware('guest')->name('customer.register.store');

    // Grup halaman pelanggan terproteksi dengan middleware khusus role 'customer'
    Route::middleware(['auth', 'verified', 'customer'])->group(function () {
        Route::get('/home', [CustomerHomeController::class, 'index'])->name('customer.home');
        // Helpdesk khusus customer (tidak melalui /support/create)
        Route::get('/help', [CustomerHelpController::class, 'create'])->name('customer.help.create');
        Route::post('/help', [CustomerHelpController::class, 'store'])->name('customer.help.store');
        Route::get('/services', [CustomerServiceController::class, 'index'])->name('customer.services.index');
        Route::get('/service/{slug}', [CustomerServiceController::class, 'show'])->name('customer.service.show');
        Route::get('/categories', function () {
            $categories = \Illuminate\Support\Facades\Schema::hasTable('service_categories')
                ? \App\Models\ServiceCategory::where('active', true)->orderBy('name')->get()
                : collect();

            return view('customer.categories.index', compact('categories'));
        })->name('customer.categories.index');
        // Pemesanan layanan (create & store)
        Route::get('/order/{slug}', function ($slug) {
            $service = \App\Models\Service::where('slug', $slug)->firstOrFail();
            $user = auth()->user();
            $customer = \App\Models\Customer::firstOrCreate(['user_id' => $user->id], [
                'name' => $user->name,
                'email' => $user->email,
            ]);

            $desc = (string) ($service->description ?? '');
            $minMinutes = (int) ($service->duration_minutes ?? 60);
            if ($desc !== '') {
                if (preg_match('/minimal\s+order\s+(\d+)\s*jam/i', $desc, $m)) {
                    $minMinutes = max($minMinutes, ((int) $m[1]) * 60);
                }
            }

            $file = storage_path('app/payment_options.json');
            $paymentOptions = [];
            if (file_exists($file)) {
                $json = file_get_contents($file);
                $paymentOptions = json_decode($json, true) ?: [];
            }
            $cashActive = session('cash_active', true);

            return view('customer.order', compact('service', 'customer', 'minMinutes', 'paymentOptions', 'cashActive'));
        })->name('customer.order.create');
        Route::post('/order', function (\Illuminate\Http\Request $request) {
            $user = auth()->user();
            $customer = \App\Models\Customer::where('user_id', $user->id)->firstOrFail();

            $validated = $request->validate([
                'service_id' => 'required|exists:services,id',
                'date' => 'required|date',
                'time' => 'required',
                'duration_minutes' => ['nullable', 'integer', 'min:1'],
                'length_m' => ['nullable', 'numeric', 'min:0.1'],
                'width_m' => ['nullable', 'numeric', 'min:0.1'],
                'qty' => ['nullable', 'integer', 'min:1'],
                'address' => 'required|string|min:6',
                'notes' => 'nullable|string',
                'promotion_code' => 'nullable|string|max:50',
                'payment_method' => ['required', 'string', 'max:100'],
            ]);

            $service = \App\Models\Service::findOrFail($validated['service_id']);
            $isDuration = strtolower(trim((string) ($service->unit_type ?? 'Durasi'))) === 'durasi';
            $desc = (string) ($service->description ?? '');
            $minMinutes = (int) ($service->duration_minutes ?? 60);
            if ($desc !== '') {
                if (preg_match('/minimal\s+order\s+(\d+)\s*jam/i', $desc, $m)) {
                    $minMinutes = max($minMinutes, ((int) $m[1]) * 60);
                }
            }
            // enforce minimum / input sesuai unit
            if ($isDuration) {
                if ((int) ($validated['duration_minutes'] ?? 0) < $minMinutes) {
                    return back()
                        ->withErrors(['duration_minutes' => 'Durasi minimal adalah '.$minMinutes.' menit untuk layanan ini.'])
                        ->withInput();
                }
            } else {
                $validated['duration_minutes'] = 0;
                $unit = strtoupper(trim((string) ($service->unit_type ?? 'SATUAN')));
                if ($unit === 'M2') {
                    if (! $request->filled('length_m') || ! $request->filled('width_m')) {
                        return back()->withErrors(['length_m' => 'Masukkan ukuran panjang & lebar dalam meter.'])->withInput();
                    }
                } else {
                    if (! $request->filled('qty')) {
                        return back()->withErrors(['qty' => 'Masukkan jumlah sesuai satuan/QTY.'])->withInput();
                    }
                }
            }

            $scheduledAt = \Carbon\Carbon::parse($validated['date'].' '.$validated['time']);

            // Validate payment method against active methods
            $file = storage_path('app/payment_options.json');
            $options = [];
            if (file_exists($file)) {
                $json = file_get_contents($file);
                $options = json_decode($json, true) ?: [];
            }
            $cashActive = session('cash_active', true);
            $allowedMethods = [];
            if (! empty($cashActive)) {
                $allowedMethods[] = 'cash';
            }
            foreach ($options as $opt) {
                if (! empty($opt['active'])) {
                    $allowedMethods[] = 'option_'.($opt['id'] ?? '');
                }
            }
            if (! in_array($validated['payment_method'], $allowedMethods, true)) {
                return back()->withErrors(['payment_method' => 'Metode pembayaran tidak tersedia.'])->withInput();
            }

            $priceUnitMinutes = 60;
            $durationVal = (int) ($validated['duration_minutes'] ?? 0);
            $subtotal = (float) ($service->base_price ?? 0);
            if ($isDuration) {
                $subtotal = (float) ($service->base_price ?? 0) * ($durationVal / $priceUnitMinutes);
            } else {
                $unit = strtoupper(trim((string) ($service->unit_type ?? 'SATUAN')));
                if ($unit === 'M2') {
                    $L = (float) ($validated['length_m'] ?? 0);
                    $W = (float) ($validated['width_m'] ?? 0);
                    $area = max($L * $W, 0);
                    $subtotal = (float) ($service->base_price ?? 0) * $area;
                } else {
                    $Q = (int) ($validated['qty'] ?? 1);
                    $subtotal = (float) ($service->base_price ?? 0) * max($Q, 1);
                }
            }
            $discount = 0.0;
            $promoCode = trim((string) ($validated['promotion_code'] ?? ''));
            if ($promoCode !== '') {
                $promo = \App\Models\Promotion::where('code', $promoCode)
                    ->where('active', true)
                    ->where(function ($q) {
                        $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                    })
                    ->first();
                if ($promo) {
                    if ($promo->discount_type === 'percent') {
                        $discount = $subtotal * ((float) $promo->discount_value) / 100.0;
                    } else {
                        $discount = (float) $promo->discount_value;
                    }
                    if ($discount < 0) {
                        $discount = 0;
                    }
                    if ($discount > $subtotal) {
                        $discount = $subtotal;
                    }
                }
            }
            $finalAmount = max($subtotal - $discount, 0);

            $booking = \App\Models\Booking::create([
                'customer_id' => $customer->id,
                'service_id' => $service->id,
                'scheduled_at' => $scheduledAt,
                'status' => 'pending',
                'address' => $validated['address'],
                'notes' => trim(($validated['notes'] ?? '')) !== ''
                    ? ($validated['notes'])
                    : null,
                'duration_minutes' => (int) ($validated['duration_minutes'] ?? 0),
                'total_amount' => $finalAmount,
                'payment_status' => 'unpaid',
                'promotion_code' => $validated['promotion_code'] ?? null,
            ]);

            // Append detail order ke notes: metode, unit/qty/ukuran, dan nomor order
            $info = [];
            $info[] = 'Metode Pembayaran: '.$validated['payment_method'];
            $unit = strtoupper(trim((string) ($service->unit_type ?? 'SATUAN')));
            if (! $isDuration) {
                if ($unit === 'M2') {
                    $L = (float) $validated['length_m'];
                    $W = (float) $validated['width_m'];
                    $info[] = 'Ukuran: Panjang '.$L.'m, Lebar '.$W.'m, Satuan: M2';
                } else {
                    $Q = (int) ($validated['qty'] ?? 1);
                    $info[] = 'Qty: '.$Q.', Satuan: '.($service->unit_type ?? 'Satuan');
                }
            }
            $booking->notes = trim(($booking->notes ? ($booking->notes.' | '.implode(' | ', $info)) : implode(' | ', $info)));
            $orderNo = 'ORD-'.now()->format('ymd').str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);
            $booking->notes = trim($booking->notes.' | Order#: '.$orderNo);
            $booking->save();

            return redirect()->route('customer.payment.show', ['booking' => $booking->id, 'method' => $validated['payment_method']]);
        })->name('customer.order.store');

        Route::get('/payment/{booking}', function (\App\Models\Booking $booking, \Illuminate\Http\Request $request) {
            $user = auth()->user();
            $customer = \App\Models\Customer::where('user_id', $user->id)->firstOrFail();
            if ($booking->customer_id !== $customer->id) {
                abort(404);
            }
            $method = (string) $request->query('method', '');
            if ($method === '') {
                $n = (string) ($booking->notes ?? '');
                if (preg_match('/Metode Pembayaran:\s*(cash|option_[a-z0-9-]+)/i', $n, $m)) {
                    $method = strtolower($m[1]);
                }
            }
            $file = storage_path('app/payment_options.json');
            $paymentOptions = [];
            if (file_exists($file)) {
                $json = file_get_contents($file);
                $paymentOptions = json_decode($json, true) ?: [];
            }
            $selectedOption = null;
            if (str_starts_with($method, 'option_')) {
                $id = substr($method, strlen('option_'));
                foreach ($paymentOptions as $opt) {
                    if ((string) ($opt['id'] ?? '') === (string) $id) {
                        $selectedOption = $opt;
                        break;
                    }
                }
            }
            $service = \App\Models\Service::find($booking->service_id);
            $orderNo = null;
            $n = (string) ($booking->notes ?? '');
            if ($n !== '' && preg_match('/Order#:\s*(ORD-[0-9]+)/i', $n, $mm)) {
                $orderNo = $mm[1];
            }

            return view('customer.payment', [
                'booking' => $booking,
                'service' => $service,
                'method' => $method,
                'paymentOption' => $selectedOption,
                'orderNo' => $orderNo,
            ]);
        })->name('customer.payment.show');

        Route::post('/payment/{booking}/confirm', function (\App\Models\Booking $booking) {
            $user = auth()->user();
            $customer = \App\Models\Customer::where('user_id', $user->id)->firstOrFail();
            if ($booking->customer_id !== $customer->id) {
                abort(404);
            }
            $booking->payment_status = 'paid';
            $booking->save();

            return redirect()->route('customer.schedule')->with('status', 'Pembayaran dikonfirmasi.');
        })->name('customer.payment.confirm');

        Route::post('/payment/{booking}/cancel', function (\App\Models\Booking $booking, \Illuminate\Http\Request $request) {
            $user = auth()->user();
            $customer = \App\Models\Customer::where('user_id', $user->id)->firstOrFail();
            if ($booking->customer_id !== $customer->id) {
                abort(404);
            }
            if ($booking->payment_status !== 'paid') {
                try {
                    if (method_exists($booking, 'reviews')) {
                        $booking->reviews()->delete();
                    }
                    $booking->delete();
                } catch (\Throwable $e) {
                    $booking->status = 'cancelled';
                    $booking->save();
                }
            }

            return redirect()->route('customer.home')->with('status', 'Order dibatalkan.');
        })->name('customer.payment.cancel');

        // Validate promo and compute discount for UI preview
        Route::get('/promo/validate', function (\Illuminate\Http\Request $request) {
            $user = auth()->user();
            $customer = \App\Models\Customer::where('user_id', $user->id)->first();
            $serviceId = (int) $request->query('service_id');
            $code = trim((string) $request->query('code'));
            $service = \App\Models\Service::find($serviceId);
            if (! $service || $code === '') {
                return response()->json(['ok' => false, 'discount' => 0]);
            }
            $amount = (float) ($request->query('amount', 0));
            $basePrice = $amount > 0 ? $amount : (float) ($service->base_price ?? 0);
            $promo = \App\Models\Promotion::where('code', $code)
                ->where('active', true)
                ->where(function ($q) {
                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                })
                ->first();
            if (! $promo) {
                return response()->json(['ok' => false, 'discount' => 0]);
            }
            $discount = 0.0;
            if ($promo->discount_type === 'percent') {
                $discount = $basePrice * ((float) $promo->discount_value) / 100.0;
            } else {
                $discount = (float) $promo->discount_value;
            }
            if ($discount < 0) {
                $discount = 0;
            }
            if ($discount > $basePrice) {
                $discount = $basePrice;
            }

            return response()->json(['ok' => true, 'discount' => round($discount, 2)]);
        })->name('customer.promo.validate');
        // Halaman profil pelanggan (versi mobile sederhana)
        Route::get('/profile', function () {
            $user = auth()->user();
            $customer = \App\Models\Customer::where('user_id', $user->id)->first();

            return view('customer.account', compact('user', 'customer'));
        })->name('customer.profile');
        // Jadwal pelanggan (versi mobile sederhana)
        Route::get('/schedule', function () {
            $user = auth()->user();
            $customer = \App\Models\Customer::where('user_id', $user->id)->first();
            $bookings = \App\Models\Booking::where('customer_id', optional($customer)->id)
                ->orderBy('scheduled_at', 'asc')
                ->get();

            return view('customer.schedule', compact('bookings', 'customer'));
        })->name('customer.schedule');
        Route::post('/logout', function (\Illuminate\Http\Request $request) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('customer.login');
        })->name('customer.logout');
        // Simple JSON notifications endpoint for polling
        Route::get('/notifications', function () {
            $user = auth()->user();
            $customer = \App\Models\Customer::where('user_id', $user->id)->first();
            $query = \App\Models\Booking::where('customer_id', optional($customer)->id);
            $openOrders = (clone $query)->whereIn('status', ['pending', 'scheduled', 'in_progress'])->count();
            $completedOrders = (clone $query)->where('status', 'completed')->count();
            $lastChange = (clone $query)->orderByDesc('updated_at')->value('updated_at');

            return response()->json([
                'open_orders' => $openOrders,
                'completed_orders' => $completedOrders,
                'last_change_at' => optional($lastChange)->toIso8601String(),
            ]);
        })->name('customer.notifications');
        // Update profil pelanggan (inline edit)
        Route::post('/profile/update', function (Request $request) {
            $user = auth()->user();
            $customer = \App\Models\Customer::firstOrCreate(['user_id' => $user->id]);

            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:25',
                'address' => 'nullable|string|max:255',
                'dob' => 'nullable|date',
            ]);

            if (array_key_exists('name', $validated)) {
                $user->name = $validated['name'];
                $customer->name = $validated['name'];
            }
            if (array_key_exists('email', $validated)) {
                $user->email = $validated['email'];
                $customer->email = $validated['email'];
            }
            $user->save();

            $customer->phone = $validated['phone'] ?? $customer->phone;
            $customer->address = $validated['address'] ?? $customer->address;
            $customer->dob = $validated['dob'] ?? $customer->dob;
            $customer->save();

            return back()->with('status', 'Profil diperbarui');
        })->name('customer.profile.update');
        // Upload avatar pelanggan
        Route::post('/profile/avatar', function (Request $request) {
            $user = auth()->user();
            $customer = \App\Models\Customer::firstOrCreate(['user_id' => $user->id]);

            $validated = $request->validate([
                'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('avatars', 'public');
                $url = Storage::url($path); // e.g., /storage/avatars/filename.jpg
                $customer->avatar = $url;
                $customer->save();
            }

            return back()->with('status', 'Foto profil diperbarui');
        })->name('customer.profile.avatar');
        Route::get('/avatar/{customer}', function (\App\Models\Customer $customer) {
            $path = (string) ($customer->avatar ?? '');
            if ($path === '') {
                abort(404);
            }
            $rel = is_string($path) ? preg_replace('#^/storage/#', '', $path) : '';
            if ($rel === '') {
                abort(404);
            }
            if (! Storage::disk('public')->exists($rel)) {
                abort(404);
            }

            return Storage::disk('public')->response($rel);
        })->name('customer.avatar');
        // Tambahkan route /customer lainnya di sini ke depannya
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Solusita: daftar pesanan/booking
    Route::get('/bookings', function (Request $request) {
        $query = \App\Models\Booking::query()
            ->with(['customer', 'service', 'cleaner'])
            ->orderByDesc('created_at');

        // Optional filter via query string ?status=pending|scheduled|completed|cancelled
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        // Optional filter by customer name/email
        if ($request->filled('q')) {
            $q = trim($request->string('q'));
            $query->whereHas('customer', function ($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%");
            });
        }

        $bookings = $query->paginate(15)->withQueryString();
        $cleaners = \App\Models\Cleaner::where('active', true)->orderBy('full_name')->get();

        // Load payment options to map option_<id> into human-readable labels
        $file = storage_path('app/payment_options.json');
        $paymentOptions = [];
        if (file_exists($file)) {
            $json = file_get_contents($file);
            $paymentOptions = json_decode($json, true) ?: [];
        }

        $assistantNames = [];
        $paymentMethods = [];
        foreach ($bookings as $b) {
            $notes = (string) ($b->notes ?? '');
            if ($notes !== '' && preg_match('/assistants\s*:\s*([^|]+)/i', $notes, $m)) {
                $ids = array_values(array_filter(array_map(function ($v) {
                    return (int) trim((string) $v);
                }, explode(',', trim((string) $m[1])))));
                if (! empty($ids)) {
                    $names = \App\Models\Cleaner::whereIn('id', $ids)->pluck('full_name')->filter()->values()->all();
                    if (empty($names)) {
                        $names = \App\Models\Cleaner::whereIn('id', $ids)->pluck('name')->filter()->values()->all();
                    }
                    $assistantNames[$b->id] = $names;
                }
            }
            if ($notes !== '' && preg_match('/Metode\s+Pembayaran\s*:\s*([^|]+)/i', $notes, $mm)) {
                $raw = strtolower(trim((string) $mm[1]));
                if ($raw === 'cash') {
                    $paymentMethods[$b->id] = 'Tunai (Cash)';
                } elseif (str_starts_with($raw, 'option_')) {
                    $id = substr($raw, strlen('option_'));
                    $label = null;
                    foreach ($paymentOptions as $opt) {
                        if ((string) ($opt['id'] ?? '') === (string) $id) {
                            $label = $opt['label'] ?? null;
                            break;
                        }
                    }
                    $paymentMethods[$b->id] = $label ? $label : $raw;
                } else {
                    $paymentMethods[$b->id] = $raw;
                }
            }
        }

        $assistantSlots = [];
        foreach ($bookings as $b) {
            $svcName = optional($b->service)->name ?? '';
            $needed = 1;
            if ($svcName && preg_match('/(\d+)\s*Cleaner/i', (string) $svcName, $sm)) {
                $needed = max(1, (int) $sm[1]);
            }
            $assistantSlots[$b->id] = max(0, $needed - 1);
        }

        $statusOptions = [
            'pending' => 'Pending',
            'scheduled' => 'Terjadwal',
            'in_progress' => 'Berjalan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        return view('bookings', [
            'bookings' => $bookings,
            'cleaners' => $cleaners,
            'assistantNames' => $assistantNames,
            'assistantSlots' => $assistantSlots,
            'paymentMethods' => $paymentMethods,
            'statusOptions' => $statusOptions,
        ]);
    })->name('bookings.index');

    // Assign petugas untuk booking
    Route::patch('/bookings/{booking}/assign', function (Request $request, \App\Models\Booking $booking) {
        $data = $request->validate([
            'cleaner_id' => ['nullable', 'exists:cleaners,id'],
            'assistants' => ['array'],
            'assistants.*' => ['nullable', 'exists:cleaners,id'],
        ]);
        $booking->cleaner_id = $data['cleaner_id'] ?? null;
        // Opsional: ketika assign, ubah status menjadi 'scheduled' jika masih pending
        if ($booking->status === 'pending' && ($booking->cleaner_id !== null)) {
            $booking->status = 'scheduled';
        }
        // Simpan asisten tambahan ke notes agar bisa ditampilkan
        $assistants = collect($data['assistants'] ?? [])->filter(fn ($v) => ! is_null($v))->map(fn ($v) => (int) $v)->values();
        if ($assistants->count() > 0) {
            $existing = (string) ($booking->notes ?? '');
            $pattern = '/assistants\s*:\s*[^|]*/i';
            $assistLine = 'assistants: '.$assistants->implode(',');
            if ($existing === '') {
                $booking->notes = $assistLine;
            } else {
                if (preg_match($pattern, $existing)) {
                    $booking->notes = preg_replace($pattern, $assistLine, $existing) ?: $existing;
                } else {
                    $booking->notes = trim($existing.' | '.$assistLine);
                }
            }
        }
        $booking->save();

        return back()->with('success', 'Petugas berhasil di-assign untuk booking #'.$booking->id);
    })->name('bookings.assign');

    // Ubah status booking
    Route::patch('/bookings/{booking}/status', function (Request $request, \App\Models\Booking $booking) {
        $data = $request->validate([
            'status' => ['required', 'in:pending,scheduled,in_progress,completed,cancelled'],
        ]);
        $booking->status = $data['status'];
        $booking->save();

        return back()->with('success', 'Status booking #'.$booking->id.' diperbarui.');
    })->name('bookings.status');

    Route::delete('/bookings/{booking}', function (Request $request, \App\Models\Booking $booking) {
        try {
            if (method_exists($booking, 'reviews')) {
                $booking->reviews()->delete();
            }
            $booking->delete();

            return back()->with('success', 'Jadwal #'.$booking->id.' dihapus.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Tidak dapat menghapus jadwal karena terkait data lain.');
        }
    })->name('bookings.destroy');

    // Manajemen layanan (admin/staff)
    Route::resource('services', \App\Http\Controllers\AdminServiceController::class)->names([
        'index' => 'services.index',
        'store' => 'services.store',
        'update' => 'services.update',
        'destroy' => 'services.destroy',
    ])->except(['create', 'show', 'edit']);

    Route::resource('service-categories', \App\Http\Controllers\AdminServiceCategoryController::class)->names([
        'index' => 'service-categories.index',
        'store' => 'service-categories.store',
        'update' => 'service-categories.update',
        'destroy' => 'service-categories.destroy',
    ])->except(['create', 'show', 'edit']);

    Route::get('/service-categories/{service_category}/image', [\App\Http\Controllers\AdminServiceCategoryController::class, 'image'])
        ->name('service-categories.image');

    // Daftar pelanggan (admin/staff)
    Route::get('/customers', [\App\Http\Controllers\CustomerAdminController::class, 'index'])->name('customers.index');

    // Petugas (Cleaners) CRUD
    Route::resource('cleaners', \App\Http\Controllers\CleanerController::class)->names([
        'index' => 'cleaners.index',
        'create' => 'cleaners.create',
        'store' => 'cleaners.store',
        'edit' => 'cleaners.edit',
        'update' => 'cleaners.update',
        'destroy' => 'cleaners.destroy',
    ]);
    // Approval actions for cleaners
    Route::patch('/cleaners/{cleaner}/approve', [\App\Http\Controllers\CleanerController::class, 'approve'])->name('cleaners.approve');
    Route::patch('/cleaners/{cleaner}/reject', [\App\Http\Controllers\CleanerController::class, 'reject'])->name('cleaners.reject');

    Route::get('/schedule', function (Request $request) {
        $month = $request->string('month') ?: now()->format('Y-m');
        try {
            $start = \Carbon\Carbon::createFromFormat('Y-m', (string) $month)->startOfMonth();
        } catch (\Throwable $e) {
            $start = now()->startOfMonth();
        }
        $end = (clone $start)->endOfMonth();

        $bookings = \App\Models\Booking::with(['customer', 'service', 'cleaner'])
            ->whereBetween('scheduled_at', [$start, $end])
            ->orderBy('scheduled_at')
            ->get();

        $byDay = $bookings->groupBy(function ($b) {
            return optional($b->scheduled_at)->toDateString();
        });

        $services = \App\Models\Service::orderBy('name')->get();
        $customers = \App\Models\Customer::orderBy('name')->get();
        $cleaners = \App\Models\Cleaner::where('active', true)->orderBy('full_name')->get();

        return view('schedule', [
            'bookings' => $bookings,
            'byDay' => $byDay,
            'monthStart' => $start,
            'monthEnd' => $end,
            'services' => $services,
            'customers' => $customers,
            'cleaners' => $cleaners,
        ]);
    })->name('schedule.index');

    Route::post('/bookings/quick-create', function (Request $request) {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'service_id' => ['required', 'exists:services,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'address' => ['required', 'string', 'min:6'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'cleaner_id' => ['nullable', 'exists:cleaners,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);
        $dt = \Carbon\Carbon::parse($data['date'].' '.$data['time']);
        $service = \App\Models\Service::find($data['service_id']);
        $booking = new \App\Models\Booking;
        $booking->service_id = (int) $data['service_id'];
        $booking->customer_id = (int) $data['customer_id'];
        $booking->cleaner_id = $data['cleaner_id'] ?? null;
        $booking->scheduled_at = $dt;
        $booking->status = 'scheduled';
        $booking->address = (string) $data['address'];
        $booking->duration_minutes = (int) ($data['duration_minutes'] ?? (optional($service)->duration_minutes ?? 0));
        $booking->total_amount = optional($service)->base_price ?? 0;
        $booking->payment_status = 'unpaid';
        $booking->notes = $data['notes'] ?? null;
        $booking->save();

        return back()->with('success', 'Jadwal baru ditambahkan untuk tanggal '.$dt->format('d M Y H:i').'.');
    })->name('bookings.quick_create');

    Route::get('/payments', function (Request $request) {
        $query = \App\Models\Booking::query()
            ->with(['customer', 'service'])
            ->orderByDesc('created_at');

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->string('payment_status'));
        }

        if ($request->filled('q')) {
            $q = trim($request->string('q'));
            $query->whereHas('customer', function ($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%");
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        $bookings = $query->paginate(15)->withQueryString();

        $summary = [
            'paid_total' => \App\Models\Booking::where('payment_status', 'paid')->sum('total_amount'),
            'unpaid_total' => \App\Models\Booking::where('payment_status', 'unpaid')->sum('total_amount'),
            'failed_total' => \App\Models\Booking::where('payment_status', 'failed')->sum('total_amount'),
            'refunded_total' => \App\Models\Booking::where('payment_status', 'refunded')->sum('total_amount'),
        ];

        $file = storage_path('app/payment_options.json');
        $paymentOptions = [];
        if (file_exists($file)) {
            $json = file_get_contents($file);
            $paymentOptions = json_decode($json, true) ?: [];
        }
        $cashActive = session('cash_active', true);

        return view('payments', compact('bookings', 'summary', 'paymentOptions', 'cashActive'));
    })->name('payments.index');

    Route::patch('/payments/{booking}/status', function (Request $request, \App\Models\Booking $booking) {
        $data = $request->validate([
            'payment_status' => ['required', 'in:unpaid,paid,refunded,failed'],
        ]);
        $booking->payment_status = $data['payment_status'];
        $booking->save();

        return back()->with('success', 'Status pembayaran booking #'.$booking->id.' diperbarui.');
    })->name('payments.status');

    // Promotions CRUD
    Route::resource('promotions', \App\Http\Controllers\PromotionsController::class)->names([
        'index' => 'promotions.index',
        'create' => 'promotions.create',
        'store' => 'promotions.store',
        'edit' => 'promotions.edit',
        'update' => 'promotions.update',
        'destroy' => 'promotions.destroy',
    ])->except(['show']);

    // Reviews listing & moderation
    Route::get('/reviews', [\App\Http\Controllers\ReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}/approve', [\App\Http\Controllers\ReviewController::class, 'approve'])->name('reviews.approve');
    Route::patch('/reviews/{review}/reject', [\App\Http\Controllers\ReviewController::class, 'reject'])->name('reviews.reject');

    // Support ticket system
    Route::get('/support', [\App\Http\Controllers\TicketController::class, 'index'])->name('support.index');
    Route::get('/support/create', [\App\Http\Controllers\TicketController::class, 'create'])->name('support.create');
    Route::post('/support', [\App\Http\Controllers\TicketController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}', [\App\Http\Controllers\TicketController::class, 'show'])->name('support.show');
    Route::patch('/support/{ticket}/status', [\App\Http\Controllers\TicketController::class, 'updateStatus'])->name('support.status');
    Route::patch('/support/{ticket}/assign', [\App\Http\Controllers\TicketController::class, 'assign'])->name('support.assign');
    Route::post('/support/{ticket}/attach', [\App\Http\Controllers\TicketController::class, 'addAttachment'])->name('support.attach');
    Route::delete('/support/{ticket}/attach/{attachmentId}', [\App\Http\Controllers\TicketController::class, 'destroyAttachment'])->name('support.attach.delete');

    Route::get('/settings', function () {
        $settings = session('settings', [
            'company_name' => 'Solusita',
            'service_area' => 'Bekasi & Sekitar',
            'notify_email' => 'admin@solusita.local',
            'enable_notifications' => true,
        ]);
        $file = storage_path('app/payment_options.json');
        $paymentOptions = [];
        if (file_exists($file)) {
            $json = file_get_contents($file);
            $paymentOptions = json_decode($json, true) ?: [];
        }

        return view('settings', compact('settings', 'paymentOptions'));
    })->name('settings.index');

    Route::post('/settings', function (Request $request) {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:100'],
            'service_area' => ['required', 'string', 'max:200'],
            'notify_email' => ['required', 'email'],
            'enable_notifications' => ['nullable'],
        ]);
        $data['enable_notifications'] = $request->boolean('enable_notifications');
        session(['settings' => $data]);

        return back()->with('status', 'Pengaturan disimpan.');
    })->name('settings.save');

    Route::post('/settings/payment-options', function (Request $request) {
        $action = (string) $request->string('action');
        if ($action === '') {
            if ($request->has('id')) {
                $action = 'update';
            } elseif ($request->has('type') || $request->has('label')) {
                $action = 'create';
            }
        }
        $file = storage_path('app/payment_options.json');
        $options = [];
        if (file_exists($file)) {
            $json = file_get_contents($file);
            $options = json_decode($json, true) ?: [];
        }

        if ($action === 'create') {
            $data = $request->validate([
                'type' => ['required', 'in:transfer,qris'],
                'label' => ['required', 'string', 'max:100'],
                'bank_name' => ['nullable', 'string', 'max:100'],
                'bank_account_name' => ['nullable', 'string', 'max:100'],
                'bank_account_number' => ['nullable', 'string', 'max:50'],
                'qris_image' => ['nullable', 'image', 'max:2048'],
            ]);
            $id = \Illuminate\Support\Str::uuid()->toString();
            $qrisPath = null;
            if ($data['type'] === 'qris') {
                $data['bank_name'] = null;
                $data['bank_account_name'] = null;
                $data['bank_account_number'] = null;
            }
            if ($data['type'] === 'qris' && $request->hasFile('qris_image')) {
                $dest = public_path('uploads/payment_options');
                if (! is_dir($dest)) {
                    @mkdir($dest, 0775, true);
                }
                $fn = \Illuminate\Support\Str::random(12).'_'.$request->file('qris_image')->getClientOriginalName();
                $request->file('qris_image')->move($dest, $fn);
                $qrisPath = 'uploads/payment_options/'.$fn;
            }
            if ($data['type'] === 'transfer') {
                $qrisPath = null;
            }
            $options[] = [
                'id' => $id,
                'type' => $data['type'],
                'label' => $data['label'],
                'bank_name' => $data['bank_name'] ?? null,
                'bank_account_name' => $data['bank_account_name'] ?? null,
                'bank_account_number' => $data['bank_account_number'] ?? null,
                'qris_image_path' => $qrisPath,
                'active' => true,
            ];
            file_put_contents($file, json_encode($options, JSON_PRETTY_PRINT));

            return redirect()->route('settings.index')->with('status', 'Pilihan pembayaran ditambahkan.');
        }

        if ($action === 'update') {
            $id = $request->string('id');
            $data = $request->validate([
                'type' => ['required', 'in:transfer,qris'],
                'label' => ['required', 'string', 'max:100'],
                'bank_name' => ['nullable', 'string', 'max:100'],
                'bank_account_name' => ['nullable', 'string', 'max:100'],
                'bank_account_number' => ['nullable', 'string', 'max:50'],
                'qris_image' => ['nullable', 'image', 'max:2048'],
                'active' => ['nullable'],
            ]);
            foreach ($options as &$opt) {
                if (($opt['id'] ?? null) === (string) $id) {
                    $opt['type'] = $data['type'];
                    $opt['label'] = $data['label'];
                    if ($data['type'] === 'qris') {
                        $opt['bank_name'] = null;
                        $opt['bank_account_name'] = null;
                        $opt['bank_account_number'] = null;
                    } else {
                        $opt['bank_name'] = $data['bank_name'] ?? null;
                        $opt['bank_account_name'] = $data['bank_account_name'] ?? null;
                        $opt['bank_account_number'] = $data['bank_account_number'] ?? null;
                    }
                    $opt['active'] = $request->boolean('active');
                    if ($data['type'] === 'qris' && $request->hasFile('qris_image')) {
                        $dest = public_path('uploads/payment_options');
                        if (! is_dir($dest)) {
                            @mkdir($dest, 0775, true);
                        }
                        $fn = \Illuminate\Support\Str::random(12).'_'.$request->file('qris_image')->getClientOriginalName();
                        $request->file('qris_image')->move($dest, $fn);
                        $opt['qris_image_path'] = 'uploads/payment_options/'.$fn;
                    }
                    if ($data['type'] === 'transfer') {
                        $opt['qris_image_path'] = null;
                    }
                    break;
                }
            }
            file_put_contents($file, json_encode($options, JSON_PRETTY_PRINT));

            return redirect()->route('settings.index')->with('status', 'Pilihan pembayaran diperbarui.');
        }

        if ($action === 'delete') {
            $id = $request->string('id');
            $options = array_values(array_filter($options, function ($o) use ($id) {
                return ($o['id'] ?? null) !== (string) $id;
            }));
            file_put_contents($file, json_encode($options, JSON_PRETTY_PRINT));

            return redirect()->route('settings.index')->with('status', 'Pilihan pembayaran dihapus.');
        }

        return redirect()->route('settings.index')->with('error', 'Aksi tidak dikenal.');
    })->name('settings.payment_options');

    Route::post('/payments/methods/active', function (Request $request) {
        $ids = (array) $request->input('active_ids', []);
        $file = storage_path('app/payment_options.json');
        $options = [];
        if (file_exists($file)) {
            $json = file_get_contents($file);
            $options = json_decode($json, true) ?: [];
        }
        $ids = array_map('strval', $ids);
        foreach ($options as &$opt) {
            $opt['active'] = in_array((string) ($opt['id'] ?? ''), $ids, true);
        }
        session(['cash_active' => $request->boolean('cash_active')]);
        file_put_contents($file, json_encode($options, JSON_PRETTY_PRINT));

        return back()->with('status', 'Metode pembayaran aktif diperbarui.');
    })->name('payments.methods.active');

    // Manajemen User (CRUD) - Hanya admin melalui proteksi di controller
    Route::resource('users', UserController::class)->names([
        'index' => 'users.index',
        'create' => 'users.create',
        'store' => 'users.store',
        'edit' => 'users.edit',
        'update' => 'users.update',
        'destroy' => 'users.destroy',
    ]);

    // Role & Permission management
    Route::get('/roles', [\App\Http\Controllers\RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/{role}/permissions', [\App\Http\Controllers\RoleController::class, 'editPermissions'])->name('roles.permissions.edit');
    Route::put('/roles/{role}/permissions', [\App\Http\Controllers\RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

    // User permission overrides
    Route::get('/users/{user}/permissions', [\App\Http\Controllers\UserPermissionController::class, 'edit'])->name('users.permissions.edit');
    Route::put('/users/{user}/permissions', [\App\Http\Controllers\UserPermissionController::class, 'update'])->name('users.permissions.update');

    // Approval (persetujuan verifikasi user)
    Route::get('/approvals', [\App\Http\Controllers\ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('/approvals/users/{user}/approve', [\App\Http\Controllers\ApprovalController::class, 'approve'])->name('approvals.users.approve');
    // Approval (petugas) via halaman approvals
    Route::post('/approvals/cleaners/{cleaner}/approve', [\App\Http\Controllers\ApprovalController::class, 'approveCleaner'])->name('approvals.cleaners.approve');
});

require __DIR__.'/auth.php';
