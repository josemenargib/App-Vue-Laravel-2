<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_batchs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $item = Crm_batchs::orderBy("id", "desc")
            ->with('Crm_especialidades')
            ->whereHas('Crm_especialidades', function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->paginate(10);
        $item->transform(function ($item) {
            $item->imagen = asset('img/img_batchs/' . $item->imagen);
            return $item;
        });
        return response()->json(["mensaje" => "Datos cargados", "datos" => $item], 200);
    }
    public function batchsIndex(){
        $item = Crm_batchs::orderBy("id", "desc")->with('Crm_especialidades')->get();
        return response()->json(["mensaje" => "Datos cargados", "datos" => $item], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->hasPermissionTo('batch crear')) {
            $request->validate([
                "version" => "required|max:15",
                "fecha_inicio" => "required|date|after_or_equal:today", // Validate fecha_inicio to be a date and after or equal to today
                "fecha_fin" => "required|date|after:fecha_inicio", // Validate fecha_fin to be a date and after fecha_inicio
                "especialidad_id" => "required",
                "imagen" => "required|mimes:png,jpg,jpeg"
            ]);
            $item = new Crm_batchs();
            $item->version = $request->version;
            $item->fecha_inicio = $request->fecha_inicio;
            $item->fecha_fin = $request->fecha_fin;
            $item->descripcion = $request->descripcion;
            $item->requisitos = $request->requisitos;
            $item->especialidad_id = $request->especialidad_id;
            if ($request->file('imagen')) {
                if ($item->imagen) {
                    unlink('img/img_batchs/' . $item->imagen);
                }
                $imagen = $request->file('imagen');
                $nombreImagen = time() . '.png';
                $imagen->move("img/img_batchs/", $nombreImagen);
                $item->imagen = $nombreImagen;
            }
            if ($item->save()) {
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
        if (Auth::user()->hasPermissionTo('batch ver')) {
            $item = Crm_batchs::find($id);
            $imagen = asset('img/img_batchs/' . $item->imagen);
            return response()->json(["mensaje" => "Dato cargado", "datos" => $item, "imagen" => $imagen], 200);
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (Auth::user()->hasPermissionTo('batch editar')) {
            $request->validate([
                "version" => "required",
                "fecha_inicio" => "required|date|after_or_equal:today", // Validate fecha_inicio to be a date and after or equal to today
                "fecha_fin" => "required|date|after:fecha_inicio", // Validate fecha_fin to be a date and after fecha_inicio
                "especialidad_id" => "required",
            ]);
            $item = Crm_batchs::find($id);
            $item->version = $request->version;
            $item->fecha_inicio = $request->fecha_inicio;
            $item->fecha_fin = $request->fecha_fin;
            $item->descripcion = $request->descripcion;
            $item->requisitos = $request->requisitos;
            $item->especialidad_id = $request->especialidad_id;
            if ($request->file('imagen')) {
                if ($item->imagen) {
                    unlink('img/img_batchs/' . $item->imagen);
                }
                $imagen = $request->file('imagen');
                $nombreImagen = time() . '.png';
                $imagen->move("img/img_batchs/", $nombreImagen);
                $item->imagen = $nombreImagen;
            }
            if ($item->save()) {
                $imageUrl = asset('img/img_especialidad/' . $item->imagen);
                return response()->json(["mensaje" => "Registro modificado con exito", "datos" => $item, "imagen" => $imageUrl], 200);
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
        if (Auth::user()->hasPermissionTo('batch borrar')) {
            $item = Crm_batchs::find($id);
            $item->is_deleted = !$item->is_deleted;
            if ($item->save()) {
                return response()->json(["mensaje" => "Estado modificado", "datos" => $item], 202);
            } else {
                return response()->json(["mensaje" => "No se pudo modifcar el estado"], 422);
            }
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }
    public function batchsActivos()
    {
        $items = Crm_batchs::where('is_deleted', false)
            ->with('Crm_especialidades')
            ->orderBy('id', 'desc') 
            ->get();
        $items->transform(function ($item) {
            $imagenPath = 'img/img_batchs/' . $item->imagen;
            $item->imagen = file_exists($imagenPath) ? asset($imagenPath) : null;
    
            // Apply the same image existence check to the Crm_especialidades relationship
            if ($item->Crm_especialidades) {
                $imagenPathEspecialidad = 'img/img_especialidad/' . $item->Crm_especialidades->imagen;
                $item->Crm_especialidades->imagen = file_exists($imagenPathEspecialidad) ? asset($imagenPathEspecialidad) : null;
            }
    
            return $item;
        });
        return response()->json(["mensaje" => "Datos cargados", "datos" => $items]);
    }
    public function batchsEspecialidad(string $id)
    {
        $items = Crm_batchs::where('is_deleted', false)
            ->where('especialidad_id','=',$id)
            ->orderBy('id', 'desc') 
            ->get();
        $items->transform(function ($item) {
            $imagenPath = 'img/img_batchs/' . $item->imagen;
            $item->imagen = file_exists($imagenPath) ? asset($imagenPath) : null;
            return $item;
        });
        return response()->json(["mensaje" => "Datos cargados", "datos" => $items]);
    }
}
