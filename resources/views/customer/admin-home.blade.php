<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home - Customer Portal</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --bg: #0f172a;
            --card: #111827;
            --muted: #94a3b8;
            --accent: #38bdf8;
            --accent2: #22d3ee;
            --white: #e5e7eb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu;
            background: radial-gradient(1200px 600px at 10% -10%, #0ea5e9, transparent 60%), radial-gradient(800px 500px at 100% 0%, #22d3ee, transparent 60%), var(--bg);
            color: var(--white);
        }

        .wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 32px 20px;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 24px;
            align-items: center;
        }

        .title {
            font-size: 40px;
            line-height: 1.1;
            margin: 0 0 8px;
        }

        .desc {
            color: var(--muted);
            font-size: 16px;
        }

        .cta {
            margin-top: 18px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: none;
            padding: 12px 18px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: #0c111b;
        }

        .btn-secondary {
            background: #1f2937;
            color: var(--white);
        }

        .cards {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .card {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0.02));
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 14px;
            padding: 18px;
            backdrop-filter: blur(6px);
        }

        .card h4 {
            margin: 0 0 8px;
            font-size: 16px;
        }

        .metric {
            font-size: 28px;
            font-weight: 700;
        }

        .muted {
            color: var(--muted);
            font-size: 13px;
        }

        .glass {
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(17, 24, 39, 0.6);
            backdrop-filter: blur(6px);
            border-radius: 16px;
        }

        .panel {
            margin-top: 28px;
            padding: 18px;
        }

        .panel h3 {
            margin: 0 0 10px;
            font-size: 18px;
        }

        .list {
            display: grid;
            gap: 10px;
        }

        .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            border-radius: 10px;
            background: #0b1220;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .row .left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .badge {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 999px;
            background: #0ea5e9;
            color: #001019;
            font-weight: 700;
        }

        .avatar {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background: linear-gradient(135deg, #0ea5e9, #22d3ee);
            display: grid;
            place-items: center;
            color: #001019;
            font-weight: 800;
        }

        @media (max-width:900px) {
            .hero {
                grid-template-columns: 1fr;
            }

            .cards {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width:600px) {
            .cards {
                grid-template-columns: 1fr;
            }

            .title {
                font-size: 32px;
            }
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="hero">
            <div>
                <div class="badge"><i class="fa-solid fa-shield-halved"></i>&nbsp; Admin Portal</div>
                <h1 class="title">Selamat datang, Administrator</h1>
                <p class="desc">Anda login melalui halaman pelanggan. Ini adalah beranda khusus administrator dengan
                    tampilan ringkas dan fokus pada operasional harian.</p>
                <div class="cta">
                    <a class="btn btn-primary" href="{{ route('dashboard') }}"><i class="fa-solid fa-gauge-high"></i>
                        Buka Dashboard Lengkap</a>
                    <a class="btn btn-secondary" href="{{ route('bookings.index') }}"><i
                            class="fa-solid fa-clipboard-list"></i> Kelola Pesanan</a>
                    <a class="btn btn-secondary" href="{{ route('services.index') }}"><i class="fa-solid fa-broom"></i>
                        Kelola Layanan</a>
                </div>
            </div>
            <div class="glass" style="padding:14px;">
                <div class="row">
                    <div class="left">
                        <div class="avatar">A</div>
                        <div>
                            <div style="font-weight:700;">Administrator</div>
                            <div class="muted">Solusita</div>
                        </div>
                    </div>
                    <div class="muted">Login: {{ now()->format('d M Y H:i') }}</div>
                </div>
                <div class="panel">
                    <h3>Akses Cepat</h3>
                    <div class="list">
                        <div class="row">
                            <div class="left"><i class="fa-solid fa-users"></i>
                                <div>Manajemen User & Role</div>
                            </div><a class="btn btn-secondary" href="{{ route('users.index') }}">Buka</a>
                        </div>
                        <div class="row">
                            <div class="left"><i class="fa-solid fa-user-check"></i>
                                <div>Petugas</div>
                            </div><a class="btn btn-secondary" href="{{ route('cleaners.index') }}">Buka</a>
                        </div>
                        <div class="row">
                            <div class="left"><i class="fa-solid fa-credit-card"></i>
                                <div>Pembayaran</div>
                            </div><a class="btn btn-secondary" href="{{ route('payments.index') }}">Buka</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <h4><i class="fa-regular fa-calendar-check"></i> Pesanan Hari Ini</h4>
                <div class="metric">{{ \App\Models\Booking::whereDate('scheduled_at', now()->toDateString())->count() }}
                </div>
                <div class="muted">Jadwal kerja pada tanggal {{ now()->format('d M Y') }}</div>
            </div>
            <div class="card">
                <h4><i class="fa-solid fa-sack-dollar"></i> Total Terbayar</h4>
                <div class="metric">Rp
                    {{ number_format((float) \App\Models\Booking::where('payment_status', 'paid')->sum('total_amount'), 0, ',', '.') }}
                </div>
                <div class="muted">Akumulasi pembayaran selesai</div>
            </div>
            <div class="card">
                <h4><i class="fa-regular fa-clock"></i> Pending</h4>
                <div class="metric">{{ \App\Models\Booking::where('status', 'pending')->count() }}</div>
                <div class="muted">Menunggu penjadwalan atau konfirmasi</div>
            </div>
        </div>
    </div>
</body>

</html>
<script>
    (function() {
        var lastAnnounced = 0;

        function showBanner(msg) {
            try {
                var wrap = document.querySelector('.wrap');
                var b = document.createElement('div');
                b.textContent = msg || 'Ada order baru';
                b.style.position = 'fixed';
                b.style.right = '16px';
                b.style.bottom = '16px';
                b.style.background = 'linear-gradient(135deg, #0ea5e9, #22d3ee)';
                b.style.color = '#001019';
                b.style.padding = '10px 14px';
                b.style.borderRadius = '10px';
                b.style.boxShadow = '0 8px 24px rgba(0,0,0,0.35)';
                b.style.fontWeight = '700';
                b.style.zIndex = '9999';
                document.body.appendChild(b);
                setTimeout(function() {
                    try {
                        document.body.removeChild(b);
                    } catch (e) {}
                }, 4500);
            } catch (e) {}
        }
        async function notify(msg) {
            try {
                var reg = await navigator.serviceWorker.getRegistration('/customer/');
                if (reg) {
                    reg.showNotification('Order Baru', {
                        body: msg || 'Ada order baru',
                        icon: '/icons/solusita_notif.png',
                        data: {
                            url: '/bookings'
                        }
                    });
                } else if (Notification && Notification.permission === 'granted') {
                    new Notification('Order Baru', {
                        body: msg || 'Ada order baru',
                        icon: '/icons/solusita_notif.png'
                    });
                } else {
                    showBanner(msg);
                }
            } catch (e) {
                showBanner(msg);
            }
        }
        async function poll() {
            try {
                var res = await fetch('{{ route('admin.notifications') }}', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) return;
                var data = await res.json();
                var t = Date.parse(data.last_order_at || 0);
                var now = Date.now();
                var recent = (now - t) < (2 * 60 * 1000);
                if (recent && t && t !== lastAnnounced) {
                    lastAnnounced = t;
                    notify('Ada order dari pelanggan. Buka Pesanan untuk detail.');
                }
            } catch (e) {}
        }
        window.addEventListener('DOMContentLoaded', function() {
            try {
                if (Notification && Notification.permission === 'default') Notification.requestPermission();
            } catch (e) {}
            poll();
            setInterval(poll, 10000);
        });
    })();
</script>
