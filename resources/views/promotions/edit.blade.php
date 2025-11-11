@extends('adminlte::page')

@section('title', 'Edit Promo')

@section('content_header')
    <h1>Edit Promo</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('promotions.update', $promotion) }}">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="code">Kode</label>
                        <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $promotion->code) }}" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-8">
                        <label for="title">Judul</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $promotion->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $promotion->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="discount_type">Tipe Diskon</label>
                        <select name="discount_type" id="discount_type" class="form-control @error('discount_type') is-invalid @enderror" required>
                            <option value="percent" {{ old('discount_type', $promotion->discount_type)==='percent' ? 'selected' : '' }}>Persen (%)</option>
                            <option value="amount" {{ old('discount_type', $promotion->discount_type)==='amount' ? 'selected' : '' }}>Nominal</option>
                        </select>
                        @error('discount_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="discount_value">Nilai Diskon</label>
                        <input type="number" step="0.01" name="discount_value" id="discount_value" class="form-control @error('discount_value') is-invalid @enderror" value="{{ old('discount_value', $promotion->discount_value) }}" required>
                        @error('discount_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="active">Status</label>
                        <select name="active" id="active" class="form-control @error('active') is-invalid @enderror" required>
                            <option value="1" {{ old('active', $promotion->active) ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ !old('active', $promotion->active) ? 'selected' : '' }}>Non-aktif</option>
                        </select>
                        @error('active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="starts_at">Mulai</label>
                        <input type="datetime-local" name="starts_at" id="starts_at" class="form-control @error('starts_at') is-invalid @enderror" value="{{ old('starts_at', optional($promotion->starts_at)->format('Y-m-d\TH:i')) }}">
                        @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ends_at">Berakhir</label>
                        <input type="datetime-local" name="ends_at" id="ends_at" class="form-control @error('ends_at') is-invalid @enderror" value="{{ old('ends_at', optional($promotion->ends_at)->format('Y-m-d\TH:i')) }}">
                        @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="usage_limit">Kuota Penggunaan</label>
                        <input type="number" name="usage_limit" id="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror" value="{{ old('usage_limit', $promotion->usage_limit) }}" placeholder="Kosongkan jika tanpa batas">
                        @error('usage_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label>Segmentasi Pelanggan (Sederhana)</label>
                        @php
                            $sr = is_array($promotion->segment_rules) ? $promotion->segment_rules : [];
                        @endphp
                        <div class="border p-2 rounded">
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="sr_new_customer" name="sr_new_customer" value="1" {{ old('sr_new_customer', ($sr['new_customer'] ?? false) ? 1 : 0) ? 'checked' : '' }}>
                                <label for="sr_new_customer" class="form-check-label">Pelanggan baru (belum pernah memesan)</label>
                            </div>
                            <div class="form-row align-items-center mb-2">
                                <div class="col-auto">
                                    <label class="mb-0">Umur akun</label>
                                </div>
                                <div class="col-auto">
                                    @php $ageMode = old('sr_age_mode', isset($sr['max_days_since_registration']) ? 'max' : (isset($sr['min_days_since_registration']) ? 'min' : 'max')); @endphp
                                    <select name="sr_age_mode" class="form-control form-control-sm">
                                        <option value="max" {{ $ageMode==='max' ? 'selected' : '' }}>≤</option>
                                        <option value="min" {{ $ageMode==='min' ? 'selected' : '' }}>≥</option>
                                    </select>
                                </div>
                                <div class="col">
                                    @php $ageDays = old('sr_age_days', $sr['max_days_since_registration'] ?? ($sr['min_days_since_registration'] ?? '')); @endphp
                                    <input type="number" name="sr_age_days" class="form-control form-control-sm" placeholder="hari" value="{{ $ageDays }}">
                                </div>
                            </div>
                            <div class="form-row align-items-center">
                                <div class="col-auto">
                                    <label class="mb-0">Minimal pesanan selesai</label>
                                </div>
                                <div class="col">
                                    <input type="number" name="sr_min_past_bookings" class="form-control form-control-sm" placeholder="jumlah" value="{{ old('sr_min_past_bookings', $sr['min_past_bookings'] ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1">Gunakan builder di atas untuk segmentasi umum tanpa perlu JSON.</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="segment_rules">Aturan Segmentasi (Lanjutan, JSON – opsional)</label>
                    <textarea name="segment_rules" id="segment_rules" class="form-control @error('segment_rules') is-invalid @enderror" rows="3">{{ old('segment_rules', $promotion->segment_rules ? json_encode($promotion->segment_rules) : '') }}</textarea>
                    @error('segment_rules')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Biarkan kosong jika menggunakan builder sederhana di atas.</small>
                </div>

                <div class="text-right">
                    <a href="{{ route('promotions.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@stop
