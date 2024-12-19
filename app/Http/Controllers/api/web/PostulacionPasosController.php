<?php

namespace App\Http\Controllers\api\web;

use App\Http\Controllers\Controller;
use App\Models\Web_empresas;
use App\Models\Web_postulacion_pasos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostulacionPasosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $postulacionPasos = Web_postulacion_pasos::when($search, function($query) use ($search){
            $query->where('nombre','LIKE',"%$search%");
        })->orderBy('numero_paso', 'asc')->paginate(4);
        $postulacionPasos->transform(function ($postulacionPasos) {
            $postulacionPasos->icono = asset('img/img_postulacionPasos/' . $postulacionPasos->icono);
            return $postulacionPasos;
        });
        return response()->json(["mensaje" => "Datos cargados", "datos" => $postulacionPasos], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->hasPermissionTo('paso postulacion crear')) {
            $request->validate([
                "nombre" => "required|max:50",
                "descripcion" => "required",
                "icono" => "mimes:jpeg,png,jpg,gif,svg|max:2048",
                "numero_paso" => "required|integer|max:99999"
            ], [
                "nombre.required" => "El título es requerido para registrar el modelo",
                "descripcion.required" => "La descripcion es requerida para realizar nuevo registro",
                "numero_paso.required" => "El numero de paso es requerido para realizar nuevo registro",
                "icono.mimes" => "Formato de la imagen no permitido, formatos permitidos: PNG, JPG y JPEG"
            ]);
            $empresa = Web_empresas::orderBy('id')->first();
            $item = new Web_postulacion_pasos();
            $item->empresa_id = $empresa->id;
            $item->nombre = $request->nombre;
            $item->descripcion = $request->descripcion;
            if($request->hasFile('icono')){
                if($item->icono){
                    unlink('img/img_postulacionPasos/'. $item->icono);
                }
                $file = $request->file('icono');
                $nombreImagen = time().'.png';
                $file->move('img/img_postulacionPasos/',$nombreImagen);
                $item->icono = $nombreImagen;
            }
            $item->numero_paso = $request->numero_paso;
            if($item->save()){
                return response()->json(["mensaje" => "Registro exitoso", "datos" => $item], 200);
            } else {
                return response()->json(["mensaje" => "No se pudo realizar el registro"], 422);
            }
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (Auth::user()->hasPermissionTo('paso postulacion ver')) {
            $item = Web_postulacion_pasos::find($id);
            $icono = asset('img/img_postulacionPasos/' . $item->icono);
            return response()->json(["mensaje" => "Dato cargado", "datos" => $item, 'icono' => $icono], 200);
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (Auth::user()->hasPermissionTo('paso postulacion editar')) {
            $request->validate([
                "nombre" => "required|max:50",
                "descripcion" => "required",
                "icono" => "mimes:jpeg,png,jpg,gif,svg|max:2048",
                "numero_paso" => "required|integer|max:99999"
            ], [
                "nombre.required" => "El título es requerido para registrar el modelo",
                "descripcion.required" => "La descripcion es requerida para realizar nuevo registro",
                "numero_paso.required" => "El numero de paso es requerido para realizar nuevo registro",
                "icono.mimes" => "Formato de la imagen no permitido, formatos permitidos: PNG, JPG y JPEG"
            ]);
            $item = Web_postulacion_pasos::find($id);
            $item->nombre = $request->nombre;
            $item->descripcion = $request->descripcion;
            if($request->hasFile('icono')){
                if($item->icono){
                    unlink('img/img_postulacionPasos/'. $item->icono);
                }
                $file = $request->file('icono');
                $nombreImagen = time().'.png';
                $file->move(public_path('img/img_postulacionPasos/'),$nombreImagen);
                $item->icono = $nombreImagen;
            }
            $item->numero_paso = $request->numero_paso;
            if($item->save()){
                $imageUrl = asset('img/img_postulacionPasos/' . $item->file);
                return response()->json(["mensaje" => "Registro modificado", "datos" => $item, "imagen_url" => $imageUrl], 200);
            } else {
                return response()->json(["mensaje" => "No se pudo realizar la modificacion"], 422);
            }
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (Auth::user()->hasPermissionTo('paso postulacion borrar')) {
            $item = Web_postulacion_pasos::find($id);
            $item->is_deleted = !$item->is_deleted;
            if($item->save()){
                return response()->json(["mensaje"=>"Estado modificado","datos"=>$item],202);
            }
            else{
                return response()->json(["mensaje"=>"No se pudo modificar el estado"],422);
            }
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }
    public function postulacionPasosActivos(){
        $item = Web_postulacion_pasos::where('is_deleted', false)->get();
        return response()->json(["mensaje" => "Datos cargados", "datos" => $item]);
    }
}
