@extends('adminlte::page')

@section('title', 'Ulasan')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Ulasan Pelanggan</h1>
        <form method="GET" action="{{ route('reviews.index') }}" class="form-inline">
            <label class="mr-2">Status:</label>
            <select name="status" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">Semua</option>
                <option value="pending" {{ (request('status')==='pending') ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ (request('status')==='approved') ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ (request('status')==='rejected') ? 'selected' : '' }}>Ditolak</option>
            </select>
        </form>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Ringkasan per Layanan</div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Layanan</th>
                                <th>Rata-rata</th>
                                <th>Total Ulasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($serviceSummary as $row)
                                <tr>
                                    <td>{{ optional($row['service'])->name ?? '-' }}</td>
                                    <td>{{ number_format($row['avg_rating'], 2) }}</td>
                                    <td>{{ $row['total'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Ringkasan per Petugas</div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Petugas</th>
                                <th>Rata-rata</th>
                                <th>Total Ulasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cleanerSummary as $row)
                                <tr>
                                    <td>{{ optional($row['cleaner'])->full_name ?? optional($row['cleaner'])->name ?? '-' }}</td>
                                    <td>{{ number_format($row['avg_rating'], 2) }}</td>
                                    <td>{{ $row['total'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Daftar Ulasan</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Petugas</th>
                        <th>Rating</th>
                        <th>Komentar</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr>
                            <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ optional($review->customer)->name ?? '-' }}</td>
                            <td>{{ optional(optional($review->booking)->service)->name ?? '-' }}</td>
                            <td>{{ optional(optional($review->booking)->cleaner)->full_name ?? optional(optional($review->booking)->cleaner)->name ?? '-' }}</td>
                            <td>{{ $review->rating }}</td>
                            <td style="max-width: 300px">{{ $review->comment }}</td>
                            <td>
                                @if($review->status === 'approved')
                                    <span class="badge badge-success">Disetujui</span>
                                @elseif($review->status === 'rejected')
                                    <span class="badge badge-danger">Ditolak</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($review->status === 'pending')
                                    <form action="{{ route('reviews.approve', $review) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-success" type="submit">Approve</button>
                                    </form>
                                    <form action="{{ route('reviews.reject', $review) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-danger" type="submit">Reject</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada ulasan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $reviews->links() }}</div>
    </div>
@stop

