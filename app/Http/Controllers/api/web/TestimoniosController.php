<?php

namespace App\Http\Controllers\api\web;

use App\Http\Controllers\Controller;
use App\Models\Web_testimonios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestimoniosController extends Controller
{
    public function index()
    {
        //Mustrar todo  -  GET 
        $item = Web_testimonios::orderBy("id","desc")->with("users")->with("users.datos_generales")->paginate(10);
        return response()->json(["mensaje" => "Datos Cargados", "datos" => $item], 200);
    }
    public function store(Request $request)
    {
        //Registra nuevos mediante formulario -  POST 
        $request->validate([
            "user_id"=>"required",
            "experiencia"=>"required"
        ],[
            "user_id.required"=>"El usuario ID es requerido",
            "experiencia.required"=>"El campo experiencia es requerido"
        ]);
        try {
            DB::beginTransaction();
                $item = new Web_testimonios();
                $item-> user_id = $request->user_id;
                $item-> experiencia = $request->experiencia;
                $item-> titulo = $request->titulo;
                $item-> save();
            DB::commit();
                return response()->json(["mensaje"=>"Registro exitoso", "datos"=>$item],200);
            
        } catch (\Throwable $th) {
            DB::rollBack();
                return response()->json(["mensaje"=>"Error:$th"],422);
        }
    }
    public function show(string $id)
    {
        //Muestra solo un registro mediante el id - GET
        $item = Web_testimonios::find($id);
        return response()->json(["mensaje" => "Datos Cargados", "datos" => $item], 200);
    }
    public function update(Request $request, string $id)
    {
        //Actualizar registros mediante formulario,id - PUT
        $request->validate([
            "user_id"=>"required",
            "experiencia"=>"required"
        ],[
            "user_id.required"=>"El usuario ID es requerido",
            "experiencia.required"=>"El campo experiencia es requerido"
        ]);
        try {
            DB::beginTransaction();
                $item = Web_testimonios::find($id);
                $item-> user_id = $request->user_id;
                $item-> experiencia = $request->experiencia;
                $item-> titulo = $request->titulo;
                $item-> save();
            DB::commit();
                return response()->json(["mensaje"=>"Modificado exitosamente", "datos"=>$item],200);
        } catch (\Throwable $th) {
            DB::rollBack();
                return response()->json(["mensaje"=>"Error:$th"],422);
        }
    }
    public function destroy(string $id)
    {
        //cambiamos el estado mediante el id - DELETE 
        $item = Web_testimonios::find($id);
        $item-> is_deleted = !$item->is_deleted;
        if ($item->save()) {
            return response()->json(["mensaje"=>"Estado modificado", "datos"=>$item],200);
        }else{
            return response()->json(["mensaje"=>"No se pudo modificar el estado"],422);
        }
    }
    public function testimoniosActivos()
    {
        //Mustrar todo  -  GET 
        $item = Web_testimonios::where("is_deleted",false)->with('datosUsuario')->orderBy("id","desc")->paginate(3);
        return response()->json(["mensaje" => "Datos Cargados", "datos" => $item], 200);
    }
}
