@extends('adminlte::page')

@section('title', 'Promosi')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Promosi</h1>
        <a href="{{ route('promotions.create') }}" class="btn btn-primary">Buat Promo</a>
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
                        <th>Kode</th>
                        <th>Judul</th>
                        <th>Diskon</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th>Kuota</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promotions as $promo)
                        <tr>
                            <td><code>{{ $promo->code }}</code></td>
                            <td>{{ $promo->title }}</td>
                            <td>
                                @if($promo->discount_type === 'percent')
                                    {{ number_format($promo->discount_value, 0) }}%
                                @else
                                    Rp {{ number_format($promo->discount_value, 0, ',', '.') }}
                                @endif
                            </td>
                            <td>
                                @if($promo->starts_at)
                                    {{ $promo->starts_at->format('d/m/Y H:i') }}
                                @else
                                    -
                                @endif
                                s/d
                                @if($promo->ends_at)
                                    {{ $promo->ends_at->format('d/m/Y H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($promo->active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Non-aktif</span>
                                @endif
                            </td>
                            <td>
                                @if($promo->usage_limit)
                                    {{ $promo->used_count }} / {{ $promo->usage_limit }}
                                @else
                                    {{ $promo->used_count }} / ∞
                                @endif
                            </td>
                            <td class="text-right">
                                <a class="btn btn-sm btn-warning" href="{{ route('promotions.edit', $promo) }}">Edit</a>
                                <form action="{{ route('promotions.destroy', $promo) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus promo ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada promo.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $promotions->links() }}
        </div>
    </div>
@stop

