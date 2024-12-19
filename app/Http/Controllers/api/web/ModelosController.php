<?php

namespace App\Http\Controllers\api\web;

use App\Models\Web_modelos;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Web_empresas;
use Illuminate\Support\Facades\Auth;

class ModelosController extends Controller
{

    public function index(){
        $modelos = Web_modelos::paginate(10);

        if ($modelos->total() === 0) {
            return response()->json(["mensaje" => "No existen modelos registrados"], 200);
        } else {
            return response()->json([
                "mensaje" => "Modelos cargados",
                "datos" => $modelos->getCollection()->transform(function($modelo) {
                    return [
                        'id' => $modelo->id,
                        'nombre' => $modelo->nombre,
                        'descripcion' => $modelo->descripcion,
                        'icono' => $modelo->icono,
                        'is_deleted' => $modelo->is_deleted,
                    ];
                }),
                "meta" => [
                    "current_page" => $modelos->currentPage(),
                    "last_page" => $modelos->lastPage(),
                    "per_page" => $modelos->perPage(),
                    "total" => $modelos->total()
                ]
            ], 200);
        }
    }

    /*public function index(){
    $modelos = Web_modelos::all();
        if ($modelos ->isEmpty()){
            return response()->json(["mensaje" => "No existe modelos registrados"], 200);
        }
        return response()->json(["mensaje" => "Modelos cargados", "datos" => $modelos], 200);
    }*/

    public function indexDisponibles(){
        $item = Web_modelos::where('is_deleted', false)->get();
        return response()->json(["mensaje"=>"Modelos disponibles cargados","datos"=>$item]);
    }

    public function store(Request $request)
    {
        if(Auth::user()->hasPermissionTo('modelo crear')){
            $request->validate([
                "nombre" => "required",
                "descripcion" => "required",
                "icono" => "required|mimes:png,jpg,jpeg",
            ], [
                "nombre.required" => "El título es requerido para registrar el modelo",
                "descripcion.required" => "La descripcion es requerida para registrar el modelo",
                "icono.required" => "El ícono es requerido para registrar el modelo",
                "icono.mimes" => "Formato de la imagen no permitido, formatos permitidos: PNG, JPG y JPEG",
            ]);
            $item = new Web_modelos();
            $empresa = Web_empresas::orderBy('id')->first();
            $item->empresa_id = $empresa->id;
            $item->nombre = $request->nombre;
            $item->descripcion = $request->descripcion;
            if ($request->file('icono') != null) {
                $file = $request->file('icono');
                $nombre_icono = time().'.png';
                $file->move("img/img-modelos/",$nombre_icono);
                $item->icono = $nombre_icono;
            }
            $item->save();
            if($item->save()){
                return response()->json(["mensaje" => "Registro exitoso", "datos" => $item], 200);
            }else{
                return response()->json(["mensaje"=>"No se pudo realizar el registro"],422);
            }
        }else{
            return response()->json(["mensaje"=>"No tienes permiso para realizar esta accion"],403);
        }
    }

    public function update(Request $request, string $id)
    {
        if (Auth::user()->hasPermissionTo('modelo editar')) {
            $request->validate([
                "nombre" => "required",
                "descripcion" => "required",
                "icono" => "mimes:png,jpg,jpeg",
            ], [
                "nombre.required" => "El título es requerido para editar el modelo",
                "descripcion.required" => "La descripcion es requerida para editar el modelo",
                "icono.mimes" => "Formato de la imagen no permitido, formatos permitidos: PNG, JPG y JPEG"
            ]);
            $item = Web_modelos::find($id);
            $item->nombre = $request->nombre;
            $item->descripcion = $request->descripcion;
            if ($request->file('icono')) {
                if($item->icono){
                    unlink('img/img-modelos/'.$item->icono);
                }
                $file = $request->file('icono');
                $nombre_icono = time().'.png';
                $file->move("img/img-modelos/",$nombre_icono);
                $item->icono = $nombre_icono;
            }
            $item->save();
            if($item->save()){
                return response()->json(["mensaje" => "Edición exitosa", "datos" => $item], 200);
            }else{
                return response()->json(["mensaje"=>"No se pudo realizar la edición del registro"],422);
            }
        }else{
            return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }
    public function show(string $id)
    {
        $item = Web_modelos::where('id',$id)->first();
        return response()->json(["mensaje"=>"Registro cargado","datos"=>$item]);
    }

    public function destroy(string $id)
    {
        if (Auth::user()->hasPermissionTo('modelo borrar')) {
            $item = Web_modelos::find($id);
            $item->is_deleted = !$item->is_deleted;
            if($item->save()){
                return response()->json(["mensaje"=>"Modelo eliminado","datos"=>$item],202);
            }
            else{
                return response()->json(["mensaje"=>"No se puede eliminar el modelos"],422);
            }
        }else{
            return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }
}
