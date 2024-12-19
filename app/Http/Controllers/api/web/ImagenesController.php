<?php

namespace App\Http\Controllers\api\web;

use App\Http\Controllers\Controller;
use App\Models\Web_actividades;
use App\Models\Web_imagenes;
use Illuminate\Http\Request;

class ImagenesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $id)
    {
        $actividad = Web_actividades::find($id);
        $item = Web_imagenes::where('actividad_id',$actividad->id)->get();
        return response()->json(["mensaje"=>"Imagenes cargadas para la actividad","datos"=>$item]);
    }

    public function indexHabilitadas(string $id)
    {
        $actividad = Web_actividades::find($id);
        $item = Web_imagenes::where('actividad_id',$actividad->id)->where('is_deleted',false)->get();
        return response()->json(["mensaje"=>"Imagenes disponibles cargadas para la actividad","datos"=>$item]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,string $id)
    {
        $request->validate([
            "imagen" => "required|mimes:png,jpg,jpeg",
            "indice" => "required"
        ],[
            "imagen.required" => "Para registrar una nueva imagen en la actividad es necesario seleccionar una imagen",
            "imagen.mimes" => "El formato de la imagen no esta permitido, la imagen debe estar en un formato PNG, JPG y JPEG"
        ]);
        $actividad = Web_actividades::find($id);
        $item = new Web_imagenes();
        $item->actividad_id = $actividad->id;
        $file = $request->file('imagen');
        $nombreImagen = time().$request->indice.".png";
        $file->move('img/img_actividad/imagenes/',$nombreImagen);
        $item->url_imagenes = $nombreImagen;
        if($request->descripcion != null){
            $item->descripcion = $request->descripcion; 
        }
        $item->save();
        return response()->json(["mensaje"=>"Imagen agregada a la actividad","datos"=>$item]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Web_imagenes::where('id',$id)->with('actividad')->first();
        return response()->json(["mensaje"=>"Registro cargado","datos"=>$item]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "imagen" => "mimes:png,jpg,jpeg",
        ],[
            "imagen.mimes" => "El formato de la imagen no esta permitido, la imagen debe estar en un formato PNG, JPG y JPEG"
        ]);
        $item = Web_imagenes::find($id);
        //$item->imagen = $request->imagen;
        if($request->file('imagen')){
            unlink('img/img_actividad/imagenes/'.$item->url_imagenes);
            $file = $request->file('imagen');
            $nombreImagen = time().".png";
            $file->move('img/img_actividad/imagenes/',$nombreImagen);
            $item->url_imagenes = $nombreImagen;
        } 
        if($request->descripcion != null){
            $item->descripcion = $request->descripcion; 
        }
        $item->save();
        return response()->json(["mensaje"=>"Imagen modificada","datos"=>$item]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Web_imagenes::find($id);
        $item->is_deleted = !$item->is_deleted;
        if($item->save()){
            return response()->json(["mensaje"=>"Imagen eliminada","datos"=>$item],202);
        }
        else{
            return response()->json(["mensaje"=>"No se puede eliminar la imagen"],422);
        }
    }

    public function delete(string $id){
        $item = Web_imagenes::find($id);
        unlink('img/img_actividad/imagenes/'.$item->url_imagenes);
        if($item->delete()){
            return response()->json(["mensaje"=>"Imagen eliminada físicamente","datos"=>$item],200);
        }
        else{
            return response()->json(["mensaje"=>"No se puede eliminar la imagen"],422);
        }
    }
}
