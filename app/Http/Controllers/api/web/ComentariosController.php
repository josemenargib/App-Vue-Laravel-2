<?php

namespace App\Http\Controllers\api\web;

use App\Http\Controllers\Controller;
use App\Models\Web_comentarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComentariosController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //Registra nuevos ->POST 
        $request->validate([
            "blog_id" => "required",
            "detalle" => "required",
            "adjunto" => "mimes:png,jpg|max:255"
        ],[
            "blog_id.required" => "Id blog es requerido, registre por favor",
            "detalle.required" => "Respuesta es requerida, registre por favor",
            "adjunto.mimes" => "El formato de la imagen no esta permitido, la imagen debe estar en un formato PNG, JPG y JPEG"
        ]);
        try {
            DB::beginTransaction();
            $item = new Web_comentarios();
            $item->user_id = Auth::id();
            $item->blog_id = $request->blog_id;
            $item->detalle = $request->detalle;
            if ($request->file('adjunto') != null) {
                $adjunto = $request->file('adjunto');
                $nombre_imagen = time().'.png';
                $adjunto->move("img/img-blogs/img-comentarios/",$nombre_imagen);
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
        $item = Web_comentarios::find($id);
        return response()->json(["mensaje" => "Dato cargado", "datos" => $item], 200);
    }

    public function update(Request $request, string $id)
    {
        //Actualiza datos /id ->PUT
        $request->validate([
            "detalle" => "required",
            "adjunto" => "mimes:png,jpg|max:255"
        ],[
            "detalle.required" => "Respuesta es requerida, registre por favor",
            "adjunto.mimes" => "El formato de la imagen no esta permitido, la imagen debe estar en un formato PNG, JPG y JPEG"
        ]);
        $item = Web_comentarios::find($id);
        if ($item->user_id <> Auth::id()){
            return response()->json(["mensaje" => "Usted no puede editar esta respuesta"], 422);
        } else{
            $item->detalle = $request->detalle;
            if ($request->file('adjunto') != null) {
                $adjunto = $request->file('adjunto');
                $nombre_imagen = time().'.png';
                $adjunto->move("img/img-blogs/img-comentarios/",$nombre_imagen);
                $item->imagen = $nombre_imagen;
            }            
            if ($item->save()) {
                return response()->json(["mensaje" => "Registro exitoso", "datos" => $item], 200);
            } else{
                return response()->json(["mensaje" => "No se realizo el registro"], 422);
            }
        }
        
    }

    public function destroy(string $id)
    {
        //Cambia estado /id ->DELETE
        $item = Web_comentarios::find($id);
        if ($item->user_id <> Auth::id()){
            return response()->json(["mensaje" => "Usted no puede editar esta respuesta"], 422);
        } else{
            $item->is_deleted = !$item->is_deleted;
            if($item->save()){
                return response()->json(["mensaje" => "Estado modificado", "datos" => $item], 202);
            }else{
                return response()->json(["mensaje" => "No se pudo modificar el estado"], 422);
            }
        }    
    }

    public function puntuar(Request $request, string $id){
        //Actualiza puntuacion /id ->PUT
        $request->validate([
            "puntuacion" => "required",
        ],[
            "puntuacion.required" => "Debe ingresar una puntuacion",
        ]);
        $item = Web_comentarios::find($id);
        if ($item->user_id <> Auth::id()){
            return response()->json(["mensaje" => "Usted no puede puntuar esta respuesta"], 422);
        } else{
            $item->puntuacion = $request->puntuacion;          
            if ($item->save()) {
                return response()->json(["mensaje" => "Puntuacion Registrada", "datos" => $item], 200);
            } else{
                return response()->json(["mensaje" => "No se realizo el registro"], 422);
            }
        }
    }

}
