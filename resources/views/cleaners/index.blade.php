@extends('adminlte::page')

@section('title', 'Petugas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Daftar Petugas</h1>
        @can('cleaners.manage')
            <a href="{{ route('cleaners.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Petugas
            </a>
        @endcan
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
                        <th>Foto</th>
                        <th>Nama Lengkap</th>
                        <th>Alamat</th>
                        <th>Nomor HP/WA</th>
                        <th>Tempat, Tanggal Lahir</th>
                        <th>Umur</th>
                        <th>Status</th>
                        <th>Bank</th>
                        <th>No. Rekening</th>
                        <th>Nama Rekening</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cleaners as $cleaner)
                        <tr>
                            <td>
                                @php($photo = ($photos[(string) $cleaner->id] ?? null))
                                @if($photo)
                                    <img src="{{ $photo }}" alt="Foto" style="width:48px; height:48px; object-fit:cover; border-radius:6px;">
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $cleaner->full_name ?? $cleaner->name }}</td>
                            <td>{{ $cleaner->address }}</td>
                            <td>{{ $cleaner->phone }}</td>
                            <td>{{ $cleaner->birth_place }}{{ $cleaner->birth_place && $cleaner->birth_date ? ', ' : '' }}{{ optional($cleaner->birth_date)->format('d M Y') }}</td>
                            <td>
                                @if ($cleaner->age !== null)
                                    {{ $cleaner->age }} th
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $cleaner->status === 'approved' ? 'success' : ($cleaner->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($cleaner->status ?? 'pending') }}
                                </span>
                                @if(!$cleaner->active)
                                    <span class="badge badge-secondary">Non-aktif</span>
                                @endif
                            </td>
                            <td>{{ $cleaner->bank_name }}</td>
                            <td>{{ $cleaner->bank_account_number }}</td>
                            <td>{{ $cleaner->bank_account_name }}</td>
                            <td class="text-right">
                                @can('cleaners.manage')
                                    @if(($cleaner->status ?? 'pending') === 'pending')
                                        <form action="{{ route('cleaners.approve', $cleaner) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('cleaners.reject', $cleaner) }}" method="POST" class="d-inline" onsubmit="return confirm('Reject petugas ini?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('cleaners.edit', $cleaner) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('cleaners.destroy', $cleaner) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Hapus petugas ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Belum ada data petugas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $cleaners->links() }}
        </div>
    </div>
@stop
