<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_datos_generales;
use App\Models\Crm_postulacion_forms;
use App\Models\Crm_postulaciones;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostulacionesController extends Controller
{


    public function index()
    {
        $postulaciones = Crm_postulaciones::where('estado', 'entrevista')->with([
            'batch',
            'batch.crm_especialidades',
            'users.roles',
            'users.datos_generales'
        ])
            ->paginate(7);
        return response()->json(["mensaje" => "Datos cargados", "datos" => $postulaciones], 200);
    }
    public function filtrofases(Request $request)
    {
        $search = $request->get('search');
        $postulaciones = Crm_postulaciones::when($search, function ($query) use ($search) {
            $query->where('estado', 'LIKE', '%' . $search . '%');
        })->with([
            'batch',
            'batch.crm_especialidades',
            'users.roles',
            'users.datos_generales'
        ])->paginate(10);
        return response()->json(["mensaje" => "Datos cargados", "datos" => $postulaciones], 200);
    }
    public function filtrofasesagrupado(Request $request)
    {
        $auth = Auth::id();

        // Obtener todos los usuarios
        $usuarios = Crm_datos_generales::all();

        $userIds = $usuarios->pluck('user_id');

        // Obtener todas las postulaciones para los user_id encontrados
        $postulaciones = Crm_postulaciones::whereIn('user_id', $userIds)
            ->with([
                'batch',
                'batch.crm_especialidades',
                'users.roles',
                'users.datos_generales'
            ])->get();

        // Agrupar las postulaciones por `user_id`
        $postulacionesAgrupadas = $postulaciones->groupBy('user_id');

        // Crear un arreglo que agrupe las postulaciones bajo un solo usuario
        $usuariosAgrupados = $postulacionesAgrupadas->map(function ($postulaciones, $userId) {
            $usuario = $postulaciones->first()->users;
            return [
                'user_id' => $userId,
                'usuario' => $usuario, // Información del usuario
                'postulaciones' => $postulaciones // Lista de postulaciones de este usuario
            ];
        });

        // Reindexar los usuarios agrupados
        $usuariosAgrupados = $usuariosAgrupados->values();

        return response()->json([
            "mensaje" => "Datos cargados",
            "datos" => $usuariosAgrupados,
            "auth" => $auth
        ], 200);
    }


    public function filtrofasesagrupadoshow(Request $request, $userId)
    {

        $search = $request->get('search');


        $postulaciones = Crm_postulaciones::when($search, function ($query) use ($search) {
            $query->where('estado', 'LIKE', '%' . $search . '%');
        })->where('user_id', $userId)
            ->with([
                'batch',
                'batch.crm_especialidades',
                'users.roles',
                'users.datos_generales'
            ])->get();

        $postulacionesAgrupadas = $postulaciones->groupBy('user_id');

        $usuariosAgrupados = $postulacionesAgrupadas->map(function ($postulaciones, $userId) {
            $usuario = $postulaciones->first()->users;
            return [
                'user_id' => $userId,
                'usuario' => $usuario,
                'postulaciones' => $postulaciones
            ];
        });

        $usuariosAgrupados = $usuariosAgrupados->values();

        $perPage = 10;
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;

        $usuariosPaginados = $usuariosAgrupados->slice($offset, $perPage);

        $paginacion = new \Illuminate\Pagination\LengthAwarePaginator(
            $usuariosPaginados,
            $usuariosAgrupados->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json([
            "mensaje" => "Datos cargados",
            "datos" => $paginacion
        ], 200);
    }


    public function indexsearchagrupado(Request $request)
    {
        $search = $request->get('search');
        $auth = Auth::id();


        $usuarios = Crm_datos_generales::when($search, function ($query) use ($search) {
            $query->where('nombre', 'LIKE', '%' . $search . '%')
                ->orWhere('apellido', 'LIKE', "%$search%")
                ->orWhere('ci', 'LIKE', "%$search%");
        })->get();

        $userIds = $usuarios->pluck('user_id');


        $postulaciones = Crm_postulaciones::whereIn('user_id', $userIds)
            ->with([
                'batch',
                'batch.crm_especialidades',
                'users.roles',
                'users.datos_generales'
            ])->get();


        $postulacionesAgrupadas = $postulaciones->groupBy('user_id');


        $usuariosAgrupados = $postulacionesAgrupadas->map(function ($postulaciones, $userId) {
            $usuario = $postulaciones->first()->users;
            return [
                'user_id' => $userId,
                'usuario' => $usuario,
                'postulaciones' => $postulaciones
            ];
        });


        $usuariosAgrupados = $usuariosAgrupados->values();


        return response()->json([
            "mensaje" => "Datos cargados",
            "datos" => $usuariosAgrupados,
            "auth" => $auth
        ], 200);
    }



    public function indexsearch(Request $request)
    {
        $search = $request->get('search');
        $auth = Auth::id();
        $usuarios = Crm_datos_generales::when($search, function ($query) use ($search) {
            $query->where('nombre', 'LIKE', '%' . $search . '%')->orWhere('apellido', 'LIKE', "%$search%")->orWhere('ci', 'LIKE', "%$search%");;
        })->get();
        $userIds = $usuarios->pluck('user_id');

        $postulaciones = Crm_postulaciones::whereIn('user_id', $userIds)->with([
            'batch',
            'batch.crm_especialidades',
            'users.roles',
            'users.datos_generales'
        ])->paginate(10);
        return response()->json(["mensaje" => "Datos cargados", "datos" => $postulaciones, "auth" => $auth], 200);
    }
    public function store(Request $request)
    {
        $request->validate([
            "user_id" => "required|max:15",
            "batch_id" => "required|max:15",
            "descripcion" => "nullable|max:250",
        ]);
        $item = new Crm_postulaciones();
        $item->user_id =   $request->user_id;
        $item->batch_id = $request->batch_id;
        $item->estado = 'prueba';
        $item->descripcion = $request->descripcion;
        if ($item->save()) {
            return response()->json(["mensaje" => "Registro exitoso.", "datos" => $item], 200);
        } else {
            return response()->json(["mensaje" => "No se pudo realizaar el registro."], 422);
        }
    }
    public function show(string $user_id)
    {
        $item[] = Crm_postulaciones::with([
            'batch',
            'batch.crm_especialidades',
            'users.roles',
            'users.datos_generales'
        ])->find($user_id);
        return response()->json(["datos" => $item], 200);
    }
    public function update(Request $request, string $id)
    {
        $request->validate([
            "user_id" => "required|max:15",
            "batch_id" => "required|max:15",
            "estado" => "required|max:15",
            "descripcion" => "nullable|max:250",
        ]);
        $item = Crm_postulaciones::find($id);
        $item->user_id =   $request->user_id;
        $item->batch_id = $request->batch_id;
        $item->estado = strtolower($request->estado);
        $item->descripcion = $request->descripcion;
        if ($item->save()) {
            return response()->json(["mensaje" => "Postulación Modificada.", "datos" => $item], 201);
        } else {
            return response()->json(["mensaje" => "No se pudo realizaar la modificacion."], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $request->validate([
            "estado" => "required|max:15",
        ]);
        $item = Crm_postulaciones::find($id);
        $item->estado = strtolower($request->estado);
        if ($item->save()) {
            return response()->json(["mensaje" => "Postulación Modificada.", "datos" => $item], 201);
        } else {
            return response()->json(["mensaje" => "No se pudo realizaar la modificacion."], 422);
        }
    }
    public function verifestado(Request $request)
    {
        $search = $request->get('search');
        $auth = Auth::id();
        $usuarios = Crm_postulaciones::when($search, function ($query) use ($search) {
            $query->where('estado', 'LIKE', '%' . $search . '%');
        })->with([
            'batch',
            'batch.crm_especialidades',
            'users.roles',
            'users.datos_generales'
        ])->get();

        return response()->json(["Busqueda" => $search, "Request" => $usuarios,  "auth" => $auth], 200);
    }


    public function vernopost()
    {
        $excludedIds = Crm_postulacion_forms::pluck('postulaciones_id');

        $postulaciones = Crm_postulaciones::with([
            'batch',
            'batch.crm_especialidades',
            'users.roles',
            'users.datos_generales'
        ])->whereNotIn('id', $excludedIds)->get();

        return response()->json(["Mensaje" => "Orden por no id", "datos" => $postulaciones], 200);
    }


    public function mostrarUsuarios(Request $request)
    {
        $search = $request->input('search');
        $query = User::with('datos_generales');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%$search%")
                    ->orWhereHas('datos_generales', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%$search%")
                            ->orWhere('apellido', 'like', "%$search%");
                    });
            });
        }


        $usuarios = $query->skip(1)->take(PHP_INT_MAX)->get();

        return response()->json(["mensaje" => "Datos cargados", "datos" => $usuarios], 200);
    }
    public function indexfull()
    {
        $postulaciones = Crm_postulaciones::with([
            'batch',
            'batch.crm_especialidades',
            'users.roles',
            'users.datos_generales'
        ])
            ->get();
        return response()->json(["mensaje" => "Datos cargados", "datos" => $postulaciones], 200);
    }
    public function showUser(string $user_id, string $batch_id)
    {
        $item = Crm_postulaciones::where('user_id', '=', $user_id)->where('batch_id', '=', $batch_id)->first();
        return response()->json(["mensaje" => "Registro cargado", "datos" => $item]);
    }
    public function storeFormularioWeb(Request $request)
    {
        $request->validate([
            "batch_id" => "required|max:15",
            "descripcion" => "nullable|max:250",
            "nivel_estudios" => "required|max:11",
            "nivel_academico" => "required|max:11",
            "nivel_programacion" => "required|max:11",
            "servicio_internet" => "required|max:11",
            "idioma_extranjero" => "required|max:11",
            "horario_trabajo" => "required|max:11",
            "comentario" => "nullable|max:250",
        ]);
        try {
            DB::beginTransaction();
            $item = new Crm_postulaciones();
            $item->user_id =   Auth::id();
            $item->batch_id = $request->batch_id;
            $item->estado = 'prueba';
            $item->descripcion = $request->descripcion;
            $item->save();
            $item2 = new Crm_postulacion_forms();
            $item2->postulaciones_id = $item->id;
            $item2->nivel_estudios = $request->nivel_estudios;
            $item2->nivel_academico = $request->nivel_academico;
            $item2->nivel_programacion = $request->nivel_programacion;
            $item2->servicio_internet = $request->servicio_internet;
            $item2->idioma_extranjero = $request->idioma_extranjero;
            $item2->horario_trabajo = $request->horario_trabajo;
            $item2->comentario = $request->comentario;
            $item2->save();
            DB::commit();
            return response()->json(["mensaje" => "Registro exitoso.", "datos" => $item], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["mensaje" => "Error: $th"], 500);
        }
    }
}
