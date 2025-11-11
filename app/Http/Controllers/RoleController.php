<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified','can:roles.manage']);
    }

    public function index()
    {
        $roles = Role::orderBy('name')->get();
        return view('roles.index', compact('roles'));
    }

    public function editPermissions(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $assigned = $role->permissions()->pluck('permissions.id')->toArray();
        return view('roles.edit_permissions', compact('role', 'permissions', 'assigned'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $permissionIds = collect($request->input('permissions', []))
            ->map(fn($id) => (int) $id)
            ->filter()
            ->values();

        $role->permissions()->sync($permissionIds);

        return redirect()->route('roles.index')->with('success', 'Izin untuk role berhasil diperbarui.');
    }
}

