@extends('adminlte::page')

@section('title', 'Override Izin Pengguna')

@section('content_header')
    <h1>Override Izin: {{ $user->name }}</h1>
@endsection

@section('content')
    <form method="POST" action="{{ route('users.permissions.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">Pengaturan Override</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Nama Izin</th>
                            <th class="text-center">Inherit</th>
                            <th class="text-center">Allow</th>
                            <th class="text-center">Deny</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $permission)
                            @php
                                $mode = 'inherit';
                                if (array_key_exists($permission->id, $overrides)) {
                                    $mode = $overrides[$permission->id] ? 'allow' : 'deny';
                                }
                            @endphp
                            <tr>
                                <td><code>{{ $permission->key }}</code></td>
                                <td>{{ $permission->name }}</td>
                                <td class="text-center">
                                    <input type="radio" name="permissions[{{ $permission->id }}]" value="inherit" {{ $mode === 'inherit' ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input type="radio" name="permissions[{{ $permission->id }}]" value="allow" {{ $mode === 'allow' ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input type="radio" name="permissions[{{ $permission->id }}]" value="deny" {{ $mode === 'deny' ? 'checked' : '' }}>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-right">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </form>
@endsection

