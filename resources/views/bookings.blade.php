@extends('adminlte::page')

@section('title', 'Pesanan')

@section('content_header')
    <h1>Pesanan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <p class="mb-3">Kelola pesanan/booking layanan kebersihan: daftar, status, penjadwalan, dan penugasan petugas.</p>

            <form method="get" class="row g-2 mb-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Semua</option>
                        @php($statuses = ['pending' => 'Pending', 'scheduled' => 'Terjadwal', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'])
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="q" class="form-label">Cari (nama/email pelanggan)</label>
                    <input type="text" name="q" id="q" class="form-control" value="{{ request('q') }}" placeholder="Ketik untuk mencari...">
                </div>
                <div class="col-md-2 align-self-end">
                    <button class="btn btn-primary w-100" type="submit">Filter</button>
                </div>
                <div class="col-md-2 align-self-end">
                    <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>

            @if(isset($bookings) && $bookings->count())
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Jadwal</th>
                                <th>Petugas</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->id }}</td>
                                    <td>
                                        {{ optional($booking->customer)->name ?? '-' }}<br>
                                        <small class="text-muted">{{ optional($booking->customer)->email }}</small>
                                    </td>
                                    <td>{{ optional($booking->service)->name ?? '-' }}</td>
                                    <td>{{ optional($booking->scheduled_at)->format('d M Y H:i') }}</td>
                                    <td>{{ optional($booking->cleaner)->full_name ?? optional($booking->cleaner)->name ?? '-' }}</td>
                                    <td><span class="badge bg-info">{{ $booking->status }}</span></td>
                                    <td>Rp {{ number_format((float)$booking->total_amount, 0, ',', '.') }}</td>
                                    <td>{{ $booking->payment_status }}</td>
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
