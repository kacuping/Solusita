<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <title>Pembayaran - Pesanan #{{ $booking->id }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        :root {
            --bg: #f2f6ff;
            --text: #1f2d3d;
            --muted: #7b8ca6;
            --card: #fff;
            --shadow: 0 10px 20px rgba(0, 0, 0, .08);
            --primary: #4b88ff;
        }

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial;
            background: var(--bg);
            color: var(--text);
        }

        .app {
            max-width: 960px;
            margin: 0 auto;
            min-height: 100vh;
            min-height: 100dvh;
            padding: 16px;
        }

        .receipt {
            background: var(--card);
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 16px;
        }

        .title {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 8px;
        }

        .line {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 6px;
        }

        .divider {
            height: 1px;
            background: #e6edff;
            margin: 10px 0;
        }

        .status {
            background: #eef3ff;
            color: #2a57c4;
            padding: 8px 10px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .qr {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 10px 0;
        }

        .qr img {
            max-width: 80%;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        .btns {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }

        .btn {
            flex: 1;
            display: block;
            text-align: center;
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 12px;
            font-weight: 600;
        }

        .btn-primary {
            background: #4b88ff;
            color: #fff;
        }

        .btn-secondary {
            background: #e6edff;
            color: #2a57c4;
        }

        .muted {
            color: var(--muted);
            font-size: 12px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }

        .footer .bar {
            max-width: 960px;
            margin: 0 auto 8px;
            display: flex;
            justify-content: space-between;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, .10);
            padding: 6px 6px;
        }
        @media (min-width: 768px) { .footer .bar { padding: 8px 10px; } }

        .footer .item {
            text-decoration: none;
            color: var(--muted);
            text-align: center;
            font-size: 11px;
            flex: 1;
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
            color: #2a57c4;
            font-size: 16px;
        }

        .footer .item.active {
            color: var(--text);
            font-weight: 600;
        }

        .footer .item.active::after {
            content: '';
            display: block;
            width: 18px;
            height: 1.5px;
            border-radius: 2px;
            background: var(--primary);
            margin: 2px auto 0;
        }
    </style>
    @include('customer.partials.base-css')
    @include('customer.partials.base-js')
</head>

<body>
    <div class="app">
        <div class="receipt">
            <div class="title">Struk Pesanan #{{ $booking->id }}</div>
            @if (!empty($orderNo))
                <div class="line"><span>No. Order</span><span>{{ $orderNo }}</span></div>
            @endif
            <div class="line"><span>Layanan</span><span>{{ $service->name ?? 'Layanan' }}</span></div>
            <div class="line">
                <span>Jadwal</span><span>{{ optional($booking->scheduled_at)->format('d M Y H:i') }}</span>
            </div>
            @php($isDuration = strtolower(trim((string) ($service->unit_type ?? 'Durasi'))) === 'durasi')
            @if ($isDuration)
                <div class="line"><span>Durasi</span><span>{{ (int) ($booking->duration_minutes ?? 0) }} menit</span>
                </div>
            @else
                @php($n = (string) ($booking->notes ?? ''))
                @if ($n !== '' && preg_match('/Ukuran:\s*Panjang\s*([0-9.]+)m,\s*Lebar\s*([0-9.]+)m/i', $n, $mm))
                    <div class="line"><span>Ukuran</span><span>{{ $mm[1] }}m x {{ $mm[2] }}m</span>
                    </div>
                @elseif($n !== '' && preg_match('/Qty:\s*(\d+)/i', $n, $qm))
                    <div class="line"><span>Qty</span><span>{{ $qm[1] }} · Satuan:
                            {{ $service->unit_type ?? 'Satuan' }}</span></div>
                @endif
            @endif
            @php($dpAmountFixed = 50000)
            @php($notes = (string) ($booking->notes ?? ''))
            @php($dpPaid = \Illuminate\Support\Facades\Schema::hasColumn('bookings', 'dp_status') ? strtolower((string) ($booking->dp_status ?? 'none')) === 'paid' : ($notes !== '' && preg_match('/DP\s*Status\s*:\s*Paid/i', $notes) ? true : false))
            @php($total = (float) ($booking->total_amount ?? 0))
            @php($raw = strtolower((string) ($method ?? '')))
            @php($isSameDay = optional($booking->scheduled_at)->isSameDay(now()))
            @php($dpRequired = !$isSameDay && $raw === 'cash')
            @php($hasDpNote = $notes !== '' && preg_match('/DP\s*:\s*Rp\s*/i', $notes))
            @php($dpExists = $dpRequired || $hasDpNote || (\Illuminate\Support\Facades\Schema::hasColumn('bookings', 'dp_status') ? strtolower((string) ($booking->dp_status ?? 'none')) !== 'none' : false))
            @php($remain = max($total - ($dpPaid ? $dpAmountFixed : 0), 0))
            <div class="divider"></div>
            <div class="line"><span>Total</span><span>Rp {{ number_format($total, 0, ',', '.') }}</span></div>
            @if ($dpExists)
                <div class="line"><span>DP ({{ strtoupper($booking->dp_status) }})</span><span>- Rp
                        {{ number_format($dpPaid ? $dpAmountFixed : 0, 0, ',', '.') }}</span></div>
                <div class="line" style="font-weight:700"><span>Tagihan Saat Ini</span><span>Rp
                        {{ number_format($remain, 0, ',', '.') }}</span></div>
            @else
                <div class="line" style="font-weight:700"><span>Tagihan Saat Ini</span><span>Rp
                        {{ number_format($total, 0, ',', '.') }}</span></div>
            @endif
            <div class="divider"></div>

            @php($m = strtolower((string) ($method ?? '')))
            @if ($m === 'cash')
                @if ($dpPaid)
                    <div class="status">DP TERBAYAR. Siapkan sisa pembayaran tunai sebesar Rp
                        {{ number_format($remain, 0, ',', '.') }} saat petugas datang.</div>
                @else
                    <div class="status">Order akan diproses. Silakan lanjutkan transaksi, dan siapkan pembayaran tunai
                        sebesar Rp {{ number_format($remain, 0, ',', '.') }} saat petugas datang.</div>
                @endif
                <div class="muted">Metode: Tunai (Cash)</div>
                <form method="POST" action="{{ route('customer.payment.order', ['booking' => $booking->id]) }}"
                    class="btns">
                    @csrf
                    <button type="submit" class="btn btn-primary">Lanjutkan Order</button>
                </form>
                <form method="POST" action="{{ route('customer.payment.cancel', ['booking' => $booking->id]) }}"
                    class="btns">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Kembali ke Beranda</button>
                </form>
            @elseif(str_starts_with($m, 'option_') && ($paymentOption ?? null))
                @if (($paymentOption['type'] ?? '') === 'qris')
                    <div class="status">Detail Transaksi QRIS</div>
                    <div class="line"><span>Metode</span><span>{{ $paymentOption['label'] ?? 'QRIS' }}</span></div>
                    <div class="qr">
                        @if (!empty($paymentOption['qris_image_path']))
                            <img src="/{{ $paymentOption['qris_image_path'] }}" alt="QRIS">
                        @else
                            <span class="muted">QR IS belum tersedia</span>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('customer.payment.proof', ['booking' => $booking->id]) }}" enctype="multipart/form-data" class="btns">
                        @csrf
                        <input type="file" name="payment_proof" accept="image/*">
                        <button type="submit" class="btn btn-secondary">Upload Bukti (opsional)</button>
                    </form>
                    <form method="POST" action="{{ route('customer.payment.order', ['booking' => $booking->id]) }}"
                        class="btns">
                        @csrf
                        <button type="submit" class="btn btn-primary">Order</button>
                    </form>
                    <form method="POST" action="{{ route('customer.payment.cancel', ['booking' => $booking->id]) }}"
                        class="btns">
                        @csrf
                        <button type="submit" class="btn btn-secondary">Kembali ke Beranda</button>
                    </form>
                @elseif(($paymentOption['type'] ?? '') === 'transfer')
                    <div class="status">Detail Transaksi Transfer</div>
                    <div class="line"><span>Bank</span><span>{{ $paymentOption['bank_name'] ?? '-' }}</span></div>
                    <div class="line"><span>Nama
                            Rekening</span><span>{{ $paymentOption['bank_account_name'] ?? '-' }}</span></div>
                    <div class="line"><span>No.
                            Rekening</span><span>{{ $paymentOption['bank_account_number'] ?? '-' }}</span></div>
                    <form method="POST" action="{{ route('customer.payment.proof', ['booking' => $booking->id]) }}" enctype="multipart/form-data" class="btns">
                        @csrf
                        <input type="file" name="payment_proof" accept="image/*">
                        <button type="submit" class="btn btn-secondary">Upload Bukti (opsional)</button>
                    </form>
                    <form method="POST" action="{{ route('customer.payment.order', ['booking' => $booking->id]) }}"
                        class="btns">
                        @csrf
                        <button type="submit" class="btn btn-primary">Order</button>
                    </form>
                    <form method="POST" action="{{ route('customer.payment.cancel', ['booking' => $booking->id]) }}"
                        class="btns">
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
                <form method="POST" action="{{ route('customer.payment.cancel', ['booking' => $booking->id]) }}"
                    class="btns">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Kembali ke Beranda</button>
                </form>
            @endif
        </div>
        <div style="margin-top:12px; text-align:center; color:var(--muted); font-size:12px;">Untuk Konfirmasi Pembayaran
            silahkan masuk pada menu pembayaran</div>
        <div class="footer">
            @include('customer.partials.bottom-nav')
        </div>
    </div>
</body>

</html>
