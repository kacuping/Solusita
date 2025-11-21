<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <title>Konfirmasi DP - Pesanan #{{ $booking->id }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        :root { --bg:#f2f6ff; --text:#1f2d3d; --muted:#7b8ca6; --card:#fff; --shadow:0 10px 20px rgba(0,0,0,.08); --primary:#4b88ff; }
        body { margin:0; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial; background:var(--bg); color:var(--text); }
        .app { max-width:960px; margin:0 auto; min-height:100vh; min-height:100dvh; padding:16px; }
        .card { background:var(--card); border-radius:16px; box-shadow:var(--shadow); padding:16px; }
        .title { font-weight:700; font-size:16px; margin-bottom:8px; }
        .line { display:flex; justify-content:space-between; font-size:14px; margin-bottom:6px; }
        .info { font-size:13px; color:var(--muted); }
        .btn { display:block; text-align:center; text-decoration:none; padding:10px 12px; border-radius:12px; font-weight:600; }
        .btn-primary { background:var(--primary); color:#fff; }
        .btn-secondary { background:#eef3ff; color:#2a57c4; }
        .input { width:100%; padding:10px 12px; border:1px solid #e6edff; border-radius:12px; font-size:14px; }
        .label { font-weight:600; font-size:13px; margin-bottom:6px; display:block; }
        .group { margin-bottom:12px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; }
        .footer .bar { max-width: 960px; margin: 0 auto 8px; display:flex; justify-content: space-between; background:#fff; border-radius:18px; box-shadow: 0 10px 20px rgba(0,0,0,.10); padding:6px 6px; }
        @media (min-width: 768px) { .footer .bar { padding: 8px 10px; } }
        .footer .item { text-decoration:none; color: var(--muted); text-align:center; font-size:11px; flex:1; position:relative; padding:4px 0; border-radius:14px; }
        .footer .item .ico { display:flex; align-items:center; justify-content:center; width:28px; height:28px; margin:0 auto 2px; color:#2a57c4; font-size:16px; }
        .footer .item.active { color: var(--text); font-weight:600; }
        .footer .item.active::after { content:''; display:block; width:18px; height:1.5px; border-radius:2px; background: var(--primary); margin:2px auto 0; }
    </style>
    @include('customer.partials.base-css')
    @include('customer.partials.base-js')
</head>
<body>
    <div class="app">
        <div class="card">
            <div class="title">Konfirmasi Pembayaran DP</div>
            <div class="info" style="margin-bottom:8px;">Silakan melakukan pembayaran DP sebesar Rp {{ number_format($dpAmount ?? 50000, 0, ',', '.') }}. Upload bukti bayar (opsional) agar admin mudah memverifikasi.</div>

            @if (!empty($dpOption))
                <div class="group">
                    <div class="label">Nomor Rekening</div>
                    <div class="line"><span>Bank</span><span>{{ $dpOption['bank_name'] ?? '-' }}</span></div>
                    <div class="line"><span>No. Rekening</span><span>{{ $dpOption['bank_account_number'] ?? '-' }}</span></div>
                    <div class="line"><span>Atas Nama</span><span>{{ $dpOption['bank_account_name'] ?? '-' }}</span></div>
                </div>
            @endif

            <form method="POST" action="{{ route('customer.dp.upload', $booking) }}" enctype="multipart/form-data">
                @csrf
                <div class="group">
                    <label class="label">Upload Bukti Bayar (opsional)</label>
                    <input class="input" type="file" name="dp_proof" accept="image/*">
                </div>
                <div style="display:flex; gap:8px;">
                    <a href="{{ route('customer.schedule') }}" class="btn btn-secondary" style="flex:1;">Nanti</a>
                    <button type="submit" class="btn btn-primary" style="flex:1;">Kirim Bukti</button>
                </div>
            </form>
        </div>

        <div class="footer">
            @include('customer.partials.bottom-nav')
        </div>
    </div>
</body>
</html>
