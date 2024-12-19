<?php
namespace App\Http\Controllers\Api\Web;

use Illuminate\Http\Request;
use App\Models\Web_redes_sociales;
use App\Http\Controllers\Controller;
use App\Models\Web_empresas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class RedesSocialesController extends Controller
{
    public function index()
    {
        // Obtener todas las redes sociales con la información de la empresa relacionada
        $redesSociales = Web_redes_sociales::orderBy('id', 'desc')->paginate(10);

        return response()->json(
            [
                "mensaje" => "Datos cargados",
                "datos" => $redesSociales
            ],
            200
        );
    }
    public function store(Request $request)
{
    if (Auth::user()->hasPermissionTo('red social crear')) {
        // Validar la solicitud
        $request->validate([
            'nombre' => 'required|max:50',
            'logo_img' => [
    'required',
    'regex:/^(fa|fab|fa-brands|fa-solid) [a-z-]+(\s+(fa|fab|fa-brands|fa-solid) [a-z-]+)*$/'
],

            'url' => 'required|url|max:255',  // Aumentar el límite de la URL
        ]);

        // Crear una nueva red social
        $redSocial = new Web_redes_sociales();
        $empresa = Web_empresas::orderBy('id')->first();
        $redSocial->empresa_id = $empresa->id;
        $redSocial->nombre = $request->nombre;
        $redSocial->logo_img = $request->logo_img;
        $redSocial->url = $request->url;

        if ($redSocial->save()) {
            return response()->json([
                'mensaje' => 'Red social creada correctamente',
                'datos' => $redSocial
            ], 201);
        } else {
            return response()->json([
                'mensaje' => 'Error al crear la red social'
            ], 422);
        }
    } else {
        return response()->json([
            'message' => 'No tienes permiso para realizar esta acción'
        ], 403);
    }
}


    public function show(string $id)
    {
        if (Auth::user()->hasPermissionTo('red social ver')) {
            // Obtener una red social específica
            $redSocial = Web_redes_sociales::find($id);

            // Verificar si se encontró el registro
            if ($redSocial) {
                return response()->json([
                    "mensaje" => "Datos cargados",
                    "datos" => $redSocial
                ], 200);
            } else {
                return response()->json([
                    "mensaje" => "Registro no encontrado"
                ], 404);
            }
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta acción"], 403);
        }
    }

    public function update(Request $request, string $id)
    {
        if (Auth::user()->hasPermissionTo('red social editar')) {
            // Validar la solicitud
            $request->validate([
                "nombre" => "required|max:50|unique:web_redes_sociales,nombre," . $id,
                'logo_img' => [
                'required',
                'regex:/^fa-[a-z-]+(\s+fa-[a-z-]+)*$/'
            ],
                "url" => "nullable|url|max:250",
            ]);



            // Crear una nueva red social
            $redSocial = Web_redes_sociales::find($id);

            // Verificar si se encontró el registro
            if (!$redSocial) {
                return response()->json([
                    "mensaje" => "Red social no encontrada"
                ], 404);
            }

            // Actualizar los campos de la red social
            $redSocial->nombre = $request->nombre;
            $redSocial->logo_img = $request->logo_img;
            $redSocial->url = $request->url;

            // Guardar los cambios
            if ($redSocial->save()) {
                return response()->json([
                    "mensaje" => "Red social actualizada correctamente",
                    "datos" => $redSocial
                ], 200);
            } else {
                return response()->json([
                    "mensaje" => "Error al actualizar la red social"
                ], 422);
            }
            return response()->json(["message" => "No tienes permiso para realizar esta acción"], 403);
        }
    }

    public function destroy(string $id)
    {
        if (Auth::user()->hasPermissionTo('red social borrar')) {
            // Realizar un soft delete (cambiar el estado a is_deleted)
            $redSocial = Web_redes_sociales::find($id);

            if (!$redSocial) {
                return response()->json(["mensaje" => "Red social no encontrada"], 404);
            }

            $redSocial->is_deleted = !$redSocial->is_deleted; // Cambia el estado del campo is_deleted

            if ($redSocial->save()) {
                return response()->json(["mensaje" => "Estado modificado", "datos" => $redSocial], 202);
            } else {
                return response()->json(["mensaje" => "No se pudo modificar el estado"], 422);
            }
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta acción"], 403);
        }
    }

    public function redesSocialesActivo()
    {
        // Obtener todas las redes sociales no eliminadas
        $redesSociales = Web_redes_sociales::where('is_deleted', false)->get();
        return response()->json(
            [
                "mensaje" => "Datos cargados",
                "datos" => $redesSociales
            ],
            200
        );
    }
}
