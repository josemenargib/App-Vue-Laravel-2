<?php

namespace App\Http\Controllers\api\web;

use App\Http\Controllers\Controller;
use App\Models\Propuestas_empleos;
use Illuminate\Http\Request;

class PropuestasEmpleosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function showAll()
    {
        $propuestas = Propuestas_empleos::orderBy('fecha_limite_postulacion', 'asc')->paginate(10);
        return response()->json(["mensaje" => "Datos cargados", "datos" => $propuestas], 200);
    }

    public function showActives()
    {
        $propuestas = Propuestas_empleos::where('is_deleted', false)->orderBy('fecha_limite_postulacion', 'asc')->paginate(10);
        return response()->json(["mensaje" => "Datos cargados", "datos" => $propuestas], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'empresa' => 'required',
            'puesto' => 'required',
            'email' => 'required|email',
            'modalidad' => 'required',
            'descripcion_archivo' => [
                'nullable',
                'mimes:pdf,xlx,csv,doc,docx|max:2048'           
            ],
            'imagen_oferta' => [
                'nullable',
                'mimes:jpg,png,jpeg|max:2048'            
            ],
            'fecha_limite_postulacion' => 'required'
        ]);
        $propuesta = new Propuestas_empleos();
        if($request->hasFile('descripcion_archivo')) {
            $documento = $request->file('descripcion_archivo');
            $nombreDocumento = time().'.'.$request->file('descripcion_archivo')->getClientOriginalExtension();
            $documento->move('propuestas/documentos/', $nombreDocumento);
            $propuesta->descripcion_archivo = $nombreDocumento;
        }
        if($request->hasFile('imagen_oferta')) {
            $imagen = $request->file('imagen_oferta');
            $nombreImagen = time().'.png';
            $imagen->move('propuestas/imagenes/', $nombreImagen);
            $propuesta->imagen_oferta = $nombreImagen;
        }
        $propuesta->fill($request->only([
            'empresa',
            'email',
            'contacto',
            'puesto',
            'descripcion',
            'modalidad',
            'fecha_limite_postulacion',
        ]));
        $propuesta->save();
        return response()->json([
            'mensaje' => 'Registro exitoso',
            'datos' => $propuesta
        ], 200);      
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $propuesta = Propuestas_empleos::find($id);
        $propuesta->is_deleted = !$propuesta->is_deleted;
        if($propuesta->save()) {
            return response()->json([
                "mensaje" => "Estado modificado exitosamente.", 
                "datos" => $propuesta]
            , 200);
        }else {
            return response()->json([
                "mensaje" => "Error al modificar estado."]
            , 422);
        } 
    }
}
