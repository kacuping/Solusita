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
                    <table class="table table-striped">
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
                                <tr>
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
                                        <div class="d-flex gap-2 flex-wrap">
                                            <form method="POST" action="{{ route('bookings.assign', $booking) }}"
                                                class="form-inline d-flex align-items-center" style="gap:.5rem;">
                                                @csrf
                                                @method('PATCH')
                                                <select name="cleaner_id" class="form-control form-control-sm"
                                                    style="min-width:160px;">
                                                    <option value="">-- Pilih Petugas --</option>
                                                    @foreach ($cleaners ?? [] as $c)
                                                        <option value="{{ $c->id }}"
                                                            {{ (int) optional($booking->cleaner)->id === (int) $c->id ? 'selected' : '' }}>
                                                            {{ $c->full_name ?? $c->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @for ($i = 0; $i < ($assistantSlots[$booking->id] ?? 0); $i++)
                                                    <select name="assistants[]" class="form-control form-control-sm" style="min-width:160px;">
                                                        <option value="">-- Asisten {{ $i + 1 }} --</option>
                                                        @foreach ($cleaners ?? [] as $c)
                                                            <option value="{{ $c->id }}">{{ $c->full_name ?? $c->name }}</option>
                                                        @endforeach
                                                    </select>
                                                @endfor
                                                <button class="btn btn-sm btn-outline-primary"
                                                    type="submit">Assign</button>
                                            </form>

                                            <form method="POST" action="{{ route('bookings.status', $booking) }}"
                                                class="form-inline d-flex align-items-center" style="gap:.5rem;">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" class="form-control form-control-sm"
                                                    style="min-width:160px;">
                                                    @foreach ($statusOptions as $key => $label)
                                                        <option value="{{ $key }}"
                                                            {{ $booking->status === $key ? 'selected' : '' }}>
                                                            {{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                <button class="btn btn-sm btn-outline-success"
                                                    type="submit">Update</button>
                                            </form>
                                            <form method="POST" action="{{ route('bookings.destroy', $booking) }}" onsubmit="return confirm('Hapus jadwal ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $bookings->links() }}
            @else
                <div class="alert alert-info mb-0">Belum ada pesanan.</div>
            @endif
        </div>
    </div>
@stop
