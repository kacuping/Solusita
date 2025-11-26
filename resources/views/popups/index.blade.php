@extends('adminlte::page')

@section('title', 'PopUp')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>PopUp</h1>
        <a href="{{ route('popups.create') }}" class="btn btn-primary">Buat PopUp</a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Status</th>
                        <th>Aktif</th>
                        <th>Max/Hari</th>
                        <th>Jam</th>
                        <th>Periode</th>
                        <th>Gambar</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($popups as $p)
                        <tr>
                            <td>{{ $p->title }}</td>
                            <td>{!! $p->enabled ? '<span class="badge badge-success">Enabled</span>' : '<span class="badge badge-secondary">Disabled</span>' !!}</td>
                            <td>{!! $p->active ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Non-aktif</span>' !!}</td>
                            <td>{{ (int) ($p->max_per_day ?? 0) }}</td>
                            <td>{{ is_array($p->hours) ? implode(', ', $p->hours) : '—' }}</td>
                            <td>
                                {{ $p->starts_at ? $p->starts_at->format('d/m/Y H:i') : '-' }} s/d {{ $p->ends_at ? $p->ends_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td style="width:120px">
                                @if($p->image_path)
                                    <img src="/{{ $p->image_path }}" alt="{{ $p->title }}" style="max-height:60px; max-width:110px; object-fit:contain; border-radius:6px;">
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-right">
                                <a class="btn btn-sm btn-info" href="{{ route('popups.edit', $p) }}">Edit</a>
                                <form action="{{ route('popups.force', $p) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-primary" type="submit" title="Tampilkan sekarang">Tampilkan Sekarang</button>
                                </form>
                                <form action="{{ route('popups.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus popup ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada popup.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $popups->links() }}</div>
    </div>
@stop
