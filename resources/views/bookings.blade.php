@extends('adminlte::page')

@section('title', 'Pesanan')

@section('content_header')
    <h1>Pesanan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <p class="mb-3">Kelola pesanan/booking layanan kebersihan: daftar, status, penjadwalan, dan penugasan petugas.
            </p>

            <form method="get" class="row g-2 mb-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Semua</option>
                        @php($statuses = ['pending' => 'Pending', 'scheduled' => 'Terjadwal', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'])
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="q" class="form-label">Cari (nama/email pelanggan)</label>
                    <input type="text" name="q" id="q" class="form-control" value="{{ request('q') }}"
                        placeholder="Ketik untuk mencari...">
                </div>
                <div class="col-md-2 align-self-end">
                    <button class="btn btn-primary w-100" type="submit">Filter</button>
                </div>
                <div class="col-md-2 align-self-end">
                    <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>

            @if (isset($bookings) && $bookings->count())
                <div class="table-responsive">
                    <div class="text-muted mb-2"><i class="fas fa-hand-pointer"></i> Klik baris untuk assign petugas</div>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Jadwal</th>
                                <th>Petugas</th>
                                <th>Metode</th>
                                <th>DP</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Bayar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $booking)
                                <tr class="clickable-row" data-href="{{ route('bookings.assign.edit', $booking) }}" style="cursor:pointer;" title="Klik untuk assign petugas">
                                    @php($n = (string) ($booking->notes ?? ''))
                                    @php($ord = ($n !== '' && preg_match('/Order#:\s*(ORD-[0-9]+)/i', $n, $mm)) ? $mm[1] : ('#'.($booking->id)))
                                    <td>{{ $ord }}</td>
                                    <td>
                                        {{ optional($booking->customer)->name ?? '-' }}<br>
                                        <small class="text-muted">{{ optional($booking->customer)->email }}</small>
                                    </td>
                                    <td>{{ optional($booking->service)->name ?? '-' }}</td>
                                    <td>{{ optional($booking->scheduled_at)->format('d M Y H:i') }}</td>
                                    <td>
                                        {{ optional($booking->cleaner)->full_name ?? (optional($booking->cleaner)->name ?? '-') }}
                                        @php($assistants = $assistantNames[$booking->id] ?? [])
                                        @if (!empty($assistants))
                                            <div><small class="text-muted">Asisten: {{ implode(', ', $assistants) }}</small></div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ ($paymentMethods[$booking->id] ?? '-') }}
                                    </td>
                                    @php($notes = (string) ($booking->notes ?? ''))
                                    @php($raw = null)
                                    @php($raw = ($notes !== '' && preg_match('/PaymentKey\s*:\s*([^|]+)/i', $notes, $mmk)) ? strtolower(trim((string) $mmk[1])) : (($notes !== '' && preg_match('/Metode\s+Pembayaran\s*:\s*([^|]+)/i', $notes, $mm)) ? strtolower(trim((string) $mm[1])) : null))
                                    @php($isSameDay = optional($booking->scheduled_at)->isSameDay(now()))
                                    @php($dpReq = (! $isSameDay) && ($raw === 'cash'))
                                    @php($dpRaw = strtolower((string) ($booking->dp_status ?? 'none')))
                                    @php($dpRaw = ($dpRaw === '' || $dpRaw === 'none') ? (($notes !== '' && preg_match('/DP\s*Status\s*:\s*Paid/i', $notes)) ? 'paid' : (($notes !== '' && preg_match('/DP\s*Proof\s*:/i', $notes)) ? 'verifikasi' : (($notes !== '' && preg_match('/DP\s*:\s*Rp\s*/i', $notes)) ? 'unpaid' : 'none'))) : $dpRaw)
                                    @php($dpShow = $dpReq || $dpRaw !== 'none')
                                    <td>{{ $dpShow ? ($dpRaw === 'paid' ? 'Paid' : ($dpRaw === 'verifikasi' ? 'Verifikasi' : 'Unpaid')) : '-' }}</td>
                                    <td><span class="badge bg-info">{{ $booking->status }}</span></td>
                                    <td>Rp {{ number_format((float) $booking->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        {{ $booking->payment_status }}
                                        @if ($booking->payment_status !== 'paid')
                                            <form method="POST" action="{{ route('payments.status', $booking) }}" style="display:inline-block; margin-left:8px;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="payment_status" value="paid">
                                                <button class="btn btn-sm btn-success" title="Konfirmasi pembayaran" type="submit">✔</button>
                                            </form>
                                        @endif
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('bookings.destroy', $booking) }}" onsubmit="return confirm('Hapus jadwal ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" type="submit" title="Hapus" onclick="event.stopPropagation();">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <script>
                    document.querySelectorAll('.clickable-row').forEach(function(row){
                        row.addEventListener('click', function(e){
                            var target = e.target;
                            while (target && target !== row) {
                                if (target.tagName === 'BUTTON' || target.tagName === 'A' || target.closest('form')) {
                                    return;
                                }
                                target = target.parentElement;
                            }
                            var href = row.getAttribute('data-href');
                            if (href) window.location.href = href;
                        });
                    });
                </script>
                {{ $bookings->links() }}
            @else
                <div class="alert alert-info mb-0">Belum ada pesanan.</div>
            @endif
        </div>
    </div>
@stop
