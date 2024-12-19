<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Web_empresas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Web_reconocimientos;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;


class ReconocimientosController extends Controller
{
    public function index(Request $request)
    {
        // Obtiene todos los reconocimientos y los pagina de 10 en 10
        $search = $request->get('search');
        $reconocimientos = Web_reconocimientos::orderBy('titulo', 'asc')->when($search, function ($query) use ($search) {
            $query->where('titulo', 'LIKE', '%' . $search . '%');
        })->with('empresa')->paginate(10);

        // Retorna una respuesta JSON con un mensaje y los datos paginados
        return response()->json([
            "mensaje" => "Datos cargados",
            "datos" => $reconocimientos
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->hasPermissionTo('reconocimiento crear')) {
            // Validar los datos de entrada
            $request->validate([
                "titulo" => "required|string|max:100",
                "imagen" => "nullable|image|mimes:jpeg,png,jpg|max:2048",
                "detalle" => "nullable|string",
            ]);

            // Crear un nuevo reconocimiento
            $reconocimiento = new Web_reconocimientos();
            $empresa = Web_empresas::orderBy('id')->first();
            $reconocimiento->empresa_id = $empresa->id;
            $reconocimiento->titulo = $request->titulo;
            $reconocimiento->detalle = $request->detalle;

            if ($request->file('imagen') != null) {
                $file = $request->file('imagen');
                $nombreImagen = time() . '.png';
                $file->move("img/img_reconocimientos/", $nombreImagen);
                $reconocimiento->imagen = $nombreImagen;
            }

            if ($reconocimiento->save()) {
                return response()->json(["mensaje" => "Registro exitoso", "datos" => $reconocimiento], 201);
            } else {
                return response()->json(["mensaje" => "No se pudo realizar el registro"], 422);
            }
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta acción"], 403);
        }
    }



    public function show(string $id)
    {
        if (Auth::user()->hasPermissionTo('reconocimiento ver')) {
            // Buscar un reconocimiento específico
            $reconocimiento = Web_reconocimientos::find($id);

            // Verificar si se encontró el registro
            if ($reconocimiento) {
                return response()->json([
                    "mensaje" => "Datos cargados",
                    "datos" => $reconocimiento
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
    // Verificar si el usuario tiene el permiso para editar un reconocimiento
    if (Auth::user()->hasPermissionTo('reconocimiento editar')) {

        // Validar los datos de entrada
        $request->validate([
            'titulo' => 'required|string|max:100',
            'imagen' => 'nullable|image|mimes:jpeg,png|max:2048',
            'detalle' => 'nullable|string',
        ]);

        // Buscar el reconocimiento específico por su ID
        $reconocimiento = Web_reconocimientos::find($id);

        // Verificar si el reconocimiento existe
        if (!$reconocimiento) {
            return response()->json([
                'mensaje' => 'Reconocimiento no encontrado'
            ], 404);
        }

        // Actualizar los campos
        $reconocimiento->titulo = $request->titulo;
        $reconocimiento->detalle = $request->detalle;

        // Manejar la actualización de la imagen si existe
        if ($request->hasFile('imagen')) {
            // Eliminar la imagen anterior si no es la predeterminada
            if ($reconocimiento->imagen && $reconocimiento->imagen !== 'default.png' && file_exists(public_path('img/img_reconocimientos/' . $reconocimiento->imagen))) {
                unlink(public_path('img/img_reconocimientos/' . $reconocimiento->imagen));
            }

            // Subir la nueva imagen
            $file = $request->file('imagen');
            $nombreImagen = time() . '.png';
            $file->move(public_path('img/img_reconocimientos'), $nombreImagen);
            $reconocimiento->imagen = $nombreImagen;
        }

        // Guardar los cambios en la base de datos
        if ($reconocimiento->save()) {
            return response()->json([
                'mensaje' => 'Actualización exitosa',
                'datos' => [
                    'titulo' => $reconocimiento->titulo,
                    'detalle' => $reconocimiento->detalle,
                    'imagen' => asset('img/img_reconocimientos/' . $reconocimiento->imagen),
                    'is_deleted' => $reconocimiento->is_deleted
                ]
            ], 200);
        }

        // Si ocurre un error al guardar
        return response()->json([
            'mensaje' => 'No se pudo realizar la actualización'
        ], 500);

    } else {
        // Respuesta si el usuario no tiene permisos
        return response()->json([
            'mensaje' => 'No tienes permiso para realizar esta acción'
        ], 403);
    }
}




    public function destroy(string $id)
    {
        if (Auth::user()->hasPermissionTo('reconocimiento borrar')) {
            // Realizar un soft delete (cambiar el estado a is_deleted)
            $reconocimiento = Web_reconocimientos::find($id);

            if (!$reconocimiento) {
                return response()->json(["mensaje" => "Registro no encontrado"], 404);
            }

            $reconocimiento->is_deleted = !$reconocimiento->is_deleted; // Cambia el estado del campo is_deleted

            if ($reconocimiento->save()) {
                return response()->json(["mensaje" => "Estado modificado", "datos" => $reconocimiento], 202);
            } else {
                return response()->json(["mensaje" => "No se pudo modificar el estado"], 422);
            }
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta acción"], 403);
        }
    }

    public function reconocimientosActivo()
    {
        // Obtiene todos los reconocimientos no eliminados y los pagina de 10 en 10
        $reconocimientos = Web_reconocimientos::where('is_deleted', false)->paginate(10);

        // Retorna una respuesta JSON con un mensaje y los datos paginados
        return response()->json(
            [
                "mensaje" => "Datos cargados",  // Mensaje indicando que los datos se han cargado con éxito
                "datos" => $reconocimientos     // Los datos paginados que se van a retornar
            ],
            200  // Código de estado HTTP 200, que indica que la solicitud fue exitosa
        );
    }
}
