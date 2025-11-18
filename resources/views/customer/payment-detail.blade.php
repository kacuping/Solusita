<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <title>Detail Transaksi - Pesanan #{{ $booking->id }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        :root { --bg:#f2f6ff; --text:#1f2d3d; --muted:#7b8ca6; --card:#fff; --primary:#4b88ff; --shadow:0 10px 20px rgba(0,0,0,.08); }
        body { margin:0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial; background:var(--bg); color:var(--text); }
        .app { max-width:420px; margin:0 auto; min-height:100vh; min-height:100dvh; padding:16px 16px 88px; }
        .header { background:linear-gradient(180deg,#6aa4ff 0%,#9ec1ff 100%); color:#fff; border-radius:16px; padding:16px; box-shadow:var(--shadow); }
        .title { font-weight:700; font-size:16px; }
        .card { background:var(--card); border-radius:14px; box-shadow:var(--shadow); padding:12px; margin-top:12px; }
        .row { display:flex; justify-content:space-between; font-size:13.5px; margin-bottom:6px; }
        .muted { color:var(--muted); font-size:12px; }
        .actions { display:flex; gap:8px; margin-top:10px; }
        .btn { flex:1; display:block; text-align:center; text-decoration:none; padding:10px 12px; border-radius:12px; font-weight:600; }
        .btn-primary { background:#4b88ff; color:#fff; }
        .btn-secondary { background:#e6edff; color:#2a57c4; }
        .btn-mini { display:inline-block; padding:4px 8px; border-radius:10px; font-size:11px; background:#e6edff; color:#2a57c4; text-decoration:none; }
        .btn-mini.disabled { opacity:.6; pointer-events:none; cursor:not-allowed; }
        .btn-danger { background:#ef4444; color:#fff; }
        .footer { position:fixed; bottom:0; left:0; right:0; }
        .footer .bar { max-width:420px; margin:0 auto 8px; display:flex; justify-content:space-between; background:#fff; border-radius:18px; box-shadow:0 10px 20px rgba(0,0,0,.10); padding:6px 6px; }
        .footer .item { text-decoration:none; color:var(--muted); text-align:center; font-size:11px; flex:1; position:relative; padding:4px 0; border-radius:14px; }
        .footer .item .ico { display:flex; align-items:center; justify-content:center; width:28px; height:28px; margin:0 auto 2px; color:#2a57c4; font-size:16px; }
        .footer .item.active { color:var(--text); font-weight:600; }
        .footer .item.active::after { content:''; display:block; width:18px; height:1.5px; border-radius:2px; background: var(--primary); margin:2px auto 0; }
    </style>
    @include('customer.partials.base-css')
    @include('customer.partials.base-js')
    </head>
<body>
    <div class="app">
        <div class="header"><div class="title">Detail Transaksi</div></div>
        <div class="card">
            @if ($orderNo)
                <div class="row"><span>No. Order</span><span>{{ $orderNo }}</span></div>
            @endif
            <div class="row"><span>Layanan</span><span>{{ optional(optional($booking)->service)->name ?? '-' }}</span></div>
            <div class="row"><span>Jadwal</span><span>{{ optional($booking->scheduled_at)->format('d M Y H:i') }}</span></div>
            <div class="row"><span>Metode</span><span>{{ $methodLabel }}</span></div>
            <div class="row" style="font-weight:700"><span>Total</span><span>Rp {{ number_format((float) ($booking->total_amount ?? 0), 0, ',', '.') }}</span></div>
            @if ($dpExists)
                <div class="row"><span>DP</span><span>Rp {{ number_format(50000, 0, ',', '.') }} · {{ $dpPaid ? 'Paid' : ($dpVerif ? 'Verifikasi' : 'Unpaid') }}
                    @if (! $dpPaid && ! $dpVerif)
                        <a href="{{ route('customer.dp.show', ['booking' => $booking->id]) }}" class="btn-mini" style="margin-left:8px;">Bayar DP</a>
                    @else
                        <span class="btn-mini disabled" style="margin-left:8px;">Bayar DP</span>
                    @endif
                </span></div>
            @endif
            <div class="row"><span>Status Pembayaran</span><span>{{ strtolower((string) ($booking->payment_status ?? 'unpaid')) === 'paid' ? 'Paid' : 'Unpaid' }}</span></div>
            @php($dpAmountFixed = 50000)
            @php($dpPaidLocal = !!$dpPaid)
            @php($total = (float) ($booking->total_amount ?? 0))
            @php($remain = max($total - ($dpPaidLocal ? $dpAmountFixed : 0), 0))
            <div class="row"><span>Tagihan Saat Ini</span><span>Rp {{ number_format($remain, 0, ',', '.') }}</span></div>
            <div class="actions">
                @if ((string) ($booking->payment_status ?? 'unpaid') !== 'paid')
                    <form method="POST" action="{{ route('customer.payment.cancel', ['booking' => $booking->id]) }}" style="flex:1;">
                        @csrf
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Batalkan transaksi ini?');">Batalkan</button>
                    </form>
                @endif
                <a class="btn btn-secondary" href="{{ route('customer.payments.index') }}">Kembali</a>
                @if (strtolower((string) ($methodRaw ?? '')) !== 'cash')
                <a class="btn btn-primary" href="{{ route('customer.payment.show', ['booking' => $booking->id]) }}">Bayar</a>
                @endif
            </div>
        </div>
        <div class="footer">
            @include('customer.partials.bottom-nav')
        </div>
    </div>
</body>
</html>
