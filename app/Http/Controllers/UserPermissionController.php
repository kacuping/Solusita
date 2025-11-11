<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;

class UserPermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified','can:users.manage']);
    }

    public function edit(User $user)
    {
        $permissions = Permission::orderBy('name')->get();
        // Ambil override per user
        $overrides = $user->permissions()->pluck('allowed', 'permissions.id')->toArray();

        return view('users.permissions', compact('user', 'permissions', 'overrides'));
    }

    public function update(Request $request, User $user)
    {
        // Data berbentuk: permissions[permission_id] = 'inherit'|'allow'|'deny'
        $data = $request->input('permissions', []);
        $syncData = [];

        foreach ($data as $permissionId => $mode) {
            if ($mode === 'inherit') {
                // Hapus override
                continue;
            }
            $syncData[$permissionId] = ['allowed' => $mode === 'allow'];
        }

        // Sinkronisasi: hapus yang tidak ada, tambahkan/ubah yang ada
        $user->permissions()->sync($syncData);

        return redirect()->route('users.index')->with('success', 'Override izin pengguna berhasil diperbarui.');
    }
}

