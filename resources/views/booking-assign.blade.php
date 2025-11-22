@extends('adminlte::page')

@section('title', 'Assign Petugas')

@section('content_header')
    @php($n = (string) ($booking->notes ?? ''))
    @php($ord = ($n !== '' && preg_match('/Order#:\s*(ORD-[0-9]+)/i', $n, $mm)) ? $mm[1] : ('#'.($booking->id)))
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="mb-0"><i class="fas fa-receipt"></i> Nomor Order {{ $ord }}</h1>
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
    <nav aria-label="breadcrumb" class="mt-2">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('bookings.index') }}">Bookings</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nomor Order {{ $ord }}</li>
        </ol>
    </nav>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Ringkasan Booking</h5>
                    <div class="mb-2">
                        <strong>Nomor Order :</strong> {{ $ord }}
                    </div>
                    <div class="mb-2">
                        <strong>Pelanggan:</strong> {{ optional($booking->customer)->name ?? '-' }}<br>
                        <small class="text-muted">{{ optional($booking->customer)->email }}</small>
                    </div>
                    <div class="mb-2">
                        <strong>Layanan:</strong> {{ optional($booking->service)->name ?? '-' }}
                    </div>
                    <div class="mb-2">
                        <strong>Jadwal:</strong> {{ optional($booking->scheduled_at)->format('d M Y H:i') }}
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong>
                        <span class="badge {{ $booking->status === 'scheduled' ? 'bg-primary' : ($booking->status === 'completed' ? 'bg-success' : ($booking->status === 'cancelled' ? 'bg-danger' : 'bg-info')) }}">{{ $booking->status }}</span>
                    </div>
                    <form method="POST" action="{{ route('bookings.status', $booking) }}" class="row g-2 mb-3">
                        @csrf
                        @method('PATCH')
                        <div class="col-sm-8">
                            <label for="status" class="form-label">Status Pengerjaan</label>
                            <select name="status" id="status" class="form-control">
                                @foreach (($statusOptions ?? []) as $key => $label)
                                    <option value="{{ $key }}" {{ $booking->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4 align-self-end">
                            <button type="submit" class="btn btn-outline-success w-100"><i class="fas fa-sync"></i> Ubah Status</button>
                        </div>
                    </form>
                    <div class="mb-2">
                        <strong>Petugas saat ini:</strong>
                        <div>{{ optional($booking->cleaner)->full_name ?? (optional($booking->cleaner)->name ?? '-') }}</div>
                    </div>
                    @if (!empty($assistantNames ?? []))
                        <div class="mb-2">
                            <strong>Asisten saat ini:</strong>
                            <div><small class="text-muted">{{ implode(', ', $assistantNames) }}</small></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Form Penugasan</h5>
                    <p class="text-muted">Pilih petugas utama dan asisten (jika diperlukan).</p>
                    <form method="POST" action="{{ route('bookings.assign', $booking) }}" class="row g-3">
                        @csrf
                        @method('PATCH')
                        <div class="col-md-6">
                            <label for="cleaner_id" class="form-label">Petugas Utama</label>
                            <select name="cleaner_id" id="cleaner_id" class="form-control">
                                <option value="">-- Pilih Petugas --</option>
                                @foreach ($cleaners ?? [] as $c)
                                    <option value="{{ $c->id }}" {{ (int) optional($booking->cleaner)->id === (int) $c->id ? 'selected' : '' }}>{{ $c->full_name ?? $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @for ($i = 0; $i < ($assistantSlots ?? 0); $i++)
                            <div class="col-md-6">
                                <label class="form-label">Asisten {{ $i + 1 }}</label>
                                <select name="assistants[]" class="form-control">
                                    <option value="">-- Pilih Asisten --</option>
                                    @foreach ($cleaners ?? [] as $c)
                                        <option value="{{ $c->id }}">{{ $c->full_name ?? $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endfor
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Penugasan</button>
                            <a href="{{ route('bookings.index') }}" class="btn btn-secondary"><i class="fas fa-list"></i> Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
