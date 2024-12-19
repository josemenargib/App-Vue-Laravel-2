<?php

namespace App\Http\Controllers\api\web;

use App\Http\Controllers\Controller;
use App\Models\Web_beneficios;
use App\Models\Web_empresas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BeneficiosController extends Controller
{
    public function index()
    {
        $item=Web_beneficios::orderBy('id', 'desc')->paginate(10);
        return response()->json(["mensaje" => "datos cargados", "datos" => $item]);
    }

    public function store(Request $request)
    {
        $request->validate([
            "tipo"=>"required",
            "descripcion"=>"required",
            "icono"=>"mimes:png,jpg,jpeg,ico|max:1024"
        ]);
        $item=new Web_beneficios();
        $empresa = Web_empresas::orderBy('id')->first();
        $item->empresa_id=$empresa->id;
        $item->tipo=$request->tipo;
        $item->descripcion=$request->descripcion;
        if ($request->hasFile('icono')) {
            $file=$request->file('icono');
            $nombreImagen=time().".png";
            $file->move('img/img_beneficios/',$nombreImagen);
            $item->icono=$nombreImagen;
        }
        $item->save();
        return response()->json(["mensaje" => "Datos cargados", "Datos" => $item], 200);
    }

    public function update(Request $request, string $id)
    {   
        $request->validate([
            "tipo"=>"required",
            "descripcion"=>"required",
            "icono"=>"mimes:png,jpg,jpeg,ico"
        ]);
        $item=Web_beneficios::find($id);
        $item->tipo=$request->tipo;
        $item->descripcion=$request->descripcion;
        if ($request->hasFile('icono')) {
           /* if($item->icono){                
                unlink(public_path('img/img_beneficios/'), $item->icono);
            }*/
            $file=$request->file("icono");
            $nombreImagen=time().".png";
            $file->move(public_path('img/img_postulacionPasos/'),$nombreImagen);
           // $file->move("img/img_beneficios/".$nombreImagen);
            $item->icono=$nombreImagen;
        }
        $item->save();
        return response()->json(["mensaje" => "Datos modificados", "Datos" => $item], 200);
    }

    public function destroy(string $id)
    {
        $item=Web_beneficios::find($id);
        $item->is_deleted=!$item->is_deleted;
        if($item->save()){
            return response()->json(["mensaje"=>"Estado modificado","datos"=>$item],202);
        }
        else{
            return response()->json(["mensaje"=>"No se pudo modificar el estado"],422);
        }
    }
    public function beneficiosActivos()
    {
        $item=Web_beneficios::where('is_deleted', false)->get(); 
        return response()->json(["mensaje" => "datos cargados", "datos" => $item]);
    }
    public function show(string $id)
    {
        $item = Web_beneficios::find($id);
        return response()->json(["mensaje" => "datos cargados", "datos" => $item]);
    } 
}
