@extends('adminlte::page')

@section('title', 'Layanan')

@section('content_header')
    <h1>Manajemen Layanan</h1>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if (isset($distinctCategoryCount) && $distinctCategoryCount < 3)
        <div class="alert alert-warning">
            <strong>Perhatian:</strong> Minimal harus ada <strong>3 kategori layanan aktif</strong> agar halaman pelanggan
            dapat menampilkan grid layanan dengan baik.
            Tambahkan layanan dengan kategori berbeda hingga mencapai minimal 3.
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('services.index') }}" class="mb-3">
                <div class="input-group">
                    <input type="text" name="q" value="{{ $search ?? '' }}" class="form-control"
                        placeholder="Cari layanan">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </div>
                </div>
            </form>

            <h5 class="mb-2">Tambah Layanan</h5>
            <form method="POST" action="{{ route('services.store') }}" class="mb-4">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="category">Kategori <span class="text-danger">*</span></label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="">Pilih kategori</option>
                            @foreach ($categoryOptions ?? [] as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="name">Nama</label>
                        <input type="text" id="name" name="name" class="form-control" required
                            placeholder="Contoh: General Cleaning">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="unit_type">Satuan/QTY</label>
                        <select id="unit_type" name="unit_type" class="form-control" required>
                            <option value="M2">M2</option>
                            <option value="Buah/Seater">Buah/Seater</option>
                            <option value="Durasi">Durasi</option>
                            <option value="Satuan" selected>Satuan</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="base_price">Harga Dasar</label>
                        <input type="number" id="base_price" name="base_price" class="form-control" min="0"
                            step="1000" placeholder="Contoh: 150000" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="duration_hours">Durasi (jam)</label>
                        <input type="number" id="duration_hours" name="duration_hours" class="form-control" min="1"
                            step="1" placeholder="Contoh: 2" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="active">Aktif</label>
                        <select id="active" name="active" class="form-control">
                            <option value="1" selected>Ya</option>
                            <option value="0">Tidak</option>
                        </select>
                    </div>
                    @php($icons = ['fa-broom', 'fa-couch', 'fa-shower', 'fa-utensils', 'fa-spray-can', 'fa-brush', 'fa-soap', 'fa-wind', 'fa-snowflake'])
                    <style>
                        .icon-picker {
                            display: flex;
                            flex-wrap: wrap;
                            gap: 8px
                        }

                        .icon-option {
                            border: 1px solid #ddd;
                            border-radius: 6px;
                            background: #fff;
                            padding: 6px 8px;
                            cursor: pointer
                        }

                        .icon-option.active {
                            border-color: #2a57c4;
                            box-shadow: 0 0 0 2px rgba(42, 87, 196, .15)
                        }
                    </style>
                    <div class="form-group col-md-3">
                        <label for="icon">Icon</label>
                        <input type="hidden" id="icon" name="icon" value="">
                        <div class="icon-picker" data-target="icon">
                            <button type="button" class="icon-option" data-value="">
                                <span>Default</span>
                            </button>
                            @foreach ($icons as $ic)
                                <button type="button" class="icon-option" data-value="{{ $ic }}">
                                    <i class="fa {{ $ic }}" style="font-size:18px"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="description">Deskripsi</label>
                        <input type="text" id="description" name="description" class="form-control"
                            placeholder="Deskripsi singkat layanan">
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
                            <th>Kategori</th>
                            <th>Satuan/QTY</th>
                            <th>Harga Dasar</th>
                            <th>Durasi (jam)</th>
                            <th>Icon</th>
                            <th>Aktif</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $index => $service)
                            <tr>
                                <td>{{ ($services->currentPage() - 1) * $services->perPage() + $index + 1 }}</td>
                                <td>
                                    <form method="POST" action="{{ route('services.update', $service) }}"
                                        class="form-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" value="{{ $service->name }}"
                                            class="form-control form-control-sm" style="min-width:180px;">
                                </td>
                                <td style="width:200px">
                                    <select name="category" class="form-control form-control-sm">
                                        @foreach ($categoryOptions ?? [] as $cat)
                                            <option value="{{ $cat->name }}"
                                                {{ $service->category === $cat->name ? 'selected' : '' }}>
                                                {{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width:160px">
                                    <select name="unit_type" class="form-control form-control-sm">
                                        @foreach ($unitTypes ?? ['M2', 'Buah/Seater', 'Durasi', 'Satuan'] as $ut)
                                            <option value="{{ $ut }}"
                                                {{ ($service->unit_type ?? 'Satuan') === $ut ? 'selected' : '' }}>
                                                {{ $ut }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width:150px">
                                    <input type="number" name="base_price" value="{{ $service->base_price }}"
                                        class="form-control form-control-sm" min="0" step="1000">
                                </td>
                                <td style="width:130px">
                                    <input type="number" name="duration_hours"
                                        value="{{ max((int) ceil(($service->duration_minutes ?? 0) / 60), 0) }}"
                                        class="form-control form-control-sm" min="1" step="1">
                                </td>
                                <td style="width:200px">
                                    <select name="icon" class="form-control form-control-sm">
                                        <option value="" {{ empty($service->icon) ? 'selected' : '' }}>Default
                                        </option>
                                        @foreach ($icons ?? ['fa-broom', 'fa-couch', 'fa-shower', 'fa-utensils', 'fa-spray-can', 'fa-brush', 'fa-soap', 'fa-wind', 'fa-snowflake'] as $ic)
                                            <option value="{{ $ic }}"
                                                {{ ($service->icon ?? '') === $ic ? 'selected' : '' }}>{{ $ic }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width:110px">
                                    <select name="active" class="form-control form-control-sm">
                                        <option value="1" {{ $service->active ? 'selected' : '' }}>Ya</option>
                                        <option value="0" {{ !$service->active ? 'selected' : '' }}>Tidak</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="description" value="{{ $service->description }}"
                                        class="form-control form-control-sm" style="min-width:260px;">
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" type="submit" title="Simpan">
                                        <i class="fas fa-save"></i>
                                    </button>
                                    </form>
                                    <form method="POST" action="{{ route('services.destroy', $service) }}"
                                        style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" title="Hapus"
                                            onclick="return confirm('Hapus layanan ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada layanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="admin-services-pagination d-flex justify-content-center">
                {{ $services->links('vendor.pagination.adminlte-sm') }}
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        Perubahan pada daftar layanan akan otomatis tampil di dashboard pelanggan (/customer/home) pada bagian "Layanan".
    </div>
    <script>
        function bindUnitTypeDisable() {
            var createUnit = document.getElementById('unit_type');
            var createDuration = document.getElementById('duration_hours');
            var toggle = function(sel, dur) {
                dur.disabled = sel.value !== 'Durasi';
            };
            if (createUnit && createDuration) {
                toggle(createUnit, createDuration);
                createUnit.addEventListener('change', function() {
                    toggle(createUnit, createDuration);
                });
            }
            document.querySelectorAll('select[name="unit_type"]').forEach(function(s) {
                s.addEventListener('change', function() {
                    var row = s.closest('tr');
                    var dur = row ? row.querySelector('input[name="duration_hours"]') : null;
                    if (dur) dur.disabled = s.value !== 'Durasi';
                });
                var row = s.closest('tr');
                var dur = row ? row.querySelector('input[name="duration_hours"]') : null;
                if (dur) dur.disabled = s.value !== 'Durasi';
            });
        }

        function setIcon(btn) {
            var cell = btn.closest('td');
            var input = cell.querySelector('input[name="icon"]');
            cell.querySelectorAll('.icon-option').forEach(function(b) {
                b.classList.remove('active');
            });
            btn.classList.add('active');
            if (input) input.value = btn.getAttribute('data-value');
        }
        document.addEventListener('DOMContentLoaded', function() {
            bindUnitTypeDisable();
            document.querySelectorAll('.icon-picker').forEach(function(p) {
                var target = p.getAttribute('data-target');
                var input = document.getElementById(target);
                p.querySelectorAll('.icon-option').forEach(function(b) {
                    b.addEventListener('click', function() {
                        p.querySelectorAll('.icon-option').forEach(function(x) {
                            x.classList.remove('active');
                        });
                        b.classList.add('active');
                        if (input) input.value = b.getAttribute('data-value');
                    });
                });
            });
        });
    </script>
@endsection
