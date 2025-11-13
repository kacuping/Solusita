@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $bookingsCount ?? 0 }}</h3>
                    <p>Total Pesanan</p>
                </div>
                <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                <a href="{{ route('bookings.index') }}" class="small-box-footer">Lihat Pesanan <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pendingCount ?? 0 }}</h3>
                    <p>Pending</p>
                </div>
                <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                <a href="{{ route('bookings.index', ['status' => 'pending']) }}" class="small-box-footer">Tindaklanjuti <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $scheduledToday ?? 0 }}</h3>
                    <p>Jadwal Hari Ini</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-day"></i></div>
                <a href="{{ route('schedule.index', ['month' => now()->format('Y-m')]) }}" class="small-box-footer">Buka Jadwal <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>Rp {{ number_format($paidTotal ?? 0, 0, ',', '.') }}</h3>
                    <p>Pendapatan Terbayar</p>
                </div>
                <div class="icon"><i class="fas fa-money-bill"></i></div>
                <a href="{{ route('payments.index', ['payment_status' => 'paid']) }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Ringkasan Pembayaran</span>
                    <span class="text-muted">Belum Bayar: Rp {{ number_format($unpaidTotal ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Pelanggan</th>
                                    <th>Layanan</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($recentBookings ?? collect()) as $b)
                                    <tr>
                                        <td>{{ optional($b->created_at)->format('d M Y H:i') }}</td>
                                        <td>{{ optional($b->customer)->name }}</td>
                                        <td>{{ optional($b->service)->name }}</td>
                                        <td>{{ $b->payment_status }}</td>
                                        <td>Rp {{ number_format($b->total_amount ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada data terbaru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
