<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <title>Pembayaran - Pesanan #{{ $booking->id }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        :root { --bg:#f2f6ff; --text:#1f2d3d; --muted:#7b8ca6; --card:#fff; --shadow:0 10px 20px rgba(0,0,0,.08); --primary:#4b88ff; }
        body { margin:0; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial; background:var(--bg); color:var(--text); }
        .app { max-width:420px; margin:0 auto; min-height:100vh; min-height:100dvh; padding:16px; }
        .receipt { background:var(--card); border-radius:16px; box-shadow:var(--shadow); padding:16px; }
        .title { font-weight:700; font-size:16px; margin-bottom:8px; }
        .line { display:flex; justify-content:space-between; font-size:14px; margin-bottom:6px; }
        .divider { height:1px; background:#e6edff; margin:10px 0; }
        .status { background:#eef3ff; color:#2a57c4; padding:8px 10px; border-radius:12px; font-size:13px; margin-bottom:10px; }
        .qr { display:flex; align-items:center; justify-content:center; margin:10px 0; }
        .qr img { max-width:80%; border-radius:12px; box-shadow:var(--shadow); }
        .btns { display:flex; gap:8px; margin-top:12px; }
        .btn { flex:1; display:block; text-align:center; text-decoration:none; padding:10px 12px; border-radius:12px; font-weight:600; }
        .btn-primary { background:#4b88ff; color:#fff; }
        .btn-secondary { background:#e6edff; color:#2a57c4; }
        .muted { color:var(--muted); font-size:12px; }
    </style>
    @include('customer.partials.base-css')
    @include('customer.partials.base-js')
</head>
<body>
    <div class="app">
        <div class="receipt">
            <div class="title">Struk Pesanan #{{ $booking->id }}</div>
            @if(!empty($orderNo))
                <div class="line"><span>No. Order</span><span>{{ $orderNo }}</span></div>
            @endif
            <div class="line"><span>Layanan</span><span>{{ $service->name ?? 'Layanan' }}</span></div>
            <div class="line"><span>Jadwal</span><span>{{ optional($booking->scheduled_at)->format('d M Y H:i') }}</span></div>
            @php($isDuration = strtolower(trim((string)($service->unit_type ?? 'Durasi'))) === 'durasi')
            @if($isDuration)
                <div class="line"><span>Durasi</span><span>{{ (int)($booking->duration_minutes ?? 0) }} menit</span></div>
            @else
                @php($n = (string)($booking->notes ?? ''))
                @if($n !== '' && preg_match('/Ukuran:\s*Panjang\s*([0-9.]+)m,\s*Lebar\s*([0-9.]+)m/i', $n, $mm))
                    <div class="line"><span>Ukuran</span><span>{{ $mm[1] }}m x {{ $mm[2] }}m</span></div>
                @elseif($n !== '' && preg_match('/Qty:\s*(\d+)/i', $n, $qm))
                    <div class="line"><span>Qty</span><span>{{ $qm[1] }} · Satuan: {{ $service->unit_type ?? 'Satuan' }}</span></div>
                @endif
            @endif
            <div class="divider"></div>
            <div class="line" style="font-weight:700"><span>Total</span><span>Rp {{ number_format((float)($booking->total_amount ?? 0), 0, ',', '.') }}</span></div>
            <div class="divider"></div>

            @php($m = strtolower((string)($method ?? '')))
            @if($m === 'cash')
                <div class="status">Order diproses. Silakan siapkan pembayaran tunai saat petugas datang.</div>
                <div class="muted">Metode: Tunai (Cash)</div>
                <form method="POST" action="{{ route('customer.payment.cancel', ['booking' => $booking->id]) }}" class="btns">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Kembali ke Beranda</button>
                </form>
            @elseif(str_starts_with($m, 'option_') && ($paymentOption ?? null))
                @if(($paymentOption['type'] ?? '') === 'qris')
                    <div class="status">Detail Transaksi QRIS</div>
                    <div class="line"><span>Metode</span><span>{{ $paymentOption['label'] ?? 'QRIS' }}</span></div>
                    <div class="qr">
                        @if(!empty($paymentOption['qris_image_path']))
                            <img src="/{{ $paymentOption['qris_image_path'] }}" alt="QRIS">
                        @else
                            <span class="muted">QR IS belum tersedia</span>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('customer.payment.confirm', ['booking' => $booking->id]) }}" class="btns">
                        @csrf
                        <button type="submit" class="btn btn-primary">Order</button>
                    </form>
                    <form method="POST" action="{{ route('customer.payment.cancel', ['booking' => $booking->id]) }}" class="btns">
                        @csrf
                        <button type="submit" class="btn btn-secondary">Kembali ke Beranda</button>
                    </form>
                @elseif(($paymentOption['type'] ?? '') === 'transfer')
                    <div class="status">Detail Transaksi Transfer</div>
                    <div class="line"><span>Bank</span><span>{{ $paymentOption['bank_name'] ?? '-' }}</span></div>
                    <div class="line"><span>Nama Rekening</span><span>{{ $paymentOption['bank_account_name'] ?? '-' }}</span></div>
                    <div class="line"><span>No. Rekening</span><span>{{ $paymentOption['bank_account_number'] ?? '-' }}</span></div>
                    <form method="POST" action="{{ route('customer.payment.confirm', ['booking' => $booking->id]) }}" class="btns">
                        @csrf
                        <button type="submit" class="btn btn-primary">Order</button>
                    </form>
                    <form method="POST" action="{{ route('customer.payment.cancel', ['booking' => $booking->id]) }}" class="btns">
                        @csrf
                        <button type="submit" class="btn btn-secondary">Kembali ke Beranda</button>
                    </form>
                @else
                    <div class="status">Metode pembayaran tidak dikenali.</div>
                    <div class="btns">
                        <a href="{{ route('customer.home') }}" class="btn btn-secondary">Kembali ke Beranda</a>
                    </div>
                @endif
            @else
                <div class="status">Metode pembayaran tidak ditemukan.</div>
                <form method="POST" action="{{ route('customer.payment.cancel', ['booking' => $booking->id]) }}" class="btns">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Kembali ke Beranda</button>
                </form>
            @endif
        </div>
        <div style="margin-top:12px; text-align:center; color:var(--muted); font-size:12px;">Untuk Konfirmasi Pembayaran silahkan masuk pada menu pembayaran</div>
    </div>
</body>
</html>
