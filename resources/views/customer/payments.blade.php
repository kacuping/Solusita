<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <title>Pembayaran Saya</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        :root { --bg:#f2f6ff; --text:#1f2d3d; --muted:#7b8ca6; --card:#fff; --primary:#4b88ff; --shadow:0 10px 20px rgba(0,0,0,.08); }
        body { margin:0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial; background:var(--bg); color:var(--text); }
        .app { max-width:420px; margin:0 auto; min-height:100vh; min-height:100dvh; padding:16px 16px 88px; }
        .header { background:linear-gradient(180deg,#6aa4ff 0%,#9ec1ff 100%); color:#fff; border-radius:16px; padding:16px; box-shadow:var(--shadow); }
        .title { font-weight:700; font-size:16px; }
        .section { margin-top:12px; }
        .section h3 { margin:0 0 8px; font-size:14px; color:#2a57c4; }
        .card { background:var(--card); border-radius:14px; box-shadow:var(--shadow); padding:12px; margin-bottom:10px; }
        .row { display:flex; justify-content:space-between; font-size:13.5px; margin-bottom:6px; }
        .muted { color:var(--muted); font-size:12px; }
        .actions { display:flex; gap:8px; margin-top:8px; }
        .btn { flex:1; display:block; text-align:center; text-decoration:none; padding:10px 12px; border-radius:12px; font-weight:600; }
        .btn-primary { background:#4b88ff; color:#fff; }
        .btn-secondary { background:#e6edff; color:#2a57c4; }
        .footer { position:fixed; bottom:0; left:0; right:0; }
        .footer .bar { max-width:420px; margin:0 auto 8px; display:flex; justify-content:space-between; background:#fff; border-radius:18px; box-shadow:0 10px 20px rgba(0,0,0,.10); padding:6px 6px; }
        .footer .item { text-decoration:none; color:var(--muted); text-align:center; font-size:11px; flex:1; position:relative; padding:4px 0; border-radius:14px; }
        .footer .item .ico { display:flex; align-items:center; justify-content:center; width:28px; height:28px; margin:0 auto 2px; color:#2a57c4; font-size:16px; }
        .footer .item.active { color:var(--text); font-weight:600; }
        .footer .item.active::after { content:''; display:block; width:18px; height:1.5px; border-radius:2px; background:var(--primary); margin:2px auto 0; }
    </style>
    @include('customer.partials.base-css')
    @include('customer.partials.base-js')
    </head>
<body>
    <div class="app">
        <div class="header"><div class="title">Pembayaran Saya</div></div>

        <div class="section">
            <h3>Belum Dibayar</h3>
            @forelse($pending as $b)
                <div class="card">
                    <div class="row"><span>Pesanan</span><span>#{{ $b->id }}</span></div>
                    <div class="row"><span>Layanan</span><span>{{ optional(optional($b)->service)->name ?? '-' }}</span></div>
                    <div class="row"><span>Jadwal</span><span>{{ optional($b->scheduled_at)->format('d M Y H:i') }}</span></div>
                    <div class="row"><span>Metode</span><span>{{ $paymentMethods[$b->id] ?? '-' }}</span></div>
                    <div class="row" style="font-weight:700"><span>Total</span><span>Rp {{ number_format((float) ($b->total_amount ?? 0), 0, ',', '.') }}</span></div>
                    @php($hasDp = (bool) ($dpExists[$b->id] ?? false))
                    @php($dpOk = (bool) ($dpPaid[$b->id] ?? false))
                    @php($dpV = (bool) ($dpVerif[$b->id] ?? false))
                    @if ($hasDp)
                        <div class="row"><span>DP</span><span>Rp {{ number_format(50000, 0, ',', '.') }} · {{ $dpOk ? 'Paid' : ($dpV ? 'Verifikasi' : 'Unpaid') }}</span></div>
                    @endif
                    <div class="row"><span>Status Pembayaran</span><span>{{ strtolower((string) ($b->payment_status ?? 'unpaid')) === 'paid' ? 'Paid' : 'Unpaid' }}</span></div>
                    @php($needDp = (bool) ($dpRequired[$b->id] ?? false))
                    @php($dpOk = (bool) ($dpPaid[$b->id] ?? false))
                    @php($dpV = (bool) ($dpVerif[$b->id] ?? false))
                    @php($raw = strtolower((string) ($paymentRaw[$b->id] ?? '')))
                    @if ($needDp && ! $dpOk && ! $dpV && $raw !== 'cash')
                        <div class="row"><span>DP</span><span style="color:#b45309;">Belum terbayar (Rp 50.000)</span></div>
                        <div class="actions">
                            <a class="btn btn-primary" href="{{ route('customer.dp.show', ['booking' => $b->id]) }}">Bayar DP</a>
                            <a class="btn btn-secondary" href="{{ route('customer.payment.detail', ['booking' => $b->id]) }}">Detail Pembayaran</a>
                        </div>
                    @else
                        <div class="actions">
                            <a class="btn btn-primary" href="{{ route('customer.payment.detail', ['booking' => $b->id]) }}">Detail Pembayaran</a>
                            <a class="btn btn-secondary" href="{{ route('customer.payment.cancel', ['booking' => $b->id]) }}" onclick="return confirm('Batalkan transaksi ini?');">Batalkan</a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="card"><div class="muted">Tidak ada pembayaran pending.</div></div>
            @endforelse
        </div>

        <div class="section">
            <h3>Sudah Dibayar</h3>
            @forelse($completed as $b)
                <div class="card">
                    <div class="row"><span>Pesanan</span><span>#{{ $b->id }}</span></div>
                    <div class="row"><span>Layanan</span><span>{{ optional(optional($b)->service)->name ?? '-' }}</span></div>
                    <div class="row"><span>Jadwal</span><span>{{ optional($b->scheduled_at)->format('d M Y H:i') }}</span></div>
                    <div class="row"><span>Metode</span><span>{{ $paymentMethods[$b->id] ?? '-' }}</span></div>
                    <div class="row" style="font-weight:700"><span>Total</span><span>Rp {{ number_format((float) ($b->total_amount ?? 0), 0, ',', '.') }}</span></div>
                    @php($hasDpC = (bool) ($dpExists[$b->id] ?? false))
                    @php($dpOkC = (bool) ($dpPaid[$b->id] ?? false))
                    @php($dpVC = (bool) ($dpVerif[$b->id] ?? false))
                    @if ($hasDpC)
                        <div class="row"><span>DP</span><span>Rp {{ number_format(50000, 0, ',', '.') }} · {{ $dpOkC ? 'Paid' : ($dpVC ? 'Verifikasi' : 'Unpaid') }}</span></div>
                    @endif
                    <div class="row"><span>Status Pembayaran</span><span>{{ strtolower((string) ($b->payment_status ?? 'unpaid')) === 'paid' ? 'Paid' : 'Unpaid' }}</span></div>
                    <div class="actions">
                        <a class="btn btn-secondary" href="{{ route('customer.payment.detail', ['booking' => $b->id]) }}">Detail Pembayaran</a>
                        <a class="btn btn-secondary" href="{{ route('customer.home') }}">Beranda</a>
                    </div>
                </div>
            @empty
                <div class="card"><div class="muted">Belum ada pembayaran selesai.</div></div>
            @endforelse
        </div>

        <div class="footer">
            @include('customer.partials.bottom-nav')
        </div>
    </div>
</body>
</html>
