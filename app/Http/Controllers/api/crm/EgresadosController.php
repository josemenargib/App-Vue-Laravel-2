<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_egresados;
use App\Models\Crm_empleo_estados;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class EgresadosController extends Controller
{
    public function cambiarRol(Request $request, $usuarioId)
    {
        if (!Auth::user()->hasPermissionTo('gestion egresados')) {
            return response()->json(['mensaje' => 'No tienes permiso para realizar esta acción'], 403);
        }
    
        $request->validate([
            'rol_name' => 'required|in:estudiante,egresado',
        ]);

        $usuario = User::find($usuarioId);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
    
        $rol = Role::where('name', 'Egresado')->first();
        if (!$rol) {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }
    
        DB::beginTransaction();
    
        try {
            $usuario->syncRoles($rol);

            $item = new Crm_empleo_estados();
            $item->user_id = $usuarioId;
            $item->save();
            DB::commit();
    
            return response()->json(['success' => 'Rol actualizado con éxito'], 200);
        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json(['error' => 'No se pudo actualizar el rol'], 500);
        }
    }
    


    public function users_egresados()
{
    try {
        $egresadoRole = Role::where('name', 'egresado')->first();

        if (!$egresadoRole) {
            return response()->json(["mensaje" => "Rol 'egresado' no encontrado"], 404);
        }
        $usuarios = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.model_type', User::class)
            ->where('model_has_roles.role_id', $egresadoRole->id)
            ->where('users.is_deleted', 0) 
            ->with('datos_generales') 
            ->get();

        return response()->json(["mensaje" => "Datos cargados", "datos" => $usuarios], 200);
    } catch (\Throwable $th) {
        return response()->json(["mensaje" => "ERROR", "datos" => $th->getMessage()], 400);
    }
}

}    