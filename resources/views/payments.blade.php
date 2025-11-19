@extends('adminlte::page')

@section('title', 'Pembayaran')

@section('content_header')
    <h1>Pembayaran</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <p class="mb-3">Kelola pembayaran untuk pesanan: lihat status, filter, dan ubah status pembayaran.</p>

            <div class="mb-4">
                <form method="POST" action="{{ route('payments.methods.active') }}" class="border rounded p-3">
                    @csrf
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Metode Pembayaran Aktif</strong>
                        <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="cash_active" id="opt_cash" value="1" {{ !empty($cashActive) ? 'checked' : '' }}>
                                <label class="form-check-label" for="opt_cash">Tunai (Cash)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @forelse(($paymentOptions ?? []) as $opt)
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="active_ids[]" id="opt_{{ $opt['id'] }}" value="{{ $opt['id'] }}" {{ !empty($opt['active']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="opt_{{ $opt['id'] }}">{{ $opt['label'] ?? '-' }} ({{ strtoupper($opt['type'] ?? '-') }})</label>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-muted">Belum ada pilihan pembayaran. Tambahkan di halaman Pengaturan.</div>
                        @endforelse
                    </div>
                </form>
            </div>

            <form method="get" class="row g-2 mb-3">
                <div class="col-md-3">
                    <label for="payment_status" class="form-label">Status Pembayaran</label>
                    <select name="payment_status" id="payment_status" class="form-control">
                        <option value="">Semua</option>
                        @php($statuses = ['unpaid' => 'Belum Bayar', 'verifikasi' => 'Verifikasi', 'paid' => 'Terbayar', 'refunded' => 'Direfund', 'failed' => 'Gagal'])
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('payment_status') === $key ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="q" class="form-label">Cari (nama/email pelanggan)</label>
                    <input type="text" name="q" id="q" class="form-control" value="{{ request('q') }}"
                        placeholder="Ketik untuk mencari...">
                </div>
                <div class="col-md-2">
                    <label for="from" class="form-label">Dari Tanggal</label>
                    <input type="date" name="from" id="from" class="form-control" value="{{ request('from') }}">
                </div>
                <div class="col-md-2">
                    <label for="to" class="form-label">Sampai Tanggal</label>
                    <input type="date" name="to" id="to" class="form-control" value="{{ request('to') }}">
                </div>
                <div class="col-md-2 align-self-end">
                    <button class="btn btn-primary w-100" type="submit">Filter</button>
                </div>
            </form>

            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="alert alert-success mb-0">
                        <div>Terbayar</div>
                        <strong>Rp {{ number_format((float) ($summary['paid_net_total'] ?? 0), 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="alert alert-warning mb-0">
                        <div>Belum Bayar</div>
                        <strong>Rp {{ number_format((float) ($summary['unpaid_total'] ?? 0), 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="alert alert-danger mb-0">
                        <div>Gagal</div>
                        <strong>Rp {{ number_format((float) ($summary['failed_total'] ?? 0), 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="alert alert-secondary mb-0">
                        <div>Direfund</div>
                        <strong>Rp {{ number_format((float) ($summary['refunded_total'] ?? 0), 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="col-md-3 mt-2">
                    <div class="alert alert-info mb-0">
                        <div>Total Pembayaran DP</div>
                        <strong>Rp {{ number_format((float) ($summary['dp_total'] ?? 0), 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>

            @if (isset($bookings) && $bookings->count())
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Total</th>
                                <th>DP</th>
                                <th>Status Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $index => $booking)
                                <tr>
                                    <td>{{ ($bookings->currentPage() - 1) * $bookings->perPage() + $index + 1 }}</td>
                                    @php($n = (string) ($booking->notes ?? ''))
                                    @php($ord = ($n !== '' && preg_match('/Order#:\s*(ORD-[0-9]+)/i', $n, $mm)) ? $mm[1] : ('#'.($booking->id)))
                                    <td>{{ $ord }}</td>
                                    <td>{{ optional($booking->created_at)->format('d M Y H:i') }}</td>
                                    <td>
                                        {{ optional($booking->customer)->name ?? '-' }}<br>
                                        <small class="text-muted">{{ optional($booking->customer)->email }}</small>
                                    </td>
                                    <td>{{ optional($booking->service)->name ?? '-' }}</td>
                                    <td>Rp {{ number_format((float) $booking->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        @php(
                                            $dpMap = ['none' => 'secondary', 'unpaid' => 'warning', 'verifikasi' => 'info', 'paid' => 'success']
                                        )
                                        @php(
                                            $dpRaw = strtolower((string) ($booking->dp_status ?? 'none'))
                                        )
                                        @php(
                                            $n = (string) ($booking->notes ?? '')
                                        )
                                        @php(
                                            $dpRaw = ($dpRaw === '' || $dpRaw === 'none')
                                                ? (
                                                    ( $n !== '' && preg_match('/DP\s*Status\s*:\s*Paid/i', $n) ) ? 'paid'
                                                    : ( ( $n !== '' && preg_match('/DP\s*Proof\s*:/i', $n) ) ? 'verifikasi'
                                                    : ( ( $n !== '' && preg_match('/DP\s*:\s*Rp\s*/i', $n) ) ? 'unpaid' : 'none' ) )
                                                )
                                                : $dpRaw
                                        )
                                        @if($dpRaw === 'none')
                                            -
                                        @else
                                            <span class="badge bg-{{ $dpMap[$dpRaw] ?? 'secondary' }}">{{ $dpRaw }}</span>
                                        @endif
                                        @if($booking->dp_proof)
                                            <div><a href="{{ $booking->dp_proof }}" target="_blank">Bukti</a></div>
                                        @endif
                                    </td>
                                    <td>
                                        @php($map = ['unpaid' => 'secondary', 'verifikasi' => 'info', 'paid' => 'success', 'refunded' => 'warning', 'failed' => 'danger'])
                                        <span class="badge bg-{{ $map[$booking->payment_status] ?? 'secondary' }}">{{ $booking->payment_status }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <form method="POST" action="{{ route('payments.status', $booking) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="payment_status" value="verifikasi">
                                                <button class="btn btn-sm btn-outline-info" type="submit">Verifikasi</button>
                                            </form>
                                            <form method="POST" action="{{ route('payments.dpstatus', $booking) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="dp_status" value="paid">
                                                <button class="btn btn-sm btn-outline-success" type="submit">DP Terbayar</button>
                                            </form>
                                            <form method="POST" action="{{ route('payments.dpstatus', $booking) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="dp_status" value="unpaid">
                                                <button class="btn btn-sm btn-outline-secondary" type="submit">DP Belum Bayar</button>
                                            </form>
                                            <form method="POST" action="{{ route('payments.status', $booking) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="payment_status" value="paid">
                                                <button class="btn btn-sm btn-outline-success" type="submit">Tandai Terbayar</button>
                                            </form>
                                            <form method="POST" action="{{ route('payments.status', $booking) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="payment_status" value="unpaid">
                                                <button class="btn btn-sm btn-outline-secondary" type="submit">Belum Bayar</button>
                                            </form>
                                            <form method="POST" action="{{ route('payments.status', $booking) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="payment_status" value="refunded">
                                                <button class="btn btn-sm btn-outline-warning" type="submit">Refund</button>
                                            </form>
                                            <form method="POST" action="{{ route('payments.status', $booking) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="payment_status" value="failed">
                                                <button class="btn btn-sm btn-outline-danger" type="submit">Gagal</button>
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
                <div class="alert alert-info mb-0">Belum ada data pembayaran.</div>
            @endif
        </div>
    </div>
@stop
