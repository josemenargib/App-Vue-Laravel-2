<?php

namespace App\Http\Controllers\api\web;

use App\Http\Controllers\Controller;
use App\Models\Web_pagina_imagenes;
use App\Models\Web_paginas_secciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaginaImagenesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(Request $request)
    {
        $pagina_id = $request->get('pagina');
        $seccion_id = $request->get('seccion');
        $item = Web_pagina_imagenes::join('web_paginas_secciones', 'web_pagina_imagenes.pagina_seccion_id', '=', 'web_paginas_secciones.id')
        ->join('web_paginas', 'web_paginas_secciones.pagina_id', '=', 'web_paginas.id')
        ->join('web_secciones', 'web_paginas_secciones.seccion_id', '=', 'web_secciones.id')
        ->select(
            'web_pagina_imagenes.detalle',
            'web_pagina_imagenes.url_imagen',
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
        })->get();
        return response()->json(["mensaje"=>"Datos cargados","datos"=>$item]);
        /*where(function ($query) {
            $query->where('web_paginas.id', '=', 1)
                ->orWhere('web_secciones.id', '=', 1);
        })*/
        
        /*$item = Web_pagina_imagenes::when($pagina, function ($query) use ($pagina){
            $query->where('titulo','LIKE',"%$search%")->orWhere('detalle','LIKE',"%$search%");
        })->with('imagenes')->paginate(10);
        return response()->json(["mensaje"=>"Datos cargados","datos"=>$item]);*/
    }

    public function indexDisponibles(Request $request)
    {
        $pagina_id = $request->get('pagina');
        $seccion_id = $request->get('seccion');
        $item = Web_pagina_imagenes::join('web_paginas_secciones', 'web_pagina_imagenes.pagina_seccion_id', '=', 'web_paginas_secciones.id')
        ->join('web_paginas', 'web_paginas_secciones.pagina_id', '=', 'web_paginas.id')
        ->join('web_secciones', 'web_paginas_secciones.seccion_id', '=', 'web_secciones.id')
        ->select(
            'web_pagina_imagenes.detalle',
            'web_pagina_imagenes.url_imagen',
            DB::raw("CASE 
            WHEN web_paginas_secciones.tipo_presentacion = 'c' THEN 'carrusel' 
            WHEN web_paginas_secciones.tipo_presentacion = 'e' THEN 'estatico' 
            ELSE '' 
        END AS tipo_presentacion"),
            'web_paginas.pagina',
            'web_secciones.seccion'
        )
        ->where('web_pagina_imagenes.is_deleted',false)->when($pagina_id, function ($query) use ($pagina_id){
            $query->where('web_paginas.id', '=', $pagina_id);
        })->when($seccion_id, function ($query) use ($seccion_id){
            $query->where('web_secciones.id', '=', $seccion_id);
        })->get();
        return response()->json(["mensaje"=>"Datos cargados","datos"=>$item]);
    }

    public function indexNoDisponibles(Request $request)
    {
        $pagina_id = $request->get('pagina');
        $seccion_id = $request->get('seccion');
        $item = Web_pagina_imagenes::join('web_paginas_secciones', 'web_pagina_imagenes.pagina_seccion_id', '=', 'web_paginas_secciones.id')
        ->join('web_paginas', 'web_paginas_secciones.pagina_id', '=', 'web_paginas.id')
        ->join('web_secciones', 'web_paginas_secciones.seccion_id', '=', 'web_secciones.id')
        ->select(
            'web_pagina_imagenes.detalle',
            'web_pagina_imagenes.url_imagen',
            DB::raw("CASE 
            WHEN web_paginas_secciones.tipo_presentacion = 'c' THEN 'carrusel' 
            WHEN web_paginas_secciones.tipo_presentacion = 'e' THEN 'estatico' 
            ELSE '' 
        END AS tipo_presentacion"),
            'web_paginas.pagina',
            'web_secciones.seccion'
        )
        ->where('web_pagina_imagenes.is_deleted',true)->when($pagina_id, function ($query) use ($pagina_id){
            $query->where('web_paginas.id', '=', $pagina_id);
        })->when($seccion_id, function ($query) use ($seccion_id){
            $query->where('web_secciones.id', '=', $seccion_id);
        })->get();
        return response()->json(["mensaje"=>"Datos cargados","datos"=>$item]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $id)
    {
        $request->validate([
            "imagen"=>"required|mimes:png,jpg,jpeg",
            "indice"=>"required|numeric"
        ],[
            "imagen.required"=>"Un imagen es necesaria para registrarla en la base de datos",
            "imagen.mimes"=>"La imagen seleccionada tiene el formato inconrrecto, seleccione una imagen con extension: png, jpg, jpeg"
        ]);
        $item = new Web_pagina_imagenes();
        $item->pagina_seccion_id=$id;
        if($request->file('imagen')){
            $file = $request->file('imagen');
            $nombreImagen = time().$request->indice.".png";
            $file->move('img/img_pagina/',$nombreImagen);
            $item->url_imagen = $nombreImagen;
        }
        if($request->detalle){
            $item->detalle = $request->detalle;
        }
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
        $item = Web_pagina_imagenes::where('id',$id)->with('paginaSeccion.pagina')->with('paginaSeccion.seccion')->first();
        return response()->json(["mensaje"=>"Registro cargado","datos"=>$item]);
    }

    public function showPaginaSeccion(string $id){
        $item = Web_pagina_imagenes::where('pagina_seccion_id',$id)->get();
        return response()->json(["mensaje" => "Registros cargados", "datos" => $item]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,string $id)
    {
        $request->validate([
            "imagen"=>"mimes:png,jpg,jpeg"
        ],[
            "imagen.mimes"=>"La imagen seleccionada tiene el formato inconrrecto, seleccione una imagen con extension: png, jpg, jpeg"
        ]);
        $item = Web_pagina_imagenes::find($id);
        if($request->file('imagen')){
            if($item->url_imagen){
                unlink('img/img_pagina/',$item->url_imagen);
            }
            $file = $request->file('imagen');
            $nombreImagen = time().".png";
            $file->move('img/img_pagina/',$nombreImagen);
            $item->url_imagen = $nombreImagen;
        }
        if($request->detalle){
            $item->detalle = $request->detalle;
        }
        if($item->save()){
            return response()->json(["mensaje"=>"Modificación exitosa","datos"=>$item],200);
        }
        else{
            return response()->json(["mansaje"=>"No se pudo realizar la modificacion"],422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Web_pagina_imagenes::find($id);
        $item->is_deleted = !$item->is_deleted;
        if($item->save()){
            return response()->json(["mensaje"=>"Imagen deshabilitada","datos"=>$item],202);
        }
        else{
            return response()->json(["mensaje"=>"No se puede eliminar la actividad"],422);
        }
    }

    public function delete(string $id){
        $item = Web_pagina_imagenes::find($id);
        unlink('img/img_pagina/'.$item->url_imagen);
        if($item->delete()){
            return response()->json(["mensaje"=>"Imagen eliminada físicamente","datos"=>$item],200);
        }
        else{
            return response()->json(["mensaje"=>"No se puede eliminar la imagen"],422);
        }
    }
}
