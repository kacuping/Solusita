@extends('adminlte::page')

@section('title', 'Ubah Petugas')

@section('content_header')
    <h1>Ubah Petugas</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('cleaners.update', $cleaner) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="full_name" value="{{ old('full_name', $cleaner->full_name) }}" class="form-control @error('full_name') is-invalid @enderror" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <input type="text" name="address" value="{{ old('address', $cleaner->address) }}" class="form-control @error('address') is-invalid @enderror">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Nomor HP/WA</label>
                            <input type="text" name="phone" value="{{ old('phone', $cleaner->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Tempat Lahir</label>
                            <input type="text" name="birth_place" value="{{ old('birth_place', $cleaner->birth_place) }}" class="form-control @error('birth_place') is-invalid @enderror">
                            @error('birth_place')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Tanggal Lahir</label>
                            <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', optional($cleaner->birth_date)->format('Y-m-d')) }}" class="form-control @error('birth_date') is-invalid @enderror">
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Umur (otomatis)</label>
                            <input type="text" id="age" class="form-control" value="{{ $cleaner->age ? $cleaner->age . ' th' : '' }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Bank</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name', $cleaner->bank_name) }}" class="form-control @error('bank_name') is-invalid @enderror">
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Nomor Rekening</label>
                            <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $cleaner->bank_account_number) }}" class="form-control @error('bank_account_number') is-invalid @enderror">
                            @error('bank_account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Nama pada Rekening</label>
                            <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $cleaner->bank_account_name) }}" class="form-control @error('bank_account_name') is-invalid @enderror">
                            @error('bank_account_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('cleaners.index') }}" class="btn btn-secondary mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        function calculateAge(dateStr) {
            if (!dateStr) return '';
            const today = new Date();
            const birthDate = new Date(dateStr);
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            return age + ' th';
        }

        const birthInput = document.getElementById('birth_date');
        const ageInput = document.getElementById('age');
        function updateAge() {
            ageInput.value = calculateAge(birthInput.value);
        }
        birthInput.addEventListener('change', updateAge);
        window.addEventListener('DOMContentLoaded', updateAge);
    </script>
@stop

