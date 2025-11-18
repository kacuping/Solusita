<nav class="bar" aria-label="Navigasi bawah">
    <a href="{{ route('customer.home') }}" class="item {{ \Illuminate\Support\Facades\Route::is('customer.home') ? 'active' : '' }}">
        <div class="ico"><i class="fa-solid fa-house"></i></div>
        Beranda
    </a>
    <a href="{{ route('customer.schedule') }}" class="item {{ \Illuminate\Support\Facades\Route::is('customer.schedule') ? 'active' : '' }}">
        <div class="ico"><i class="fa-regular fa-clock"></i></div>
        Order
    </a>
    <a href="{{ route('customer.payments.index') }}" class="item {{ (\Illuminate\Support\Facades\Route::is('customer.payments.index') || \Illuminate\Support\Facades\Route::is('customer.payment.*') || \Illuminate\Support\Facades\Route::is('customer.dp.*')) ? 'active' : '' }}">
        <div class="ico"><i class="fa-solid fa-credit-card"></i></div>
        Pembayaran
    </a>
    <a href="{{ route('customer.profile') }}" class="item {{ \Illuminate\Support\Facades\Route::is('customer.profile') ? 'active' : '' }}">
        <div class="ico"><i class="fa-regular fa-user"></i></div>
        Akun
    </a>
</nav>
