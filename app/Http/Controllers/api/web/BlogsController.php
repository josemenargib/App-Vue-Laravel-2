<?php

namespace App\Http\Controllers\api\web;

use App\Http\Controllers\Controller;
use App\Models\Web_blogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlogsController extends Controller
{
    public function index(Request $request)
    {
        //Muestra todos ->GET + Buscador
        $search = $request->get('search');
        $item = Web_blogs::when($search, function($query) use ($search){
            $query->where('titulo','LIKE',"%$search%")->orWhere('contenido','LIKE',"%$search%");
        })->with('datos_users')->withCount('comentarios')->orderBy('id', 'desc')->paginate(10);
        return response()->json(["mensaje" => "Datos cargados", "datos" => $item], 200);
    }

    public function store(Request $request)
    {
        //Registra nuevos ->POST 
        $request->validate([
            "titulo" => "required|max:100",
            "contenido" => "required",
            "imagen" => "mimes:png,jpg,jpeg|max:255"
        ],[
            "titulo.required" => "Título del blog es requerido, registre por favor",
            "contenido.required" => "Contenido del blog es requerido, registre por favor",
            "imagen.mimes" => "El formato de la imagen no esta permitido, la imagen debe estar en un formato PNG, JPG y JPEG"
        ]);
        try {
            DB::beginTransaction();
            $item = new Web_blogs();
            $item->user_id = Auth::id();
            $item->titulo = $request->titulo;
            $item->contenido = $request->contenido;
            if ($request->file('imagen') != null) {
                $adjunto = $request->file('imagen');
                $nombre_imagen = time().'.png';
                $adjunto->move("img/img-blogs/",$nombre_imagen);
                $item->imagen = $nombre_imagen;
            } 
            $item->save();
            DB::commit();
            return response()->json(["mensaje" => "Registro exitoso", "datos" => $item], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["mensaje"=>"Error:$th"],422);
        }
    }

    public function show(string $id)
    {
        //Muestra mediante /id ->GET
        $item = Web_blogs::where('id', $id)->with('datos_users')->withCount('comentarios')->with('comentarios','comentarios.users')->get();
        return response()->json(["mensaje" => "Dato cargado", "datos" => $item], 200);
    }

    public function update(Request $request, string $id)
    {
        //Actualiza datos /id ->PUT
        $request->validate([
            "titulo" => "required|max:100",
            "contenido" => "required",
            "imagen" => "mimes:png,jpg,jpeg|max:255"
        ],[
            "titulo.required" => "Título del blog es requerido, registre por favor",
            "contenido.required" => "Contenido del blog es requerido, registre por favor",
            "imagen.mimes" => "El formato de la imagen no esta permitido, la imagen debe estar en un formato PNG, JPG y JPEG"
        ]);
        $item = Web_blogs::find($id);
        $item->titulo = $request->titulo;
        $item->contenido = $request->contenido;
        if ($request->file('imagen') != null) {
            if ($item->imagen){
                unlink('img/img-blogs/'.$item->imagen);
            }
            $adjunto = $request->file('imagen');
            $nombre_imagen = time().'.png';
            $adjunto->move("img/img-blogs/",$nombre_imagen);
            $item->imagen = $nombre_imagen;
        } 
        if($item->save()){
            return response()->json(["mensaje" => "Modificado", "datos" => $item], 201);
        }else{
            return response()->json(["mensaje" => "No se pudo modificar"], 422);
        }
    }

    public function destroy(string $id)
    {
        //Cambia estado /id ->DELETE
        $item = Web_blogs::find($id);
        $item->is_deleted = !$item->is_deleted;
        if($item->save()){
            return response()->json(["mensaje" => "Estado modificado", "datos" => $item], 202);
        }else{
            return response()->json(["mensaje" => "No se pudo modificar el estado"], 422);
        }
    }

    public function blogsActivos(){
        $item = Web_blogs::where('is_deleted', false)->with('datos_users')->withCount('comentarios')->orderBy('id', 'desc')->get();
        return response()->json(["mensaje" => "Datos cargados", "datos" => $item]);
    }
    public function blogsInactivos(){
        $item = Web_blogs::where('is_deleted', true)->with('datos_users')->withCount('comentarios')->orderBy('id', 'desc')->get();
        return response()->json(["mensaje" => "Datos cargados", "datos" => $item]);
    }

    public function vistas(string $id)
    {
        //Incrementa contador de vistas /id ->PUT
        $item = Web_blogs::find($id);
        $item->views += 1;
        if($item->save()){
            return response()->json(["mensaje" => "Registrado"], 201);
        }else{
            return response()->json(["mensaje" => "No se pudo registrar"], 422);
        }
    }

}
