<?php

namespace App\Http\Controllers\api\web;

use App\Http\Controllers\Controller;
use App\Models\Web_paginas;
use Illuminate\Http\Request;

class PaginasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $item=Web_paginas::get();
        return response()->json(["mensaje"=>"Datos cargados","datos"=>$item]);
    }

    public function indexDisponibles()
    {
        $item=Web_paginas::where('is_deleted',false)->get();
        return response()->json(["mensaje"=>$item]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "pagina" => "required|max:30|min:4|unique:web_paginas,pagina"
        ],[
            "pagina.required" => "El nombre de la página es necesario para registrarla",
            "pagina.max" => "El nombre de la página debe tener máximo 30 caracteres",
            "pagina.min" => "El nombre de la página debe tener como mínimo 4 caracteres",
            "pagina.unique" => "El nombre de la página ya se encuentra seregistrado"
        ]);
        $item = new Web_paginas();
        $item->pagina = $request->pagina;
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
        $item = Web_paginas::where('id',$id)->with('seccionesPagina')->with('seccionesPagina.seccion')->with('seccionesPagina.imagenes')->first();
        return response()->json(["mansaje"=>"Datos cargados","datos"=>$item],200);
    }

    public function showByName(Request $request)
    {
        $request->validate(
            [
                "nombre" => "required"
            ]
        );
        $nombre = $request->nombre;
        $item = Web_paginas::where('pagina','LIKE',$nombre)->with('seccionesPagina')->with('seccionesPagina.seccion')->with(['seccionesPagina.imagenes' => function ($query){
            $query->where('is_deleted',false);
        }])->first();
        return response()->json(["mansaje"=>"Datos cargados","datos"=>$item],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
        $request->validate([
            "pagina" => "required|max:30|min:4|unique:web_paginas,pagina,$id"
        ],[
            "pagina.required" => "El nombre de la página es necesario para registrarla",
            "pagina.max" => "El nombre de la página debe tener máximo 30 caracteres",
            "pagina.min" => "El nombre de la página debe tener como mínimo 4 caracteres",
            "pagina.unique" => "El nombre modificado de la página ya se encuentra seregistrado con otro identificador"
        ]);
        $item = Web_paginas::find($id);
        $item->pagina = $request->pagina;
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
        $item = Web_paginas::find($id);
        $item->is_deleted = !$item->is_deleted;
        if($item->save()){
            return response()->json(["mensaje"=>"Estado modificado","datos"=>$item],202);
        }
        else{
            return response()->json(["mensaje"=>"No se puede modificar el estado"],422);
        }
    }
}
