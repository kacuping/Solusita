@extends('adminlte::page')

@section('title', 'Jadwal')

@section('content_header')
    <h1>Jadwal</h1>
@stop

@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('schedule.index') }}" class="form-inline">
                <label class="mr-2">Bulan:</label>
                <input type="month" name="month" value="{{ request('month', optional($monthStart)->format('Y-m')) }}" class="form-control mr-2">
                <button class="btn btn-primary" type="submit">Terapkan</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">Kalender {{ optional($monthStart)->format('F Y') }}</div>
                <div class="card-body p-0">
                    @php
                        $start = $monthStart ?? now()->startOfMonth();
                        $firstWeekday = (int) $start->copy()->startOfMonth()->dayOfWeekIso; // 1..7 (Mon..Sun)
                        $daysInMonth = (int) $start->daysInMonth;
                        $cells = [];
                        for ($i = 1; $i < $firstWeekday; $i++) { $cells[] = null; }
                        for ($d = 1; $d <= $daysInMonth; $d++) { $cells[] = $d; }
                        while (count($cells) % 7 !== 0) { $cells[] = null; }
                    @endphp
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Sen</th><th>Sel</th><th>Rab</th><th>Kam</th><th>Jum</th><th>Sab</th><th>Min</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $cells = $cells ?? []; @endphp
                            @foreach (array_chunk($cells, 7) as $week)
                                <tr>
                                    @foreach ($week as $day)
                                        @php
                                            $dateStr = $day ? $start->copy()->day($day)->toDateString() : null;
                                            $items = $day && isset($byDay[$dateStr]) ? $byDay[$dateStr] : collect();
                                        @endphp
                                        <td style="vertical-align:top;">
                                            @if ($day)
                                                @php $dateStr = $start->copy()->day($day)->toDateString(); @endphp
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <strong>{{ $day }}</strong>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#dayModal" data-date="{{ $dateStr }}">Lihat</button>
                                                    </div>
                                                </div>
                                                @foreach ($items->take(3) as $b)
                                                    <div style="font-size:12px; color:#555;">
                                                        {{ optional($b->service)->name }}
                                                        <span class="text-muted">@ {{ optional($b->scheduled_at)->format('H:i') }}</span>
                                                    </div>
                                                @endforeach
                                                <template id="tpl-{{ $dateStr }}">
                                                    <div>
                                                        @forelse(($byDay[$dateStr] ?? collect()) as $bk)
                                                            <div class="mb-2">
                                                                <strong>{{ optional($bk->service)->name }}</strong>
                                                                <span class="text-muted">@ {{ optional($bk->scheduled_at)->format('H:i') }}</span>
                                                                <div style="font-size:12px;">{{ optional($bk->customer)->name }}{{ optional($bk->cleaner)->full_name ? ' · '.optional($bk->cleaner)->full_name : '' }}</div>
                                                            </div>
                                                        @empty
                                                            <div class="text-muted">Belum ada jadwal untuk tanggal ini.</div>
                                                        @endforelse
                                                    </div>
                                                </template>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">Daftar Jadwal Bulan Ini</div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Layanan</th>
                                <th>Pelanggan</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bookings as $b)
                                <tr>
                                    <td>{{ optional($b->scheduled_at)->format('d M H:i') }}</td>
                                    <td>{{ optional($b->service)->name }}</td>
                                    <td>{{ optional($b->customer)->name }}</td>
                                    <td>{{ optional($b->cleaner)->full_name ?? optional($b->cleaner)->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada jadwal pada bulan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="dayModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Jadwal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="dayModalList" class="mb-3"></div>
                    <form method="POST" action="{{ route('bookings.quick_create') }}" class="border rounded p-3">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dayModalDate" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jam</label>
                                <input type="time" name="time" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Layanan</label>
                                <select name="service_id" class="form-control" required>
                                    @foreach(($services ?? []) as $svc)
                                        <option value="{{ $svc->id }}">{{ $svc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pelanggan</label>
                                <select name="customer_id" class="form-control" required>
                                    @foreach(($customers ?? []) as $cust)
                                        <option value="{{ $cust->id }}">{{ $cust->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-2">
                            <label class="form-label">Catatan</label>
                            <input type="text" name="notes" class="form-control" placeholder="Opsional">
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-primary" type="submit">Tambah Jadwal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function(){
            var modal = document.getElementById('dayModal');
            if(!modal) return;
            $('#dayModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var dateStr = button.data('date');
                var tpl = document.getElementById('tpl-'+dateStr);
                var target = document.getElementById('dayModalList');
                var dateInput = document.getElementById('dayModalDate');
                if (tpl && target) { target.innerHTML = tpl.innerHTML; }
                if (dateInput) { dateInput.value = dateStr; }
            });
        })();
    </script>
@stop
