@extends('adminlte::page')

@section('title', 'Buat Promo')

@section('content_header')
    <h1>Buat Promo</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('promotions.store') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="code">Kode</label>
                        <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-8">
                        <label for="title">Judul</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="discount_type">Tipe Diskon</label>
                        <select name="discount_type" id="discount_type" class="form-control @error('discount_type') is-invalid @enderror" required>
                            <option value="percent" {{ old('discount_type')==='percent' ? 'selected' : '' }}>Persen (%)</option>
                            <option value="amount" {{ old('discount_type')==='amount' ? 'selected' : '' }}>Nominal</option>
                        </select>
                        @error('discount_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="discount_value">Nilai Diskon</label>
                        <input type="number" step="0.01" name="discount_value" id="discount_value" class="form-control @error('discount_value') is-invalid @enderror" value="{{ old('discount_value', 0) }}" required>
                        @error('discount_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="active">Status</label>
                        <select name="active" id="active" class="form-control @error('active') is-invalid @enderror" required>
                            <option value="1" {{ old('active', '1')==='1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('active')==='0' ? 'selected' : '' }}>Non-aktif</option>
                        </select>
                        @error('active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="starts_at">Mulai</label>
                        <input type="datetime-local" name="starts_at" id="starts_at" class="form-control @error('starts_at') is-invalid @enderror" value="{{ old('starts_at') }}">
                        @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ends_at">Berakhir</label>
                        <input type="datetime-local" name="ends_at" id="ends_at" class="form-control @error('ends_at') is-invalid @enderror" value="{{ old('ends_at') }}">
                        @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="usage_limit">Kuota Penggunaan</label>
                        <input type="number" name="usage_limit" id="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror" value="{{ old('usage_limit') }}" placeholder="Kosongkan jika tanpa batas">
                        @error('usage_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="segment_rules">Aturan Segmentasi Pelanggan (JSON)</label>
                        <textarea name="segment_rules" id="segment_rules" class="form-control @error('segment_rules') is-invalid @enderror" rows="3" placeholder='Contoh: {"new_customer_only": true, "min_orders": 3}'>{{ old('segment_rules') ? json_encode(old('segment_rules')) : '' }}</textarea>
                        @error('segment_rules')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Masukkan JSON untuk membatasi pelanggan yang berhak menggunakan promo.</small>
                    </div>
                </div>

                <div class="text-right">
                    <a href="{{ route('promotions.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@stop

