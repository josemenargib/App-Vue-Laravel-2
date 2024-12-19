<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_tecnologias;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class TecnologiasController extends Controller
{
    // Display a listing of the resource.
    public function index(Request $request)
    {
        if (Auth::user()->hasPermissionTo('tecnologia ver')) {
            $search = $request->query('search');
            $currentPage = $request->query('page', 1);
            $query = Crm_tecnologias::query();
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
        // if (Auth::user()->hasPermissionTo('tecnologia ver')) {
        $search = $request->query('search');

        $query = Crm_tecnologias::query();
        $query->where('is_deleted', false);
        if (!empty($search)) {
            $query->where('nombre', 'LIKE', "%$search%");
        }
        $query->with('modulos');
        $query->orderBy('id', 'desc');
        $items = $query->get();

        return response()->json(['mensaje' => 'Datos cargados', 'datos' => $items]);
        // } else {
        // return response()->json(['message' => "No tienes permiso para realizar esta acción."], 403);
        // }
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        if (Auth::user()->hasPermissionTo('tecnologia crear')) {
            $request->validate([
                'nombre' => "required|max:50",
                'descripcion' => "required",
                'imagen' => "nullable|mimes:png,jpg|max:2048",
                // 'modulos' => "nullable|array",
                // 'modulos.*' => "nullable|exists:modulos,id",
            ], [
                'nombre.required' => 'El campo Nombre es requerido.',
                'descripcion.required' => 'El campo Descripción es requerido.',
                'imagen.mimes' => 'El campo Imagen debe ser de tipo PNG o JPG.',
                'imagen.max' => 'El campo Imagen debe ser menor a 2048kb (2MB).',
            ]);
            $item = new Crm_tecnologias();
            $item->nombre = $request->nombre;
            $item->descripcion = $request->descripcion;
            if ($request->file('imagen')) {
                $image = $request->file('imagen');
                $nameImage = time() . '.png';
                $image->move('img/img_tecnologias/', $nameImage);
                $item->imagen = $nameImage;
            }
            if ($item->save()) {
                // if ($request->has('modulos') && count($request->modulos) > 0) {
                //     $item->modulos()->sync($request->modulos);
                // }
                return response()->json(['mensaje' => "Nueva Tecnología agregada exitosamente.", 'datos' => $item], 201);
            } else {
                return response()->json(['mensaje' => "Error. Nueva Tecnología no agregada."], 422);
            }
        } else {
            return response()->json(['message' => "No tienes permiso para realizar esta acción."], 403);
        }
    }

    // Display the specified resource.
    public function show(string $id)
    {
        if (Auth::user()->hasPermissionTo('tecnologia ver')) {
            $tecnologia = Crm_tecnologias::with('modulos', 'especialidades')->find($id);
            if ($tecnologia) {
                return response()->json(['mensaje' => "Detalles de Tecnología", 'datos' => $tecnologia], 200);
            } else {
                return response()->json(['mensaje' => "Tecnología No encontrada."], 422);
            }
        } else {
            return response()->json(['message' => "No tienes permiso para realizar esta acción."], 403);
        }
    }

    // Update the specified resource in storage.
    public function update(Request $request, string $id)
    {
        if (Auth::user()->hasPermissionTo('tecnologia editar')) {
            $item = Crm_tecnologias::find($id);
            if (!$item) {
                return response()->json(['mensaje' => "Tecnología no encontrada."], 404);
            }
            $request->validate([
                'nombre' => "required|max:50|unique:crm_tecnologias,nombre," . $id,
                'descripcion' => "required|max:500|unique:crm_tecnologias,descripcion," . $id,
                'imagen' => "nullable|mimes:png,jpg|max:2048",
                // 'is_deleted' => 'required|boolean',
            ], [
                'nombre.required' => 'El campo Nombre es requerido.',
                'descripcion.required' => 'El campo Descripción es requerido.',
                'imagen.mimes' => 'El campo Imagen debe ser de tipo PNG o JPG.',
                'imagen.max' => 'El campo Imagen debe ser menor a 2048kb (2MB).',
            ]);
            $item->nombre = $request->nombre;
            $item->descripcion = $request->descripcion;
            if ($request->file('imagen')) {
                $image = $request->file('imagen');
                $nameImage = time() . '.pnp';
                $image->move('img/img_tecnologias/', $nameImage);
                if ($item->imagen) {
                    // verifica si en verdad existe el archivo de imagen en la ruta (public/img/img_tecnologias)
                    $path = 'img/img_tecnologias/' . $item->imagen;
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
                $item->imagen = $nameImage;
            }
            if ($item->save()) {
                return response()->json(['mensaje' => "Tecnología actualizada correctamente.", 'data' => $item], 200);
            } else {
                return response()->json(['mensaje' => "Actualización de Tecnología NO realizada."], 422);
            }
        } else {
            return response()->json(['message' => "No tienes permiso para realizar esta acción."], 403);
        }
    }

    // Remove the specified resource from storage.
    public function destroy(string $id)
    {
        if (Auth::user()->hasPermissionTo('tecnologia borrar')) {
            $item = Crm_tecnologias::find($id);
            if (!$item) {
                return response()->json(['mensaje' => "Tecnología no encontrada."], 404);
            }
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
