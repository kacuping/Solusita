@extends('adminlte::page')

@section('title', 'Kategori Layanan')

@section('content_header')
    <h1>Manajemen Kategori Layanan</h1>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('service-categories.index') }}" class="mb-3">
                <div class="input-group">
                    <input type="text" name="q" value="{{ $search ?? '' }}" class="form-control" placeholder="Cari kategori">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </div>
                </div>
            </form>

            <h5 class="mb-2">Tambah Kategori</h5>
            <form method="POST" action="{{ route('service-categories.store') }}" class="mb-4" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="name">Nama</label>
                        <input type="text" id="name" name="name" class="form-control" required placeholder="Contoh: General">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="image">Foto/Gambar</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="active">Aktif</label>
                        <select id="active" name="active" class="form-control">
                            <option value="1" selected>Ya</option>
                            <option value="0">Tidak</option>
                        </select>
                    </div>
                </div>
                <button class="btn btn-success">Simpan</button>
            </form>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Gambar</th>
                            <th>Aktif</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $index => $cat)
                            <tr>
                                <td>{{ ($categories->currentPage() - 1) * $categories->perPage() + $index + 1 }}</td>
                                <td>
                                    <form method="POST" action="{{ route('service-categories.update', $cat) }}" class="form-inline" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" value="{{ $cat->name }}" class="form-control form-control-sm" style="min-width:200px;">
                                </td>
                                <td style="width:220px">
                                    <div class="d-flex align-items-center">
                                        @if(!empty($cat->image))
                                            <img src="{{ Route::has('service-categories.image') ? route('service-categories.image', $cat) : Storage::url($cat->image) }}" alt="{{ $cat->name }}" style="width:48px;height:48px;object-fit:cover;border-radius:8px;margin-right:8px;">
                                            @else
                                                <div style="width:48px;height:48px;border-radius:8px;background:#eef3ff;color:#2a57c4;display:flex;align-items:center;justify-content:center;margin-right:8px;">
                                                    <i class="fa {{ $cat->icon ?? 'fa-broom' }}" style="font-size:18px"></i>
                                                </div>
                                            @endif
                                        <input type="file" name="image" accept="image/*" class="form-control form-control-sm" style="max-width:150px;">
                                    </div>
                                </td>
                                <td style="width:120px">
                                    <select name="active" class="form-control form-control-sm">
                                        <option value="1" {{ $cat->active ? 'selected' : '' }}>Ya</option>
                                        <option value="0" {{ !$cat->active ? 'selected' : '' }}>Tidak</option>
                                    </select>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" type="submit">Simpan</button>
                                    </form>
                                    <form method="POST" action="{{ route('service-categories.destroy', $cat) }}" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus kategori ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $categories->links('vendor.pagination.adminlte-sm') }}
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
        });
    </script>
@endsection
