<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <title>{{ $service->name }} - Layanan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        :root { --bg:#f6f8fc; --primary:#4b88ff; --text:#1f2d3d; --muted:#7b8ca6; --card:#fff; --shadow:0 10px 20px rgba(0,0,0,.08); }
        body { margin:0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial; background: var(--bg); }
        .app { max-width: 420px; margin: 0 auto; min-height: 100vh; }
        .header { background: linear-gradient(180deg, #6aa4ff 0%, #9ec1ff 100%); color:#fff; padding: 24px 16px; }
        .card { background: var(--card); border-radius: 16px; box-shadow: var(--shadow); padding: 16px; margin: -30px 16px 16px; }
        .title { font-size: 20px; font-weight: 700; color: var(--text); }
        .meta { color: var(--muted); font-size: 13px; }
        .price { color: var(--primary); font-weight: 700; margin-top: 8px; }
        .icon { width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; background:#eef3ff; color:#2a57c4; font-size:22px; }
        .actions { display:flex; gap:10px; margin-top:12px; }
        .btn { flex:1; display:block; text-align:center; padding:12px 14px; border-radius:12px; text-decoration:none; }
        .btn-primary { background: var(--primary); color:#fff; }
        .btn-secondary { background: #eef3ff; color:#2a57c4; }
    </style>
    </head>
<body>
    <div class="app">
        <div class="header">
            <div style="display:flex; align-items:center; gap:10px;">
                <div class="icon"><i class="fa {{ $service->icon ?? 'fa-broom' }}"></i></div>
                <div>
                    <div class="title">{{ $service->name }}</div>
                    <div class="meta">Durasi: {{ $service->duration_minutes }} menit · Kategori: {{ $service->category ?? 'Umum' }}</div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="meta">Deskripsi</div>
            <div style="margin-top:6px;">{{ $service->description ?? 'Belum ada deskripsi.' }}</div>
            <div class="price">Rp {{ number_format($service->base_price, 0, ',', '.') }}</div>
            <div class="actions">
                <a href="#" class="btn btn-primary">Pesan Layanan</a>
                <a href="{{ route('customer.home') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</body>
</html>

