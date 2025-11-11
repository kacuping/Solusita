@extends('adminlte::page')

@section('title', 'User')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Daftar User</h1>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah User
        </a>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Verifikasi</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge badge-info">{{ $user->role }}</span></td>
                            <td>
                                @if ($user->email_verified_at)
                                    <span class="badge badge-success">Terverifikasi</span>
                                @else
                                    <span class="badge badge-secondary">Belum</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @can('users.manage')
                                    <a href="{{ route('users.permissions.edit', $user) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-user-shield"></i>
                                    </a>
                                @endcan
                                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Hapus user ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $users->links() }}
        </div>
    </div>
@stop
