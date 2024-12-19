<?php

namespace App\Http\Controllers\api\web;

use App\Http\Controllers\Controller;
use App\Models\Web_paginas_secciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaginasSeccionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pagina_id = $request->get('pagina');
        $seccion_id = $request->get('seccion');
        $item = Web_paginas_secciones::join('web_pagina_imagenes', 'web_pagina_imagenes.pagina_seccion_id', '=', 'web_paginas_secciones.id')->join('web_paginas', 'web_paginas_secciones.pagina_id', '=', 'web_paginas.id')
        ->join('web_secciones', 'web_paginas_secciones.seccion_id', '=', 'web_secciones.id')
        ->select(
            'web_paginas_secciones.id',
            DB::raw('COUNT(web_pagina_imagenes.id) AS cantidad_imagenes'),
            DB::raw("CASE 
                WHEN web_paginas_secciones.tipo_presentacion = 'c' THEN 'carrusel' 
                WHEN web_paginas_secciones.tipo_presentacion = 'e' THEN 'estatico' 
                ELSE '' 
            END AS tipo_presentacion"),
            'web_paginas.pagina',
            'web_secciones.seccion'
        )
        ->when($pagina_id, function ($query) use ($pagina_id){
            $query->where('web_paginas.id', '=', $pagina_id);
        })->when($seccion_id, function ($query) use ($seccion_id){
            $query->where('web_secciones.id', '=', $seccion_id);
        })->groupBy(
            DB::raw("CASE 
                WHEN web_paginas_secciones.tipo_presentacion = 'c' THEN 'carrusel' 
                WHEN web_paginas_secciones.tipo_presentacion = 'e' THEN 'estatico' 
                ELSE '' 
            END"),
            'web_paginas.pagina',
            'web_secciones.seccion',
            'web_paginas_secciones.id'
        )->paginate(10);
        return response()->json(["mensaje"=>"Datos cargados","datos"=>$item]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "pagina"=>"required|numeric",
            "seccion"=>"required|numeric",
            "tipo_presentacion"=>"required"
        ],[
            "pagina.required" => "Seleccione una página",
            "pagina.numeric" => "Seleccione una página valida",
            "seccion.required" => "Seleccione una sección",
            "seccion.numeric" => "Seleccione una sección valida",
            "tipo_presentacion.required" => "Seleccione la manera que se presentaran la imagenes en la sección elegida"
        ]);
        $item = new Web_paginas_secciones();
        $item->pagina_id = $request->pagina;
        $item->seccion_id = $request->seccion;
        $item->tipo_presentacion = $request->tipo_presentacion;
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
        $item = Web_paginas_secciones::where('id',$id)->with('pagina')->with('seccion')->with('imagenes')->first();
        return response()->json(["mansaje"=>"Datos cargados","datos"=>$item],200);
    }

    public function showSelected(Request $request)
    {
        $request->validate([
            "pagina"=>"required|numeric",
            "seccion"=>"required|numeric"
        ],[
            "pagina.required" => "Seleccione una página",
            "pagina.numeric" => "Seleccione una página valida",
            "seccion.required" => "Seleccione una sección",
            "seccion.numeric" => "Seleccione una sección valida"
        ]);
        $item = Web_paginas_secciones::where('pagina_id',$request->pagina)->where('seccion_id',$request->seccion)->with('pagina')->with('seccion')->with('imagenes')->first();
        return response()->json(["mansaje"=>"Datos cargados","datos"=>$item],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "tipo_presentacion"=>"required"
        ],[
            "tipo_presentacion.required" => "Seleccione la manera que se presentaran la imagenes en la sección elegida"
        ]);
        $item = Web_paginas_secciones::where('id',$id)->first();
        $item->tipo_presentacion = $request->tipo_presentacion;
        if($item->save()){
            return response()->json(["mensaje"=>"Datos modificados con exito","datos"=>$item],200);
        }
        else{
            return response()->json(["mansaje"=>"No se pudo modificar los datos"],422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
