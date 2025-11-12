<nav class="bar" aria-label="Navigasi bawah">
    <a href="{{ route('customer.home') }}" class="item {{ \Illuminate\Support\Facades\Route::is('customer.home') ? 'active' : '' }}">
        <div class="ico"><i class="fa-solid fa-house"></i></div>
        Beranda
    </a>
    <a href="{{ route('customer.schedule') }}" class="item {{ \Illuminate\Support\Facades\Route::is('customer.schedule') ? 'active' : '' }}">
        <div class="ico"><i class="fa-regular fa-clock"></i></div>
        Jadwal
    </a>
    <a href="#" class="item">
        <div class="ico"><i class="fa-regular fa-bell"></i></div>
        Notif
    </a>
    <a href="{{ route('customer.profile') }}" class="item {{ \Illuminate\Support\Facades\Route::is('customer.profile') ? 'active' : '' }}">
        <div class="ico"><i class="fa-regular fa-user"></i></div>
        Akun
    </a>
</nav>
