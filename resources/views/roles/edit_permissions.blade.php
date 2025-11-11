@extends('adminlte::page')

@section('title', 'Kelola Izin Role')

@section('content_header')
    <h1>Kelola Izin: {{ $role->name }}</h1>
@endsection

@section('content')
    <form method="POST" action="{{ route('roles.permissions.update', $role) }}">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">Daftar Izin</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Nama Izin</th>
                            <th class="text-center">Diizinkan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $permission)
                            <tr>
                                <td><code>{{ $permission->key }}</code></td>
                                <td>{{ $permission->name }}</td>
                                <td class="text-center">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" {{ in_array($permission->id, $assigned) ? 'checked' : '' }}>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-right">
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </form>
@endsection

