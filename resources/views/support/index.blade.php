@extends('adminlte::page')

@section('title', 'Dukungan')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Tiket Dukungan</h1>
        <a href="{{ route('support.create') }}" class="btn btn-primary">Buat Tiket</a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('support.index') }}" class="form-inline">
                <label class="mr-2">Status:</label>
                <select name="status" class="form-control mr-2" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="open" {{ (request('status')==='open') ? 'selected' : '' }}>Open</option>
                    <option value="pending" {{ (request('status')==='pending') ? 'selected' : '' }}>Pending</option>
                    <option value="resolved" {{ (request('status')==='resolved') ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ (request('status')==='closed') ? 'selected' : '' }}>Closed</option>
                </select>
            </form>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Subject</th>
                        <th>Pelanggan</th>
                        <th>Petugas</th>
                        <th>Status</th>
                        <th>Prioritas</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $t)
                        <tr>
                            <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $t->subject }}</td>
                            <td>{{ optional($t->customer)->name ?? '-' }}</td>
                            <td>{{ optional($t->cleaner)->full_name ?? optional($t->cleaner)->name ?? '-' }}</td>
                            <td><span class="badge badge-info">{{ ucfirst($t->status) }}</span></td>
                            <td>{{ ucfirst($t->priority) }}</td>
                            <td class="text-right">
                                <a class="btn btn-sm btn-primary" href="{{ route('support.show', $t) }}">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada tiket.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $tickets->links() }}</div>
    </div>
@stop

