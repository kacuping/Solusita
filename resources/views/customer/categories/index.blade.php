<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <title>Semua Kategori</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    @include('customer.partials.base-css')
    <style>
        :root { --bg:#f2f6ff; --primary:#4b88ff; --text:#1f2d3d; --muted:#7b8ca6; --card:#fff; --shadow:0 10px 20px rgba(0,0,0,.08); }
        body { margin:0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial; background: var(--bg); }
        .app { max-width:420px; margin:0 auto; min-height:100vh; min-height: 100dvh; }
        .header { background: linear-gradient(180deg, #6aa4ff 0%, #9ec1ff 100%); color:#fff; padding:20px 16px; }
        .title { font-weight:700; font-size:20px; }
        .content { padding: 16px; }
        .grid { display:grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .tile { display:block; text-decoration:none; background: var(--card); border-radius: 16px; padding: 14px 10px; text-align:center; box-shadow: var(--shadow); color: var(--muted); }
        .tile .icon { width: 42px; height: 42px; border-radius: 12px; margin: 0 auto 8px; display:flex; align-items:center; justify-content:center; background: #eef3ff; color: #2a57c4; font-size:20px; }
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
            <div class="title">Semua Kategori</div>
        </div>
        <div class="content">
            <div class="grid">
                @php
                    $defaultIcons = [
                        'General' => 'fa-broom',
                        'Karpet' => 'fa-brush',
                        'Sofa' => 'fa-couch',
                        'AC' => 'fa-wind',
                        'Dapur' => 'fa-utensils',
                        'Kamar Mandi' => 'fa-shower',
                        'Lantai' => 'fa-broom',
                    ];
                @endphp
                @forelse($categories as $cat)
                    @php
                        $iconClass = $cat->icon ?? ($defaultIcons[$cat->name] ?? 'fa-broom');
                        $href = route('customer.services.index', ['category' => $cat->name]);
                    @endphp
                    <a class="tile" href="{{ $href }}">
                        <div class="icon"><i class="fa {{ $iconClass }}"></i></div>
                        <div>{{ $cat->name }}</div>
                    </a>
                @empty
                    <div class="tile" style="grid-column: span 3;">Belum ada kategori.</div>
                @endforelse
            </div>
            <a href="{{ route('customer.home') }}" class="tile" style="grid-column: span 3; margin-top:12px; background:#eef3ff; color:#2a57c4;">Kembali ke Beranda</a>
        </div>
        <div class="footer">
            @include('customer.partials.bottom-nav')
        </div>
    </div>
</body>
@include('customer.partials.base-js')
</html>
