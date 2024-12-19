<?php

namespace App\Http\Controllers\api\web;

use App\Http\Controllers\Controller;
use App\Models\Web_secciones;
use Illuminate\Http\Request;

class SeccionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $item=Web_secciones::get();
        return response()->json(["mensaje"=>"Datos cargados", "datos"=>$item]);
    }

    public function indexDisponibles()
    {
        $item=Web_secciones::where('is_deleted',false)->get();
        return response()->json(["mensaje"=>$item]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "seccion" => "required|max:30|min:4|unique:web_secciones,seccion"
        ],[
            "seccion.required" => "El nombre de la sección es necesario para registrarla",
            "seccion.max" => "El nombre de la sección debe tener máximo 30 caracteres",
            "seccion.min" => "El nombre de la sección debe tener como mínimo 4 caracteres",
            "seccion.unique" => "El nombre de la sección ya se encuentra seregistrado"
        ]);
        $item = new Web_secciones();
        $item->seccion = $request->seccion;
        if($item->save()){
            return response()->json(["mensaje"=>"Registro exitoso","datos"=>$item],200);
        }
        else{
            return response()->json(["mansaje"=>"No se pudo realizar el registro"],422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Web_secciones::find($id);
        return response()->json(["mansaje"=>"Datos cargados","datos"=>$item],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "seccion" => "required|max:30|min:4|unique:web_secciones,seccion,$id"
        ],[
            "seccion.required" => "El nombre de la página es necesario para registrarla",
            "seccion.max" => "El nombre de la página debe tener máximo 30 caracteres",
            "seccion.min" => "El nombre de la página debe tener como mínimo 4 caracteres",
            "seccion.unique" => "El nombre modificado de la página ya se encuentra seregistrado con otro identificador"
        ]);
        $item = Web_secciones::find($id);
        $item->seccion = $request->seccion;
        if($item->save()){
            return response()->json(["mensaje"=>"Datos modificados con exito","datos"=>$item],200);
        }
        else{
            return response()->json(["mansaje"=>"No se pudo modificar el registro"],422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Web_secciones::find($id);
        $item->is_deleted = !$item->is_deleted;
        if($item->save()){
            return response()->json(["mensaje"=>"Estado modificado","datos"=>$item],202);
        }
        else{
            return response()->json(["mensaje"=>"No se puede modificar el estado"],422);
        }
    }
}
