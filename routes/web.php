<?php

use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerHomeController;
use App\Http\Controllers\CustomerHelpController;
use App\Http\Controllers\CustomerRegistrationController;
use App\Http\Controllers\CustomerServiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Solusita: jadikan root sebagai Dashboard terproteksi
Route::get('/', function () {
    // Jika user adalah pelanggan, arahkan ke dashboard pelanggan
    $user = auth()->user();
    if ($user && ($user->role ?? null) === 'customer') {
        return redirect()->route('customer.home');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Alihkan /dashboard ke route bernama dashboard (root)
Route::get('/dashboard', function () {
    return redirect()->route('dashboard');
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
        // Pemesanan layanan (create & store)
        Route::get('/order/{slug}', function ($slug) {
            $service = \App\Models\Service::where('slug', $slug)->firstOrFail();
            $user = auth()->user();
            $customer = \App\Models\Customer::firstOrCreate(['user_id' => $user->id], [
                'name' => $user->name,
                'email' => $user->email,
            ]);

            return view('customer.order', compact('service', 'customer'));
        })->name('customer.order.create');
        Route::post('/order', function (\Illuminate\Http\Request $request) {
            $user = auth()->user();
            $customer = \App\Models\Customer::where('user_id', $user->id)->firstOrFail();

            $validated = $request->validate([
                'service_id' => 'required|exists:services,id',
                'date' => 'required|date',
                'time' => 'required',
                'address' => 'required|string|min:6',
                'notes' => 'nullable|string',
                'promotion_code' => 'nullable|string|max:50',
            ]);

            $service = \App\Models\Service::findOrFail($validated['service_id']);
            $scheduledAt = \Carbon\Carbon::parse($validated['date'].' '.$validated['time']);

            $booking = \App\Models\Booking::create([
                'customer_id' => $customer->id,
                'service_id' => $service->id,
                'scheduled_at' => $scheduledAt,
                'status' => 'pending',
                'address' => $validated['address'],
                'notes' => $validated['notes'] ?? null,
                'duration_minutes' => $service->duration_minutes,
                'total_amount' => $service->base_price,
                'payment_status' => 'unpaid',
                'promotion_code' => $validated['promotion_code'] ?? null,
            ]);

            return redirect()->route('customer.schedule')->with('status', 'Pesanan berhasil dibuat.');
        })->name('customer.order.store');
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
        // Simple JSON notifications endpoint for polling
        Route::get('/notifications', function () {
            $user = auth()->user();
            $customer = \App\Models\Customer::where('user_id', $user->id)->first();
            $query = \App\Models\Booking::where('customer_id', optional($customer)->id);
            $openOrders = (clone $query)->whereIn('status', ['pending','scheduled','in_progress'])->count();
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
            }
            if (array_key_exists('email', $validated)) {
                $user->email = $validated['email'];
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

        return view('bookings', compact('bookings', 'cleaners'));
    })->name('bookings.index');

    // Assign petugas untuk booking
    Route::patch('/bookings/{booking}/assign', function (Request $request, \App\Models\Booking $booking) {
        $data = $request->validate([
            'cleaner_id' => ['nullable','exists:cleaners,id'],
        ]);
        $booking->cleaner_id = $data['cleaner_id'] ?? null;
        // Opsional: ketika assign, ubah status menjadi 'scheduled' jika masih pending
        if ($booking->status === 'pending' && ($booking->cleaner_id !== null)) {
            $booking->status = 'scheduled';
        }
        $booking->save();
        return back()->with('success', 'Petugas berhasil di-assign untuk booking #'.$booking->id);
    })->name('bookings.assign');

    // Ubah status booking
    Route::patch('/bookings/{booking}/status', function (Request $request, \App\Models\Booking $booking) {
        $data = $request->validate([
            'status' => ['required','in:pending,scheduled,in_progress,completed,cancelled'],
        ]);
        $booking->status = $data['status'];
        $booking->save();
        return back()->with('success', 'Status booking #'.$booking->id.' diperbarui.');
    })->name('bookings.status');

    // Manajemen layanan (admin/staff)
    Route::resource('services', \App\Http\Controllers\AdminServiceController::class)->names([
        'index' => 'services.index',
        'store' => 'services.store',
        'update' => 'services.update',
        'destroy' => 'services.destroy',
    ])->except(['create', 'show', 'edit']);

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

    Route::get('/schedule', function () {
        return view('schedule');
    })->name('schedule.index');

    Route::get('/payments', function () {
        return view('payments');
    })->name('payments.index');

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
        return view('settings');
    })->name('settings.index');

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
