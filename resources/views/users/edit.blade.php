@extends('adminlte::page')

@section('title', 'Edit User')

@section('content_header')
    <h1>Edit User</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                    @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                    @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="password">Password (isi jika ingin mengganti)</label>
                    <input type="password" id="password" name="password" class="form-control">
                    @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" class="form-control" required>
                        @foreach ($roles as $key => $label)
                            <option value="{{ $key }}" @if(old('role', $user->role)===$key) selected @endif>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
@stop

