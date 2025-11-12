<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <title>Beranda Pelanggan</title>
    <!-- Font Awesome for service icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    @include('customer.partials.base-css')
    <style>
        :root {
            --bg: #f2f6ff;
            --primary: #4b88ff;
            --primary-dark: #3867d6;
            --text: #1f2d3d;
            --muted: #7b8ca6;
            --card: #ffffff;
            --shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            background: var(--bg);
        }

        .app {
            max-width: 420px;
            margin: 0 auto;
            /* Gunakan unit viewport dinamis agar tidak muncul scroll mikro di mobile */
            min-height: 100vh; /* fallback */
            min-height: 100dvh; /* modern browsers */
            display: flex;
            flex-direction: column;
            position: relative; /* untuk menempatkan bg-extend di belakang konten */
        }

        /* Samakan bentuk header dengan halaman profil (kecuali warna) */
        .header {
            position: relative;
            color: #fff;
            background: transparent; /* hapus gradien pertama, gunakan bg-extend saja */
            padding: 32px 18px 48px; /* tetap sama untuk tata letak */
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            box-shadow: none; /* bayangan cukup dari .bg-extend */
        }
        /* Perpanjang gradien di belakang konten hingga tepat di atas label Layanan */
        .bg-extend {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 220px; /* disesuaikan via JS agar berhenti tepat di atas label Layanan */
            background: linear-gradient(135deg, #6aa4ff 0%, #9ec1ff 100%);
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            box-shadow: 0 12px 24px rgba(75, 136, 255, 0.25);
            z-index: -1; /* selalu di belakang */
        }

        .greeting {
            font-weight: 600;
            font-size: 20px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .notif-bell {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
            color: #fff;
        }
        .notif-dot {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ff3b30; /* iOS style red */
            display: none;
            box-shadow: 0 0 0 2px rgba(255,255,255,0.6);
        }

        .search {
            background: #fff;
            border-radius: 28px;
            padding: 10px 14px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
        }

        .search input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 14px;
        }

        .content {
            flex: 1;
            /* tidak melayang lagi: kurangi padding bawah */
            padding: 0 16px 24px;
        }

        .section-title {
            color: var(--text);
            font-weight: 600;
            margin: 18px 4px 8px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .tile {
            display: block;
            text-decoration: none;
            background: var(--card);
            border-radius: 16px;
            padding: 14px 10px;
            text-align: center;
            box-shadow: var(--shadow);
            color: var(--muted);
        }

        .tile.active {
            outline: 3px solid rgba(75, 136, 255, 0.25);
            color: var(--primary-dark);
        }

        .tile .icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            margin: 0 auto 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eef3ff;
            color: var(--primary-dark);
            font-size: 20px;
        }

        .list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .card {
            background: var(--card);
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 14px;
        }

        .card .name {
            font-weight: 600;
            color: var(--text);
        }

        .card .meta {
            color: var(--muted);
            font-size: 13px;
        }

        .stars {
            color: #ffb703;
            letter-spacing: 1px;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            background: #eaf3ff;
            color: var(--primary-dark);
            margin-left: 6px;
        }

        /* Bottom Navigation: fixed agar tidak bergerak saat scroll */
        .footer { position: fixed; bottom: 0; left: 0; right: 0; }

        .footer .bar {
            max-width: 420px;
            margin: 0 auto 8px;
            display: flex;
            justify-content: space-between;
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.10);
            padding: 6px 6px;
            /* perkecil kiri-kanan */
            pointer-events: auto;
        }

        .footer .item {
            text-decoration: none;
            color: var(--muted);
            text-align: center;
            font-size: 11px;
            flex: 1;
            /* distribusi item fleksibel */
            position: relative;
            padding: 4px 0;
            border-radius: 14px;
        }

        .footer .item .ico {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            margin: 0 auto 2px;
            color: var(--primary-dark);
            font-size: 16px;
        }

        .footer .item.active {
            color: var(--text);
            font-weight: 600;
        }

        /* garis aktif lebih tipis di bawah ikon */
        .footer .item.active::after {
            content: '';
            display: block;
            width: 18px;
            height: 1.5px;
            border-radius: 2px;
            background: var(--primary);
            margin: 2px auto 0;
        }


        .status {
            margin: 10px 16px 8px; /* pastikan berada di bawah header (tidak tertutup gradien) */
            background: var(--card);
            border-radius: 16px;
            padding: 12px;
            box-shadow: var(--shadow);
        }

        .status .title {
            color: var(--text);
            font-weight: 600;
            margin-bottom: 6px;
        }

        .status .line {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: var(--muted);
            margin: 4px 0;
        }

        /* Promo ditampilkan vertikal (ke bawah) */
        .promo {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding-bottom: 6px;
            overflow: hidden;
        }

        .promo .chip {
            background: var(--card);
            border-radius: 14px;
            padding: 10px 12px;
            box-shadow: var(--shadow);
            font-size: 13px;
        }

        .warning {
            margin: 10px 16px;
            color: #b45309;
            background: #fff7ed;
            border: 1px solid #fde68a;
            border-radius: 12px;
            padding: 10px;
        }

        .view-all {
            display: block;
            text-align: center;
            margin: 12px 4px;
            background: var(--primary);
            color: #fff;
            border-radius: 12px;
            padding: 10px;
            text-decoration: none;
            box-shadow: var(--shadow);
        }

        /* Help label (tautan ringan) */
        .help-row {
            text-align: right;
            margin: 6px 16px 0;
        }
        .help-link {
            color: #d32f2f; /* merah */
            text-decoration: underline;
            font-size: 13px;
            font-weight: 600;
        }
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
            <div class="greeting">
                <span>{{ $greet }}, {{ $firstName }}</span>
                <span id="notifBell" class="notif-bell" aria-label="Notifikasi">
                    <i class="fa-regular fa-bell"></i>
                </span>
            </div>
            <div class="search">
                <span style="margin-right:8px; color:#9aa6c2">🔍</span>
                <input type="text" placeholder="Cari layanan, jadwal, promo" />
            </div>
        </div>
        <!-- Wave dihapus agar bentuk mengikuti header profil yang ber-radius bawah -->
        <div class="bg-extend" aria-hidden="true"></div>

        @if (!$customer)
            <div class="warning">Profil pelanggan belum ditemukan. Silakan lengkapi data di menu Akun.</div>
        @endif

        <!-- Ringkas status -->
        <div class="status">
            <div class="title">Ringkasan</div>
            <div class="line"><span>Order</span><span>{{ $openOrders }}</span></div>
            @if(($completedOrders ?? 0) > 0)
            <div class="line"><span>Pesanan Selesai</span><span>{{ $completedOrders }}</span></div>
            @endif
            @if ($nextBooking)
                <div class="line">
                    <span>Terdekat</span><span>{{ optional($nextBooking->scheduled_at)->format('d M Y H:i') }}</span>
                </div>
            @endif
        </div>
        <div class="help-row">
            <a class="help-link" href="{{ route('customer.help.create') }}">Butuh bantuan?</a>
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
            <a class="view-all" href="{{ route('customer.services.index') }}">Lihat Semua Layanan</a>

            <div class="section-title" style="margin-top: 22px">Petugas Terbaik</div>
            <div class="list">
                @forelse($topCleaners as $c)
                    <div class="card">
                        <div class="name">{{ $c->name }} <span
                                class="badge">{{ number_format($c->avg_rating ?? 0, 1) }}</span></div>
                        <div class="meta">{{ $c->address ?? 'Lokasi tidak tersedia' }}</div>
                        <div class="stars">
                            @for ($s = 1; $s <= 5; $s++)
                                {{ $s <= round($c->avg_rating ?? 0) ? '★' : '☆' }}
                            @endfor
                        </div>
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

        <!-- Bottom bar (versi referensi) -->
        @php
            $current = \Illuminate\Support\Facades\Route::currentRouteName();
        @endphp
        <div class="footer">
            @include('customer.partials.bottom-nav')
        </div>
    </div>
</body>

@include('customer.partials.base-js')
</html>
