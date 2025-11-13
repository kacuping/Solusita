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
                                <th>ID</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Jadwal</th>
                                <th>Petugas</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Bayar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->id }}</td>
                                    <td>
                                        {{ optional($booking->customer)->name ?? '-' }}<br>
                                        <small class="text-muted">{{ optional($booking->customer)->email }}</small>
                                    </td>
                                    <td>{{ optional($booking->service)->name ?? '-' }}</td>
                                    <td>{{ optional($booking->scheduled_at)->format('d M Y H:i') }}</td>
                                    <td>
                                        @php
                                            $mainCleaner = optional($booking->cleaner)->full_name ?? (optional($booking->cleaner)->name ?? '-');
                                            $assistants = [];
                                            $notes = (string) ($booking->notes ?? '');
                                            if ($notes !== '' && preg_match('/assistants\s*:\s*([^|]+)/i', $notes, $m)) {
                                                $ids = collect(explode(',', trim($m[1])))
                                                    ->map(function ($v) { return (int) trim($v); })
                                                    ->filter(function ($v) { return $v > 0; });
                                                if ($ids->count() > 0) {
                                                    $assistants = \App\Models\Cleaner::whereIn('id', $ids)->pluck('full_name')->filter()->values()->all();
                                                    if (empty($assistants)) {
                                                        $assistants = \App\Models\Cleaner::whereIn('id', $ids)->pluck('name')->filter()->values()->all();
                                                    }
                                                }
                                            }
                                        @endphp
                                        {{ $mainCleaner }}
                                        @if (!empty($assistants))
                                            <div><small class="text-muted">Asisten: {{ implode(', ', $assistants) }}</small></div>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $method = null;
                                            $n = (string) ($booking->notes ?? '');
                                            if ($n !== '' && preg_match('/Metode\s+Pembayaran\s*:\s*([^|]+)/i', $n, $mm)) {
                                                $method = trim($mm[1]);
                                            }
                                        @endphp
                                        {{ $method ?? '-' }}
                                    </td>
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
                                                @php
                                                    $svcName = optional($booking->service)->name ?? '';
                                                    $needed = 1;
                                                    if ($svcName && preg_match('/(\d+)\s*Cleaner/i', $svcName, $m)) {
                                                        $needed = max(1, (int) $m[1]);
                                                    }
                                                    $extra = max(0, $needed - 1);
                                                @endphp
                                                @for ($i = 0; $i < $extra; $i++)
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
                                                @php($statusOptions = ['pending' => 'Pending', 'scheduled' => 'Terjadwal', 'in_progress' => 'Berjalan', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'])
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
