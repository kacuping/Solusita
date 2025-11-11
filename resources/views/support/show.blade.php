@extends('adminlte::page')

@section('title', 'Detail Tiket')

@section('content_header')
    <h1>Detail Tiket</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Informasi Tiket</div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Subject</dt>
                        <dd class="col-sm-9">{{ $ticket->subject }}</dd>

                        <dt class="col-sm-3">Pelanggan</dt>
                        <dd class="col-sm-9">{{ optional($ticket->customer)->name ?? '-' }} (ID: {{ $ticket->customer_id }})</dd>

                        <dt class="col-sm-3">Booking</dt>
                        <dd class="col-sm-9">{{ $ticket->booking_id ?? '-' }}</dd>

                        <dt class="col-sm-3">Petugas</dt>
                        <dd class="col-sm-9">{{ optional($ticket->cleaner)->full_name ?? optional($ticket->cleaner)->name ?? '-' }}</dd>

                        <dt class="col-sm-3">Prioritas</dt>
                        <dd class="col-sm-9">{{ ucfirst($ticket->priority) }}</dd>

                        <dt class="col-sm-3">Status</dt>
                        <dd class="col-sm-9">{{ ucfirst($ticket->status) }}</dd>

                        <dt class="col-sm-3">Pesan</dt>
                        <dd class="col-sm-9">{{ $ticket->message }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Lampiran</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('support.attach', $ticket) }}" enctype="multipart/form-data" class="mb-3">
                        @csrf
                        <div class="form-row">
                            <div class="col">
                                <input type="file" name="file" class="form-control-file" required>
                            </div>
                            <div class="col text-right">
                                <button class="btn btn-primary" type="submit">Upload</button>
                            </div>
                        </div>
                    </form>
                    <ul class="list-group">
                        @forelse($ticket->attachments as $att)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ asset('storage/'.$att->path) }}" target="_blank">{{ $att->original_name }}</a>
                                    <small class="text-muted"> ({{ $att->mime_type }}, {{ number_format($att->size/1024, 1) }} KB)</small>
                                </div>
                                <form action="{{ route('support.attach.delete', [$ticket, $att->id]) }}" method="POST" onsubmit="return confirm('Hapus lampiran ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                                </form>
                            </li>
                        @empty
                            <li class="list-group-item">Belum ada lampiran.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Ubah Status</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('support.status', $ticket) }}">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                @foreach(['open','pending','resolved','closed'] as $st)
                                    <option value="{{ $st }}" {{ $ticket->status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Assign ke Petugas</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('support.assign', $ticket) }}">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <label for="cleaner_id">Petugas</label>
                            <select name="cleaner_id" id="cleaner_id" class="form-control">
                                <option value="">-- Tidak ada --</option>
                                @foreach($cleaners as $c)
                                    <option value="{{ $c->id }}" {{ $ticket->cleaner_id == $c->id ? 'selected' : '' }}>{{ $c->full_name ?? $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

