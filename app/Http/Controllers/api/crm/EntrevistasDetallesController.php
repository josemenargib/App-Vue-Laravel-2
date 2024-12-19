<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_datos_generales;
use App\Models\Crm_entrevista_detalles;
use App\Models\Crm_entrevistas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntrevistasDetallesController extends Controller
{
    public function index()
    {
        $entrevistasdetalles = Crm_entrevista_detalles::with([
            'entrevistas',
            'users',
            'users.roles',
            'users.datos_generales',
            'entrevistas.postulaciones',
            'entrevistas.postulaciones.users',
            'entrevistas.postulaciones.users.datos_generales',
            'entrevistas.postulaciones.users.roles',
        ])
            ->select('Crm_entrevista_detalles.*')
            ->paginate(10);
        return response()->json(["mensaje" => "Datos cargados Entrevistasdetalles", "datos" => $entrevistasdetalles], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "entrevista_id" => "required|max:11",
            "user_id" => "required|max:50",
            "observacion" => "nullable|string|max:250",
        ]);
        $item = new Crm_entrevista_detalles();
        $item->entrevista_id = $request->entrevista_id;
        $item->user_id = $request->user_id;
        $item->observacion = $request->observacion;
        $item->save();

        if ($item->save()) {
            return response()->json(["mensaje" => "Registro exitoso.", "datos" => $item], 200);
        } else {
            return response()->json(["mensaje" => "No se pudo realizaar el registro."], 422);
        }
    }
    public function show(string $entrevistas_id)
    {
        $item = Crm_entrevista_detalles::with([
            'entrevistas',
            'entrevistas.postulaciones',
            'entrevistas.postulaciones.users',
            'entrevistas.postulaciones.users.datos_generales',
            'entrevistas.postulaciones.users.roles',
            'usuario.datos_generales', // Cargar datos generales del usuario // Cargar datos generales del usuario
        ])
            ->whereHas('entrevistas', function ($query) use ($entrevistas_id) {
                $query->where('id', $entrevistas_id);
            })
            ->get();
        return response()->json(["mensaje" => "Datos cargados Show", "datos" => $item], 200);
    }
    public function show2(string $id){
        $item = Crm_entrevista_detalles::with([
            'entrevistas.postulaciones', // Cargar la relación de postulaciones en entrevistas
            'entrevistas', // Cargar entrevistas
            'usuario.datos_generales', // Cargar datos generales del usuario // Cargar datos generales del usuario
        ])->find($id);
        return response()->json(["mensaje" => "Datos cargados Show2", "datos" => $item], 200);
    }
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "entrevistas_id" => "required|max:11",
            "user_id" => "required|max:11",
            "observacion" => "max:250"
        ]);
        $item = Crm_entrevista_detalles::find($id);
        $item->entrevistas_id = $request->entrevistas_id;
        $item->user_id = $request->user_id;
        $item->observacion = $request->observacion;
        if ($item->save()) {
            return response()->json(["mensaje" => "Registro exitoso.", "datos" => $item], 200);
        } else {
            return response()->json(["mensaje" => "No se pudo realizaar el registro."], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function buscaentrevistadores()
    {
        $excludedRoles = ['postulante', 'estudiante', 'egresado'];
        $entrevistadores = User::whereDoesntHave('roles', function($query) use ($excludedRoles) {
            $query->whereIn('name', $excludedRoles);
        })->with([
            'datos_generales',
            'roles'
        ])->paginate(3);
        return response()->json(['message' => 'Entrevistadores encontrados', 'datos' => $entrevistadores], 200);
    }

    public function buscapostulantes()
    {
        $entrevistadores = User::role('postulante')->with([
            'datos_generales',
            'roles'
        ])->paginate(3);
        return response()->json(['message' => 'Entrevistadores encontrados', 'datos' => $entrevistadores], 200);
    }
    public function searchpostulante(Request $request)
    {
        $search = $request->get('search');

        $usuarios = Crm_datos_generales::when($search, function ($query) use ($search) {
            $query->where('nombre', 'LIKE', '%' . $search . '%')->orWhere('apellido', 'LIKE', "%$search%")->orWhere('ci', 'LIKE', "%$search%");;
        })->get();
        $userIds = $usuarios->pluck('user_id');
        // Filtrar postulaciones basadas en los user_id obtenidos
        $postulaciones = User::whereIn('id', $userIds)->role('postulante')->with('datos_generales')->paginate(5);
        return response()->json(["mensaje" => "Datos cargados", "datos" => $postulaciones], 200);
    }
    public function searchentrevistadores(Request $request)
    {
        $search = $request->get('search');

        $usuarios = Crm_datos_generales::when($search, function ($query) use ($search) {
            $query->where('nombre', 'LIKE', '%' . $search . '%')->orWhere('apellido', 'LIKE', "%$search%")->orWhere('ci', 'LIKE', "%$search%");;
        })->get();
        $userIds = $usuarios->pluck('user_id');
        // Filtrar postulaciones basadas en los user_id obtenidos
        $postulaciones = User::whereIn('id', $userIds)->role('postulante')->with('datos_generales')->paginate(5);
        return response()->json(["mensaje" => "Datos cargados", "datos" => $postulaciones], 200);
    }
}
