@extends('adminlte::page')

@section('title', 'Buat PopUp')

@section('content_header')
    <h1>Buat PopUp</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('popups.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Judul</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label>Status Popup</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="enabled" name="enabled" value="1" {{ old('enabled') ? 'checked' : '' }}>
                            <label class="form-check-label" for="enabled">Enabled</label>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Maks tampil per hari</label>
                        <input type="number" name="max_per_day" class="form-control @error('max_per_day') is-invalid @enderror" value="{{ old('max_per_day', 1) }}" min="1">
                        @error('max_per_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Jam tampil (pisahkan koma)</label>
                        <input type="text" name="hours" class="form-control @error('hours') is-invalid @enderror" value="{{ old('hours') }}" placeholder="Contoh: 09,12,18 atau 09:00,12:30,18:00">
                        @error('hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label>Mulai</label>
                        <input type="datetime-local" name="starts_at" class="form-control @error('starts_at') is-invalid @enderror" value="{{ old('starts_at') }}">
                        @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label>Berakhir</label>
                        <input type="datetime-local" name="ends_at" class="form-control @error('ends_at') is-invalid @enderror" value="{{ old('ends_at') }}">
                        @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Gambar</label>
                        <input type="file" name="image" class="form-control-file @error('image') is-invalid @enderror" accept="image/*">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label>Aktif</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="active" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Aktif</label>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <a href="{{ route('popups.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@stop

