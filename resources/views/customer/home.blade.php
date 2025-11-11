<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <title>Beranda Pelanggan</title>
    <!-- Font Awesome for service icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        :root {
            --bg: #f2f6ff;
            --primary: #4b88ff;
            --primary-dark: #3867d6;
            --text: #1f2d3d;
            --muted: #7b8ca6;
            --card: #ffffff;
            --shadow: 0 10px 20px rgba(0,0,0,0.08);
        }
        body { margin:0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; background: linear-gradient(180deg, #6aa4ff 0%, #9ec1ff 35%, var(--bg) 35%); }
        .app { max-width: 420px; margin: 0 auto; min-height: 100vh; display:flex; flex-direction:column; }
        .header { padding: 24px 18px 60px; color: #fff; }
        .greeting { font-weight: 600; font-size: 20px; margin-bottom: 10px; }
        .search { background: #fff; border-radius: 28px; padding: 10px 14px; box-shadow: var(--shadow); display:flex; align-items:center; }
        .search input { border: none; outline: none; width: 100%; font-size: 14px; }
        .content { flex:1; padding: 0 16px 84px; }
        .section-title { color: var(--text); font-weight: 600; margin: 18px 4px 8px; }
        .grid { display:grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .tile { display:block; text-decoration:none; background: var(--card); border-radius: 16px; padding: 14px 10px; text-align:center; box-shadow: var(--shadow); color: var(--muted); }
        .tile.active { outline: 3px solid rgba(75,136,255,0.25); color: var(--primary-dark); }
        .tile .icon { width: 42px; height: 42px; border-radius: 12px; margin: 0 auto 8px; display:flex; align-items:center; justify-content:center; background: #eef3ff; color: var(--primary-dark); font-size:20px; }
        .list { display:flex; flex-direction:column; gap: 12px; }
        .card { background: var(--card); border-radius: 16px; box-shadow: var(--shadow); padding: 14px; }
        .card .name { font-weight: 600; color: var(--text); }
        .card .meta { color: var(--muted); font-size: 13px; }
        .stars { color: #ffb703; letter-spacing: 1px; }
        .badge { display:inline-block; padding: 2px 8px; border-radius: 12px; font-size: 12px; background: #eaf3ff; color: var(--primary-dark); margin-left: 6px; }
        .footer { position: fixed; left: 0; right: 0; bottom: 0; background: #fff; box-shadow: 0 -8px 20px rgba(0,0,0,0.08); padding: 10px 0; }
        .footer .bar { max-width: 420px; margin: 0 auto; display:flex; justify-content: space-around; }
        .footer .item { text-decoration:none; color: var(--muted); text-align:center; font-size: 12px; }
        .footer .item .ico { display:flex; align-items:center; justify-content:center; width: 36px; height: 36px; border-radius: 12px; background:#eef3ff; margin: 0 auto 4px; color: var(--primary-dark); }
        .status { margin: -40px 16px 8px; background: var(--card); border-radius: 16px; padding: 12px; box-shadow: var(--shadow); }
        .status .title { color: var(--text); font-weight: 600; margin-bottom: 6px; }
        .status .line { display:flex; justify-content:space-between; font-size: 13px; color: var(--muted); margin: 4px 0; }
        .promo { display:flex; gap:10px; overflow-x:auto; padding-bottom:6px; }
        .promo .chip { flex:0 0 auto; background: var(--card); border-radius: 14px; padding: 10px 12px; box-shadow: var(--shadow); font-size: 13px; }
        .warning { margin: 10px 16px; color: #b45309; background: #fff7ed; border: 1px solid #fde68a; border-radius: 12px; padding: 10px; }
    </style>
</head>
<body>
<div class="app">
    <div class="header">
        <?php
            $userName = auth()->user()->name ?? 'Pelanggan';
            $firstName = explode(' ', $userName)[0];
            $hour = (int) now()->format('H');
            $greet = $hour < 12 ? 'Selamat pagi' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam');
        ?>
        <div class="greeting">{{ $greet }}, {{ $firstName }}</div>
        <div class="search">
            <span style="margin-right:8px; color:#9aa6c2">🔍</span>
            <input type="text" placeholder="Cari layanan, jadwal, promo" />
        </div>
    </div>

    @if (! $customer)
        <div class="warning">Profil pelanggan belum ditemukan. Silakan lengkapi data di menu Akun.</div>
    @endif

    <!-- Ringkas status -->
    <div class="status">
        <div class="title">Ringkasan</div>
        <div class="line"><span>Jadwal mendatang</span><span>{{ $upcomingBookings->count() }}</span></div>
        <div class="line"><span>Pesanan selesai</span><span>{{ $totalPastBookings }}</span></div>
        @if ($nextBooking)
            <div class="line"><span>Terdekat</span><span>{{ optional($nextBooking->scheduled_at)->format('d M Y H:i') }}</span></div>
        @endif
    </div>

    <div class="content">
        <div class="section-title">Layanan</div>
        <div class="grid">
            @php
                $defaultIcons = [
                    'General' => 'fa-broom',
                    'Karpet' => 'fa-rug',
                    'Sofa' => 'fa-couch',
                    'AC' => 'fa-fan',
                    'Dapur' => 'fa-utensils',
                    'Kamar Mandi' => 'fa-shower',
                    'Lantai' => 'fa-broom',
                ];
            @endphp
            @forelse($services as $i => $service)
                @php
                    $iconClass = $service->icon ?? ($defaultIcons[$service->category] ?? 'fa-broom');
                    $href = $service->slug ? route('customer.service.show', $service->slug) : '#';
                @endphp
                <a class="tile {{ $i === 2 ? 'active' : '' }}" href="{{ $href }}">
                    <div class="icon"><i class="fa {{ $iconClass }}"></i></div>
                    <div>{{ $service->name }}</div>
                </a>
            @empty
                <div class="tile" style="grid-column: span 3;">Belum ada layanan.</div>
            @endforelse
        </div>

        <div class="section-title" style="margin-top: 22px">Petugas Terbaik</div>
        <div class="list">
            @forelse($topCleaners as $c)
                <div class="card">
                    <div class="name">{{ $c->name }} <span class="badge">{{ number_format($c->avg_rating ?? 0, 1) }}</span></div>
                    <div class="meta">{{ $c->address ?? 'Lokasi tidak tersedia' }}</div>
                    <div class="stars">@for($s=1;$s<=5;$s++){{ $s <= round(($c->avg_rating ?? 0)) ? '★' : '☆' }}@endfor</div>
                </div>
            @empty
                <div class="card">Belum ada penilaian petugas.</div>
            @endforelse
        </div>

        <div class="section-title" style="margin-top: 22px">Promo Aktif</div>
        <div class="promo">
            @forelse($activePromotions as $promo)
                <div class="chip">
                    <strong>{{ $promo->title }}</strong>
                    <span class="badge">{{ $promo->code }}</span>
                </div>
            @empty
                <div class="chip">Tidak ada promo</div>
            @endforelse
        </div>
    </div>

    <!-- Bottom bar -->
    <div class="footer">
        <div class="bar">
            <a href="{{ route('customer.home') }}" class="item">
                <div class="ico">🏠</div>
                Beranda
            </a>
            <a href="{{ route('bookings.index') }}" class="item">
                <div class="ico">🗓️</div>
                Jadwal
            </a>
            <a href="{{ route('promotions.index') }}" class="item">
                <div class="ico">🎟️</div>
                Promo
            </a>
            <a href="#" class="item">
                <div class="ico">🛒</div>
                Pesanan
            </a>
            <a href="{{ route('profile.edit') }}" class="item">
                <div class="ico">👤</div>
                Akun
            </a>
        </div>
    </div>
</div>
</body>
</html>
