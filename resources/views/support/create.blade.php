@extends('adminlte::page')

@section('title', 'Buat Tiket')

@section('content_header')
    <h1>Buat Tiket Dukungan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('support.store') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="customer_id">ID Pelanggan</label>
                        <input type="number" name="customer_id" id="customer_id" class="form-control" value="{{ old('customer_id') }}" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="booking_id">ID Booking (opsional)</label>
                        <input type="number" name="booking_id" id="booking_id" class="form-control" value="{{ old('booking_id') }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="cleaner_id">Assign ke Petugas (opsional)</label>
                        <select name="cleaner_id" id="cleaner_id" class="form-control">
                            <option value="">-- Tidak ada --</option>
                            @foreach($cleaners as $c)
                                <option value="{{ $c->id }}" {{ old('cleaner_id')==$c->id ? 'selected' : '' }}>{{ $c->full_name ?? $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="subject">Subject</label>
                        <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject') }}" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="priority">Prioritas</label>
                        <select name="priority" id="priority" class="form-control">
                            <option value="low" {{ old('priority')==='low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority','medium')==='medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority')==='high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="message">Pesan</label>
                    <textarea name="message" id="message" rows="4" class="form-control" required>{{ old('message') }}</textarea>
                </div>

                <div class="text-right">
                    <a href="{{ route('support.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@stop

