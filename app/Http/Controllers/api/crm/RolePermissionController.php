<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function indexRoles () {
        $roles = Role::with(['permissions' => function ($query) {
            $query->orderBy('name', 'asc');
        }])->get();
        return response()->json(['datos' => $roles], 200);
    }

    public function showRoleWithPermissions(string $id) {
        $role = Role::with('permissions')->findOrFail($id);
        $permisosAgrupados = $role->permissions->groupBy('grupo');    
        return response()->json([ 'message' => 'Datos cargados', 'datos' => [ 'nombre' => $role->name, 'id' => $role->id, 'modificacion' => $role->modificacion, 'permisos' => $permisosAgrupados]], 200);
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|unique:roles,name',
        ]);
        $role = Role::create(['name' => $request->nombre, 'guard_name' => 'sanctum']);
        return response()->json(['message' => 'Rol creado correctamente', 'datos' => $role], 201);
    }

    public function updateRole(Request $request, string $id)
    {
        $role = Role::findOrFail($id);
        $request->validate([
            'nombre' => 'required|string|unique:roles,name,'.$role->id,
            'permisos' => 'required|array|min:1',
            'permisos.*' => 'exists:permissions,name'
        ]);
        DB::transaction(function () use ($request, $role) {
            $role->update(['name' => $request->nombre]);
            $role->syncPermissions($request->permisos);
        });
        return response()->json(['message' => 'Rol actualizado correctamente', 'datos' => $role], 200);
    }

    public function destroyRole($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return response()->json(['message' => 'Rol eliminado correctamente'], 200);
    }

    public function assignPermissionsToRol(Request $request) {
        $request->validate([
            'nombre' => 'required|string|unique:roles,name',
            'permisos' => 'required|array|min:1',
            ],[
                'nombre.required' => 'El campo nombre es obligatorio.',
                'permisos' => 'Usted debe asignar por lo menos un permiso al rol que se va a crear.'
            ]);
        $role = Role::create(['name' => $request->nombre, 'guard_name' => 'sanctum']);
        $role->givePermissionTo($request->permisos);
        return response()->json(['message' => 'Rol creado correctamente', 'datos' => $role->permissions], 201);
    }

    public function indexPermissions () {
        $permissions = Permission::all();
        $permissionGroups = $permissions->groupBy('grupo');
        return response()->json(['datos' => $permissionGroups], 200);
    }

    public function assignRolesToUser(Request $request) {
        $request->validate([
            'user_id' => 'required|numeric',
            'roles' => 'array',
            ],[
                'user_id.required' => 'El id del usuario es obligatorio.',
                'roles.array' => 'Usted debe enviar una lista de roles.'
        ]);
        $user = User::findOrFail($request->user_id);
        $user->syncRoles($request->roles);
        return response()->json(['message' => 'Roles asignados correctamente', 'datos' => $user->getRoleNames()], 201);
    }

    public function showUserRoles(string $id) {
        $user = User::findOrFail($id);
        return response()->json(['message' => 'Datos cargados', 'roles' => $user->roles->pluck('name')], 201);
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|unique:permissions,name',
        ]);
        $permission = Permission::create(['name' => $request->nombre, 'guard_name' => 'sanctum']);
        return response()->json(['message' => 'Permiso creado correctamente', 'datos' => $permission], 201);
    }

    public function updatePermission(Request $request, string $id)
    {
        $permiso = Permission::find($id);
        $request->validate([
            'nombre' => 'required|string|unique:permissions,name,'.$permiso->id,
        ]);
        $permiso->name = $request->nombre;
        $permiso->update();
        return response()->json(['message' => 'Permiso actualizado correctamente', 'datos' => $permiso], 200);
    }

    public function destroyPermission($id)
    {
        $permiso = Permission::find($id);
        $permiso->delete();
        return response()->json(['message' => 'Permiso eliminado correctamente'], 200);
    }
}