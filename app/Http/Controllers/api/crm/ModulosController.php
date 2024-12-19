<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_modulos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModulosController extends Controller
{
    // Display a listing of the resource.
    public function index(Request $request)
    {
        if (Auth::user()->hasPermissionTo('modulo ver')) {
            $search = $request->query('search');
            $currentPage = $request->query('page', 1);

            $query = Crm_modulos::query();
            if ($search) {
                $query->where('nombre', 'LIKE', "%$search%");
            }
            $query->orderBy('id', 'desc');
            $items = $query->paginate(6, ['*'], 'page', $currentPage);

            return response()->json(['mensaje' => 'Datos cargados', 'datos' => $items]);
        } else {
            return response()->json(['message' => "No tienes permiso para realizar esta acción."], 403);
        }
    }

    public function indexActivos(Request $request)
    {
        // if (Auth::user()->hasPermissionTo('modulo ver')) {
        $search = $request->query('search');

        $query = Crm_modulos::query();
        $query->where('is_deleted', false);
        if (!empty($search)) {
            $query->where('nombre', 'LIKE', "%$search%");
        }
        // $query->with('evaluaciones', 'tecnologias', 'especialidades');
        $query->with('tecnologias');
        $query->orderBy('id', 'desc');
        $items = $query->get();

        return response()->json(['mensaje' => 'Datos cargados', 'datos' => $items]);
        // } else {
        //     return response()->json(['message' => "No tienes permiso para realizar esta acción."], 403);
        // }
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        if (Auth::user()->hasPermissionTo('modulo crear')) {
            $request->validate([
                'nombre' => "required|max:50",
                'objetivo' => "required|max:500",
                'entregable' => "nullable",
                'imagen' => "nullable|mimes:png,jpg|max:2048",
                // 'is_deleted' por defecto es: falso,

                'tecnologias' => "nullable|array",
                'especialidades' => "nullable|array",
                'tecnologias.*' => "integer|exists:crm_tecnologias,id",
                'especialidades.*' => "integer|exists:crm_especialidades,id",

                // 'registros' => "nullable|array",
                // 'tipos_pruebas' => "nullable|array",
                // 'registros.*' => "integer|exists:crm_registros,id",
                // 'tipos_pruebas.*' => "integer|exists:crm_tipo_pruebas,id",
            ], [
                'nombre.required' => 'El campo Nombre es requerido.',
                'objetivo.required' => 'El campo Objetivo es requerido.',
                'imagen.mimes' => 'El campo Imagen debe ser un archivo PNG o JPG.',
                'imagen.max' => 'El campo Imagen debe ser menor a 2048kb (2MB).',
            ]);
            $item = new Crm_modulos();
            $item->nombre = $request->nombre;
            $item->objetivo = $request->objetivo;
            if ($request->entregable) {
                $item->entregable = $request->entregable;
            }
            if ($request->file('imagen')) {
                $image = $request->file('imagen');
                // $nameImage = time() . '.' . $image->getClientOriginalExtension();
                $nameImage = time() . '.png';
                $image->move('img/img_modulos/', $nameImage);
                $item->imagen = $nameImage;
            }
            if ($item->save()) {
                // if (!empty($request->tecnologias) && count($request->tecnologias) > 0) {
                //     $item->tecnologias()->sync($request->tecnologias);
                // }
                // if (!empty($request->especialidades) && count($request->especialidades) > 0) {
                //     $item->especialidades()->sync($request->especialidades);
                // }
                return response()->json(['mensaje' => "Módulo creado exitosamente.", 'datos' => $item], 201);
            } else {
                return response()->json(['mensaje' => "Error. Módulo NO creado."], 422);
            }
        } else {
            return response()->json(['message' => "No tienes permiso para realizar esta acción."], 403);
        }
    }

    // Display the specified resource.
    public function show(string $id)
    {
        if (Auth::user()->hasPermissionTo('modulo ver')) {
            // $modulo = Crm_modulos::with('tecnologias', 'especialidades', 'registros', 'tipos_pruebas')->find($id);
            $modulo = Crm_modulos::with('tecnologias', 'especialidades')->find($id);
            if ($modulo) {
                return response()->json(['mensaje' => 'Detalles del módulo', 'datos' => $modulo], 200);
            } else {
                return response()->json(['mensaje' => 'Módulo no encontrado'], 422);
            }
        } else {
            return response()->json(['message' => "No tienes permiso para realizar esta acción."], 403);
        }
    }

    // Update the specified resource in storage.
    public function update(Request $request, string $id)
    {
        if (Auth::user()->hasPermissionTo('modulo editar')) {
            $item = Crm_modulos::find($id);
            if (!$item) {
                return response()->json(['mensaje' => "Módulo no encontrado."], 404);
            }
            $request->validate([
                'nombre' => "required|max:50|unique:crm_modulos,nombre," . $id,
                'objetivo' => "required|max:500|unique:crm_modulos,objetivo," . $id,
                'entregable' => "nullable",
                'imagen' => "nullable|mimes:png,jpg|max:2048",
                // 'is_deleted' => 'required|boolean',
            ], [
                'nombre.required' => 'El campo Nombre es requerido.',
                'objetivo.required' => 'El campo Objetivo es requerido.',
                'imagen.max' => 'El campo Imagen debe ser menor a 2048kb (2MB).',
                'imagen.mimes' => 'El campo Imagen debe ser un archivo PNG o JPG.',
            ]);
            $item->nombre = $request->nombre;
            $item->objetivo = $request->objetivo;
            if ($request->entregable) {
                $item->entregable = $request->entregable;
            }
            if ($request->file('imagen')) {
                $image = $request->file('imagen');
                $nameImage = time() . '.png';
                $image->move('img/img_modulos/', $nameImage);
                // ¡Sí existe!, elimina ruta/al/archivo/nombreImagen.png almacenado
                if ($item->imagen) {
                    // verifica si en verdad existe el archivo de imagen en la ruta (public/img/img_modulos)
                    $path = 'img/img_modulos/' . $item->imagen;
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
                $item->imagen = $nameImage;
            }
            // $item->is_deleted = $request->is_deleted;
            if ($item->save()) {
                return response()->json(['mensaje' => "Módulo actualizado exitosamente.", 'datos' => $item], 200);
            } else {
                return response()->json(['mensaje' => "Actualización del Módulo NO realizada.", 'datos' => $item], 422);
            }
        } else {
            return response()->json(['message' => "No tienes permiso para realizar esta acción."], 403);
        }
    }

    // Remove the specified resource from storage.
    public function destroy(string $id)
    {
        if (Auth::user()->hasPermissionTo('modulo borrar')) {
            $item = Crm_modulos::find($id);
            // $item->delete();
            // $item->is_deleted = true;
            $item->is_deleted = !$item->is_deleted;
            if ($item->save()) {
                return response()->json(['mensaje' => 'Eliminación lógica ejecutada.'], 204);
            } else {
                return response()->json(['mensaje' => "Módulo creado exitosamente."], 422);
            }
        } else {
            return response()->json(['message' => "No tienes permiso para realizar esta acción."], 403);
        }
    }
}
