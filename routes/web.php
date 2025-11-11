<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerHomeController;
use App\Http\Controllers\CustomerRegistrationController;
use App\Http\Controllers\CustomerServiceController;

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
        Route::get('/services', [CustomerServiceController::class, 'index'])->name('customer.services.index');
        Route::get('/service/{slug}', [CustomerServiceController::class, 'show'])->name('customer.service.show');
        // Tambahkan route /customer lainnya di sini ke depannya
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Solusita: menu utama terproteksi
    Route::get('/bookings', function () {
        return view('bookings');
    })->name('bookings.index');

    // Manajemen layanan (admin/staff)
    Route::resource('services', \App\Http\Controllers\AdminServiceController::class)->names([
        'index' => 'services.index',
        'store' => 'services.store',
        'update' => 'services.update',
        'destroy' => 'services.destroy',
    ])->except(['create','show','edit']);

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
