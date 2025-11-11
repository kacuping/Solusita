@extends('adminlte::page')

@section('title', 'Approval User')

@section('content_header')
    <h1>Approval User</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Daftar User Belum Terverifikasi</span>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Dibuat Oleh</th>
                        <th>Dibuat Pada</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingUsers as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ optional($user->creator)->name ?? '-' }}</td>
                            <td>{{ $user->created_at?->format('d M Y H:i') }}</td>
                            <td class="text-right">
                                @can('approvals.manage')
                                    <form action="{{ route('approvals.users.approve', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Tidak ada user yang menunggu approval.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $pendingUsers->links() }}
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Daftar Petugas Menunggu Approval</span>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Nomor HP/WA</th>
                        <th>Status</th>
                        <th>Dibuat Pada</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingCleaners as $cleaner)
                        <tr>
                            <td>{{ $cleaner->full_name ?? $cleaner->name }}</td>
                            <td>{{ $cleaner->phone ?? '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $cleaner->status === 'approved' ? 'success' : ($cleaner->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($cleaner->status ?? 'pending') }}
                                </span>
                                @if(!$cleaner->active)
                                    <span class="badge badge-secondary">Non-aktif</span>
                                @endif
                            </td>
                            <td>{{ $cleaner->created_at?->format('d M Y H:i') }}</td>
                            <td class="text-right">
                                @can('approvals.manage')
                                    <form action="{{ route('approvals.cleaners.approve', $cleaner) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada petugas yang menunggu approval.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $pendingCleaners->links() }}
        </div>
    </div>
@stop
