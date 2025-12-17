<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionController extends Controller
{
    /**
     * Crear roles admin por guard
     */
    public function createRoles()
    {
        $roles = [];

        foreach (['rubro', 'company'] as $guard) {
            $roles[] = Role::firstOrCreate(
                [
                    'name' => 'admin',
                    'guard_name' => $guard,
                ]
            );
        }

        return response()->json([
            'message' => 'Roles admin creados correctamente',
            'roles' => $roles,
        ]);
    }

    /**
     * Crear permisos por guard
     */
    public function createPermissions()
    {
        $permissions = [];

        $guards = ['rubro', 'company'];
        $basePermissions = ['view.dashboard', 'view.users'];

        foreach ($guards as $guard) {
            foreach ($basePermissions as $perm) {
                $permissions[] = Permission::firstOrCreate(
                    [
                        'name' => $perm,
                        'guard_name' => $guard,
                    ]
                );
            }
        }

        return response()->json([
            'message' => 'Permisos creados correctamente',
            'permissions' => $permissions,
        ]);
    }

    /**
     * Asignar permisos al rol admin (por guard)
     */
    public function assignPermissionsToRole(Request $request)
    {
        $request->validate([
            'guard' => 'required|in:rubro,company',
        ]);

        $role = Role::where('name', 'admin')
            ->where('guard_name', $request->guard)
            ->firstOrFail();

        $permissions = Permission::where('guard_name', $request->guard)->get();

        $role->syncPermissions($permissions);

        return response()->json([
            'message' => 'Permisos asignados al rol correctamente',
            'role' => $role->name,
            'guard' => $role->guard_name,
            'permissions' => $permissions->pluck('name'),
        ]);
    }

    /**
     * Asignar rol admin a un usuario (por guard)
     */
    public function assignRoleToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'guard'   => 'required|in:rubro,company',
        ]);

        $user = User::findOrFail($request->user_id);

        $role = Role::where('name', 'admin')
            ->where('guard_name', $request->guard)
            ->firstOrFail();

        $user->assignRole($role);

        return response()->json([
            'message' => 'Rol asignado al usuario correctamente',
            'user_id' => $user->id,
            'role' => $role->name,
            'guard' => $role->guard_name,
        ]);
    }
}
