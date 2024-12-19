<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_tipo_pruebas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TipoPruebasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $datos = Crm_tipo_pruebas::orderBy('id', 'desc')->paginate(5);
        return response()->json(['mensaje' => 'datos cargados', 'datos' => $datos]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->hasPermissionTo('tipo pruebas crear')) {

            $request->validate([
                "nombre" => "required"
            ]);

            $item = new Crm_tipo_pruebas;
            $item->nombre = $request->nombre;
            $item->save();

            return response()->json(['mensaje' => 'tipo de prueba cargado', "datos" => $item],200);
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (Auth::user()->hasPermissionTo('tipo pruebas ver')) {
            $datos = Crm_tipo_pruebas::find($id);
        return response()->json(['mensaje' => 'datos cargados', $datos], 200);
        } else {
                    return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
                }
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (Auth::user()->hasPermissionTo('tipo pruebas editar')) {

            $request->validate([
            "nombre" => "required"
        ]);

        $item = Crm_tipo_pruebas::find($id);
        $item->nombre = $request->nombre;
        $item->save();

        if ($item->save()) {
            return response()->json(['mensaje' => 'registro modificado exitosamente', 'datos' => $item], 200);
        } else {
            return response()->json(['mensaje' => 'datos incompletos o incorrectos'], 400);
        }
        } else {
                    return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
                }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (Auth::user()->hasPermissionTo('tipo pruebas borrar')) {

            $datos = Crm_tipo_pruebas::find($id);
        $datos->is_deleted = !$datos->is_deleted;
        $datos->save();
        if ($datos->save()) {
            return response()->json(['mensaje' => 'Se ha modificado el estado correctamente', 'datos' => $datos], 200);
        } else {
            return response()->json(['mensaje' => 'No pudimos modificar el estado correctamente'], 400);
        }
        } else {
                    return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
                }

        
    }

    public function obtenerTodosTiposPruebas()
    {
        $datos = Crm_tipo_pruebas::orderBy('id', 'desc')->get();
        return response()->json(['mensaje' => 'datos cargados', 'datos' => $datos]);
    }

    public function buscar(Request $request)
    {
        $usuarioAutenticado = auth()->id();
        if ($usuarioAutenticado) {
            $search = $request->get('search');

            $item = Crm_tipo_pruebas::when($search, function ($query) use ($search) {
                $query->where('nombre', 'LIKE', "%$search%");
            })->get();
            return response()->json(['mensaje' => 'Datos cargados', 'datos' => $item], 200);
        } else {
            return response()->json("No autenticado,inicie sesion");
        }
    }
}
