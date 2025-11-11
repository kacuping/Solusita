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
            (/customer/home) dapat menampilkan grid layanan dengan baik.
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
                        <label for="name">Nama</label>
                        <input type="text" id="name" name="name" class="form-control" required
                            placeholder="Contoh: General Cleaning">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="base_price">Harga Dasar</label>
                        <input type="number" id="base_price" name="base_price" class="form-control" min="0"
                            step="1000" placeholder="Contoh: 150000" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="duration_minutes">Durasi (menit)</label>
                        <input type="number" id="duration_minutes" name="duration_minutes" class="form-control"
                            min="0" step="15" placeholder="Contoh: 60" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="category">Kategori <span class="text-danger">*</span></label>
                        <input type="text" id="category" name="category" class="form-control"
                            placeholder="Contoh: General, Karpet, Sofa" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="icon">Icon (Preset)</label>
                        <select id="icon" name="icon" class="form-control">
                            <option value="">Pilih ikon</option>
                            <option value="fa-broom">fa-broom (General)</option>
                            <option value="fa-rug">fa-rug (Karpet)</option>
                            <option value="fa-couch">fa-couch (Sofa)</option>
                            <option value="fa-fan">fa-fan (AC)</option>
                            <option value="fa-shower">fa-shower (Kamar Mandi)</option>
                            <option value="fa-utensils">fa-utensils (Dapur)</option>
                            <option value="fa-vacuum">fa-vacuum (Vacuum)</option>
                            <option value="fa-spray-can">fa-spray-can (Spray)</option>
                            <option value="fa-bucket">fa-bucket (Bucket)</option>
                            <option value="fa-mop">fa-mop (Mop)</option>
                        </select>
                        <small class="text-muted">Preset ikon Font Awesome. Kosongkan untuk default sesuai kategori.</small>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="active">Aktif</label>
                        <select id="active" name="active" class="form-control">
                            <option value="1" selected>Ya</option>
                            <option value="0">Tidak</option>
                        </select>
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
                            <th>Harga Dasar</th>
                            <th>Durasi (menit)</th>
                            <th>Kategori</th>
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
                                <td style="width:150px">
                                    <input type="number" name="base_price" value="{{ $service->base_price }}"
                                        class="form-control form-control-sm" min="0" step="1000">
                                </td>
                                <td style="width:130px">
                                    <input type="number" name="duration_minutes"
                                        value="{{ $service->duration_minutes }}" class="form-control form-control-sm"
                                        min="0" step="15">
                                </td>
                                <td style="width:160px">
                                    <input type="text" name="category" value="{{ $service->category }}"
                                        class="form-control form-control-sm">
                                </td>
                                <td style="width:180px">
                                    <div class="d-flex align-items-center">
                                        <i class="fa {{ $service->icon ?? 'fa-broom' }} mr-2" style="font-size:18px"></i>
                                        <select name="icon" class="form-control form-control-sm">
                                            <option value="" {{ empty($service->icon) ? 'selected' : '' }}>Default
                                            </option>
                                            <option value="fa-broom"
                                                {{ $service->icon === 'fa-broom' ? 'selected' : '' }}>fa-broom</option>
                                            <option value="fa-rug" {{ $service->icon === 'fa-rug' ? 'selected' : '' }}>
                                                fa-rug</option>
                                            <option value="fa-couch"
                                                {{ $service->icon === 'fa-couch' ? 'selected' : '' }}>fa-couch</option>
                                            <option value="fa-fan" {{ $service->icon === 'fa-fan' ? 'selected' : '' }}>
                                                fa-fan</option>
                                            <option value="fa-shower"
                                                {{ $service->icon === 'fa-shower' ? 'selected' : '' }}>fa-shower</option>
                                            <option value="fa-utensils"
                                                {{ $service->icon === 'fa-utensils' ? 'selected' : '' }}>fa-utensils
                                            </option>
                                            <option value="fa-vacuum"
                                                {{ $service->icon === 'fa-vacuum' ? 'selected' : '' }}>fa-vacuum</option>
                                            <option value="fa-spray-can"
                                                {{ $service->icon === 'fa-spray-can' ? 'selected' : '' }}>fa-spray-can
                                            </option>
                                            <option value="fa-bucket"
                                                {{ $service->icon === 'fa-bucket' ? 'selected' : '' }}>fa-bucket</option>
                                            <option value="fa-mop" {{ $service->icon === 'fa-mop' ? 'selected' : '' }}>
                                                fa-mop</option>
                                        </select>
                                    </div>
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
                                    <button class="btn btn-primary btn-sm" type="submit">Simpan</button>
                                    </form>
                                    <form method="POST" action="{{ route('services.destroy', $service) }}"
                                        style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('Hapus layanan ini?')">Hapus</button>
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

            {{ $services->links() }}
        </div>
    </div>

    <div class="alert alert-info">
        Perubahan pada daftar layanan akan otomatis tampil di dashboard pelanggan (/customer/home) pada bagian "Layanan".
    </div>
@endsection
