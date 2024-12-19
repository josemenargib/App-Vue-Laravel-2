<?php

namespace App\Http\Controllers\api\web;

use App\Http\Controllers\Controller;
use App\Models\Web_actividades;
use App\Models\Web_empresas;
use App\Models\Web_imagenes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActividadesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $item = Web_actividades::when($search, function ($query) use ($search){
            $query->where('titulo','LIKE',"%$search%")->orWhere('detalle','LIKE',"%$search%");
        })->with('imagenes')->orderBy('id','desc')->paginate(10);
        return response()->json(["mensaje"=>"Datos cargados","datos"=>$item]);
    }

    public function indexDisponibles(Request $request)
    {
        $search = $request->get('search');
        $item = Web_actividades::where('is_deleted',false)->when($search, function ($query) use ($search){
            $query->where('titulo','LIKE',"%$search%")->orWhere('detalle','LIKE',"%$search%");
        })->with(['imagenes' => function ($query){
            $query->where('is_deleted',false);
        }])->orderBy('id','desc')->paginate(10);
        return response()->json(["mensaje"=>"Datos cargados","datos"=>$item]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //return response($request);
        $request->validate([
            "titulo" => "required",
            "detalle" => "required",
            "banner" => "mimes:png,jpg,jpeg",
            "fecha" => "date_format:Y-m-d"
        ],[
            "titulo.required" => "El título es requerido para registrar una nueva actividad",
            "detalle.required" => "Un detalle que describa la actividad es necesario para registrar una nueva actividad",
            "banner.mimes" => "El formato de la imagen no esta permitido, la imagen debe estar en un formato PNG, JPG y JPEG",
            "fecha.date_format" => "El formato de la fecha tiene que ser AAAA-MM-DD"
        ]);
        try {
            DB::beginTransaction();
            //1)Crear la actividad
            $empresa = Web_empresas::orderBy('id')->first();
            $item = new Web_actividades();
            $item->empresa_id = $empresa->id;
            $item->titulo = $request->titulo;
            $item->detalle = $request->detalle;
            if($request->file('banner')){
                $file = $request->file('banner');
                $nombreImagen = time().".png";
                $file->move('img/img_actividad/banners/',$nombreImagen);
                $item->url_banner = $nombreImagen;
            }
            if($request->fecha != null){
                $item->fecha = $request->fecha;
            }
            $item->save();
            /*if($request->imagenes){
                $imagenes = json_decode($request->imagenes);
                foreach($imagenes as $imagen){
                    if($imagen->file('imagen')){
                        $item2 = new Web_imagenes();
                        $item2->actividad_id = $item->id;
                        $file_imagen = $imagen->file('imagen');
                        $nombreImagen_imagen = time().".png";
                        $file_imagen->move('img/img_actividad/imagenes/',$nombreImagen_imagen);
                        $item2->url_imagenes = $nombreImagen_imagen;
                        if($imagen->descripcion != null){
                            $item2->descripcion = $imagen->descripcion;
                        }
                        $item2->save();
                    }
                }
            }*/
            DB::commit();
            return response()->json(["mensaje"=>"Registro exitoso","datos"=>$item],200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["mensaje"=>"Error:$th"],422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Web_actividades::where('id',$id)->with('imagenes')->first();
        return response()->json(["mensaje"=>"Registro cargado","datos"=>$item]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //return response()->json($id);
        $request->validate([
            "titulo" => "required",
            "detalle" => "required",
            "banner" => "mimes:png,jpg,jpeg",
            "fecha" => "date_format:Y-m-d"
        ],[
            "titulo.required" => "El título es requerido para editar una nueva actividad",
            "detalle.required" => "Un detalle que describa la actividad es necesario para editar una nueva actividad",
            "banner.mimes" => "El formato de la imagen no esta permitido, la imagen debe estar en un formato PNG, JPG y JPEG",
            "fecha.date_format" => "El formato de la fecha tiene que ser AAAA-MM-DD"
        ]);

        $item = Web_actividades::find($id);
        $item->titulo = $request->titulo;
        $item->detalle = $request->detalle;
        if($request->banner != null){
            if($request->banner!=$item->url_banner && $request->file('banner')){
                if($item->url_banner){
                    unlink('img/img_actividad/banners/'.$item->url_banner);
                }
                $file = $request->file('banner');
                $nombreImagen = time().".png";
                $file->move('img/img_actividad/banners/',$nombreImagen);
                $item->url_banner = $nombreImagen;
            }
        }
        if($request->fecha != null){
            $item->fecha = $request->fecha;
        }
        $item->save();
        return response()->json(["mensaje"=>"Registro exitoso","datos"=>$item],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Web_actividades::find($id);
        $item->is_deleted = !$item->is_deleted;
        if($item->save()){
            return response()->json(["mensaje"=>"Actividad eliminada","datos"=>$item],202);
        }
        else{
            return response()->json(["mensaje"=>"No se puede eliminar la actividad"],422);
        }
    }
}
