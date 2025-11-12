<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <title>Jadwal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        :root { --bg:#f2f6ff; --primary:#4b88ff; --text:#1f2d3d; --muted:#7b8ca6; --card:#fff; --shadow:0 10px 20px rgba(0,0,0,.08); }
        body { margin:0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial; background: var(--bg); }
        .app { max-width: 420px; margin: 0 auto; min-height: 100vh; padding-bottom:64px; }
        .header { background: linear-gradient(180deg, #6aa4ff 0%, #9ec1ff 100%); color:#fff; padding: 24px 16px; }
        .title { font-weight:700; font-size:20px; }
        .content { padding: 16px; }
        .card { background: var(--card); border-radius: 16px; box-shadow: var(--shadow); padding: 14px; }
        .empty { color: var(--muted); text-align:center; }
        /* Bottom Navigation */
        .footer { position: fixed; bottom: 0; left: 0; right: 0; }
        .footer .bar { max-width: 420px; margin: 0 auto 8px; display:flex; justify-content: space-between; background:#fff; border-radius:18px; box-shadow: 0 10px 20px rgba(0,0,0,.10); padding:6px 6px; }
        .footer .item { text-decoration:none; color: var(--muted); text-align:center; font-size:11px; flex:1; position:relative; padding:4px 0; border-radius:14px; }
        .footer .item .ico { display:flex; align-items:center; justify-content:center; width:28px; height:28px; margin:0 auto 2px; color:#2a57c4; font-size:16px; }
        .footer .item.active { color: var(--text); font-weight:600; }
        .footer .item.active::after { content:''; display:block; width:18px; height:1.5px; border-radius:2px; background: var(--primary); margin:2px auto 0; }
    </style>
</head>
<body>
    <div class="app">
        <div class="header">
            <div class="title">Jadwal</div>
        </div>
        <div class="content">
            @if (session('status'))
                <div style="margin-bottom:10px; background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; border-radius:12px; padding:10px;">
                    {{ session('status') }}
                </div>
            @endif
            @if(isset($bookings) && $bookings->count())
                <div class="card" style="padding:0;">
                    @foreach($bookings as $b)
                        <div style="padding:12px; border-bottom:1px solid #eef2ff; display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <div style="font-weight:600; color:#1f2d3d;">{{ optional($b->service)->name ?? 'Layanan' }}</div>
                                <div style="font-size:13px; color:#7b8ca6;">{{ optional($b->scheduled_at)->format('d M Y H:i') }}</div>
                                <div style="font-size:12px; color:#7b8ca6;">{{ \Illuminate\Support\Str::limit($b->address, 40) }}</div>
                            </div>
                            <span style="font-size:12px; padding:4px 8px; border-radius:10px; background:#eef3ff; color:#2a57c4;">{{ $b->status }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card">
                    <div class="empty">Belum ada jadwal. Silakan melakukan pemesanan layanan.</div>
                </div>
            @endif
        </div>
        <div class="footer">
            @include('customer.partials.bottom-nav')
        </div>
    </div>
</body>
</html>
