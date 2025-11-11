<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class PermissionService
{
    /**
     * Menentukan apakah user diizinkan untuk ability/permission key tertentu.
     * Logika: override per-user (allow/deny) > role permissions > default false.
     */
    public function allowed(User $user, string $key): bool
    {
        // Cari permission by key
        $permission = Permission::where('key', $key)->first();
        if (!$permission) {
            // Jika permission belum terdaftar, default false
            return false;
        }

        // Cek override per-user
        $override = $permission->users()->where('users.id', $user->id)->first();
        if ($override) {
            return (bool) $override->pivot->allowed;
        }

        // Cek berdasarkan role
        $roleSlug = $user->role; // menggunakan kolom role string yang sudah ada
        if (!$roleSlug) {
            return false;
        }

        $role = Role::where('slug', $roleSlug)->first();
        if (!$role) {
            return false;
        }

        return $role->permissions()->where('permissions.id', $permission->id)->exists();
    }
}

