<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <title>{{ $service->name }} - Layanan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    @include('customer.partials.base-css')
    <style>
        :root { --bg:#f6f8fc; --primary:#4b88ff; --text:#1f2d3d; --muted:#7b8ca6; --card:#fff; --shadow:0 10px 20px rgba(0,0,0,.08); }
        body { margin:0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial; background: var(--bg); }
        .app { max-width: 960px; margin: 0 auto; min-height: 100vh; min-height: 100dvh; }
        .header { background: linear-gradient(180deg, #6aa4ff 0%, #9ec1ff 100%); color:#fff; padding: 28px 16px 40px; }
        .card { background: var(--card); border-radius: 16px; box-shadow: var(--shadow); padding: 18px; margin: -20px 16px 18px; }
        .title { font-size: 20px; font-weight: 700; color: var(--text); }
        .meta { color: var(--muted); font-size: 13px; line-height: 1.4; }
        .price { color: var(--primary); font-weight: 700; margin-top: 10px; }
        .icon { width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; background:#eef3ff; color:#2a57c4; font-size:22px; }
        .actions { display:flex; gap:10px; margin-top:14px; }
        .btn { flex:1; display:block; text-align:center; padding:12px 14px; border-radius:12px; text-decoration:none; }
        .btn-primary { background: var(--primary); color:#fff; }
        .btn-secondary { background: #eef3ff; color:#2a57c4; }
        /* Bottom Navigation */
        .footer { position: fixed; bottom: 0; left: 0; right: 0; }
        .footer .bar { max-width: 960px; margin: 0 auto 8px; display:flex; justify-content: space-between; background:#fff; border-radius:18px; box-shadow: 0 10px 20px rgba(0,0,0,.10); padding:6px 6px; }
        @media (min-width: 768px) { .footer .bar { padding: 8px 10px; } }
        .footer .item { text-decoration:none; color: var(--muted); text-align:center; font-size:11px; flex:1; position:relative; padding:4px 0; border-radius:14px; }
        .footer .item .ico { display:flex; align-items:center; justify-content:center; width:28px; height:28px; margin:0 auto 2px; color:#2a57c4; font-size:16px; }
        .footer .item.active { color: var(--text); font-weight:600; }
        .footer .item.active::after { content:''; display:block; width:18px; height:1.5px; border-radius:2px; background: var(--primary); margin:2px auto 0; }
    </style>
    </head>
<body>
    <div class="app">
        <div class="header">
            <div style="display:flex; align-items:center; gap:10px;">
                <div class="icon"><i class="fa {{ $iconClass }}"></i></div>
                <div>
                    <div class="title">{{ $service->name }}</div>
                    @php($isDuration = strtolower(trim((string)($service->unit_type ?? 'Durasi'))) === 'durasi')
                    <div class="meta">
                        @if($isDuration && (int)($service->duration_minutes ?? 0) > 0)
                            Durasi: {{ (int)$service->duration_minutes }} menit
                        @else
                            Satuan/QTY: {{ $service->unit_type ?? 'Satuan' }}
                        @endif
                        · Kategori: {{ $service->category ?? 'Umum' }}
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="meta" style="margin-bottom:6px;">Deskripsi</div>
            <div class="meta" style="color: var(--text);">{{ $service->description ?? 'Belum ada deskripsi.' }}</div>
            <div class="price">Rp {{ number_format($service->base_price, 0, ',', '.') }}</div>
            <div class="actions">
                <a href="{{ route('customer.order.create', $service->slug) }}" class="btn btn-primary">Pesan Layanan</a>
                <a href="{{ route('customer.home') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
        <div class="footer">
            @include('customer.partials.bottom-nav')
        </div>
    </div>
</body>
@include('customer.partials.base-js')
</html>
