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
            <form method="POST" action="{{ route('service-categories.store') }}" class="mb-4">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="name">Nama</label>
                        <input type="text" id="name" name="name" class="form-control" required placeholder="Contoh: General">
                    </div>
                    @php($icons = ['fa-broom','fa-couch','fa-shower','fa-utensils','fa-spray-can','fa-brush','fa-soap','fa-wind','fa-snowflake'])
                    <style>
                        .icon-picker{display:flex;flex-wrap:wrap;gap:8px}
                        .icon-option{border:1px solid #ddd;border-radius:6px;background:#fff;padding:6px 8px;cursor:pointer}
                        .icon-option.active{border-color:#2a57c4;box-shadow:0 0 0 2px rgba(42,87,196,.15)}
                    </style>
                    <div class="form-group col-md-3">
                        <label for="icon">Icon</label>
                        <input type="hidden" id="icon" name="icon" value="">
                        <div class="icon-picker" data-target="icon">
                            <button type="button" class="icon-option" data-value="">
                                <span>Default</span>
                            </button>
                            @foreach($icons as $ic)
                                <button type="button" class="icon-option" data-value="{{ $ic }}">
                                    <i class="fa {{ $ic }}" style="font-size:18px"></i>
                                </button>
                            @endforeach
                        </div>
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
                            <th>Icon</th>
                            <th>Aktif</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $index => $cat)
                            <tr>
                                <td>{{ ($categories->currentPage() - 1) * $categories->perPage() + $index + 1 }}</td>
                                <td>
                                    <form method="POST" action="{{ route('service-categories.update', $cat) }}" class="form-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" value="{{ $cat->name }}" class="form-control form-control-sm" style="min-width:200px;">
                                </td>
                                <td style="width:180px">
                                    <div class="d-flex align-items-center">
                                        <i class="fa {{ $cat->icon ?? 'fa-broom' }} mr-2" style="font-size:18px"></i>
                                        <select name="icon" class="form-control form-control-sm">
                                            <option value="" {{ empty($cat->icon) ? 'selected' : '' }}>Default</option>
                                            @foreach(['fa-broom','fa-couch','fa-shower','fa-utensils','fa-spray-can','fa-brush','fa-soap','fa-wind','fa-snowflake'] as $opt)
                                                <option value="{{ $opt }}" {{ ($cat->icon ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                            @endforeach
                                        </select>
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

            {{ $categories->links() }}
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('.icon-picker').forEach(function(p){
                var target = p.getAttribute('data-target');
                var input = document.getElementById(target);
                p.querySelectorAll('.icon-option').forEach(function(b){
                    b.addEventListener('click', function(){
                        p.querySelectorAll('.icon-option').forEach(function(x){ x.classList.remove('active'); });
                        b.classList.add('active');
                        if (input) input.value = b.getAttribute('data-value');
                    });
                });
            });
        });
    </script>
@endsection
