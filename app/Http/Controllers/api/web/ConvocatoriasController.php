<?php

namespace App\Http\Controllers\api\web;

use App\Http\Controllers\Controller;
use App\Models\Web_convocatorias;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Psr7\Query;
use Illuminate\Http\Request;
use Spatie\Permission\Traits\HasRoles;

class ConvocatoriasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $item = Web_convocatorias::with('batch')->when($search, function($query) use ($search){
            $query->whereHas('batch', function($query) use ($search){
                $query->where('version', 'LIKE', "%$search%");
            });
        })->orderBy('id', 'desc')->paginate(5);
        return response()->json(["mensaje" => "Datos cargados", "datos" => $item], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->hasPermissionTo('convocatoria crear')) {
            $request->validate([
                "batch_id" => "required",
                "fecha_inicio" => "required|date|before:fecha_fin",
                "fecha_fin" => "required|date|after:fecha_inicio",
                "url_imagen" => "required|nullable|mimes:png,jpg,jpeg|max:2048",
            ]);
            $nuevo_item = Web_convocatorias::orderBy('id', 'desc')->first();
            if($nuevo_item == null){
                $nombre = 1;
            }else{
                $nombre = $nuevo_item->id+1;
            }
            $item = new Web_convocatorias();
            $item->batch_id = $request->batch_id;
            $item->fecha_inicio = $request->fecha_inicio;
            $item->fecha_fin = $request->fecha_fin;
            if ($request->hasFile('url_imagen')) {
                $imagen = $request->file('url_imagen');
                $nombreImagen = $nombre.".png";
                $imagen->move('img/img_convocatorias/', $nombreImagen);
                $item->url_imagen = $nombreImagen;
            }
            if ($item->save()) {
                return response()->json(["mensaje" => "Se agrego el registro", "datos" => $item], 200);
            }else{
                return response()->json(["mensaje" =>"No se logro realizar el registro"], 422);
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
        $item = Web_convocatorias::where('id', $id)->with('batch')->orderBy('id', 'desc')->first();
        return response()->json(["mensaje" => "Dato cargado", "datos" => $item], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (Auth::user()->hasPermissionTo('convocatoria editar')) {
            try {
                $request->validate([
                    "batch_id" => "required",
                    "fecha_inicio" => "required|date",
                    "fecha_fin" => "required|date",
                    "url_imagen" => "required|nullable|mimes:png,jpg,jpeg|max:2048"
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json(["errors" => $e->errors()], 422);
            }
            $item = Web_convocatorias::find($id);
            $item->batch_id = $request->batch_id;
            $item->fecha_inicio = $request->fecha_inicio;
            $item->fecha_fin = $request->fecha_fin;
            if ($request->hasFile('url_imagen')) {
                $imagen = $request->file('url_imagen');
                $nombreImagen = $id.".png";
                $imagen->move('img/img_convocatorias/', $nombreImagen);
                $item->url_imagen = $nombreImagen;
            }
            if ($item->save()) {
                return response()->json(["mensaje" => "Se modifico el registro", "datos" => $item], 200);
            }else{
                return response()->json(["mensaje" =>"No se logro editar el registro"], 422);
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
        if (Auth::user()->hasPermissionTo('convocatoria editar')) {
            $item = Web_convocatorias::find($id);
            $item->is_deleted = !$item->is_deleted;
            if ($item->save()) {
                return response()->json(["mensaje" => "Estado modificado", "datos" => $item], 202);
            }else{
                return response()->json(["mensaje" =>"No se logro realizar la modificación"], 422);
            }
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }

    public function batchsActivos(){
        $item = Web_convocatorias::where('is_deleted', false)->with('batch.Crm_especialidades')->orderBy('id', 'desc')->get();
        return response()->json(["mensaje" => "Datos cargados", "datos" => $item]);
    }
}