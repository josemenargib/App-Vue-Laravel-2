<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_postulacion_forms;
use Illuminate\Http\Request;

class PostulacionFormsController extends Controller
{
    //Los estados que tendrá un usuario son los siguientes aprobado reprobado en proceso
    public function index(string $order)
    {
        $postulaciones = Crm_postulacion_forms::with([
            'postulaciones',
            'postulaciones.users',
            'postulaciones.users.datos_generales',
            'postulaciones.batch',
            'postulaciones.batch.crm_especialidades'
        ])
            ->paginate(7);
        return response()->json(["mensaje" => "Datos cargados", "datos" => $postulaciones], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            "postulaciones_id" => "required|max:11",
            "nivel_estudios" => "required|max:11",
            "nivel_academico" => "required|max:11",
            "nivel_programacion" => "required|max:11",
            "servicio_internet" => "required|max:11",
            "idioma_extranjero" => "required|max:11",
            "horario_trabajo" => "required|max:11",
            "comentario" => "nullable|max:250",
        ]);
        $item = new Crm_postulacion_forms();
        $item->postulaciones_id = $request->postulaciones_id;
        $item->nivel_estudios = $request->nivel_estudios;
        $item->nivel_academico = $request->nivel_academico;
        $item->nivel_programacion = $request->nivel_programacion;
        $item->servicio_internet = $request->servicio_internet;
        $item->idioma_extranjero = $request->idioma_extranjero;
        $item->horario_trabajo = $request->horario_trabajo;
        $item->comentario = $request->comentario;


        if ($item->save()) {
            return response()->json(["mensaje" => "Registro exitoso.", "datos" => $item], 200);
        } else {
            return response()->json(["mensaje" => "No se pudo realizaar el registro."], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $postulaciones_id)
    {

        $postulaciones = Crm_postulacion_forms::where('crm_postulacion_forms.postulaciones_id', $postulaciones_id)->with([
            'postulaciones',
            'postulaciones.users',
            'postulaciones.users.datos_generales',
            'postulaciones.batch',
            'postulaciones.batch.crm_especialidades'
        ])
            ->get();
        return response()->json(["mensaje" => $postulaciones_id, "datos" => $postulaciones], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "postulaciones_id" => "required|max:11",
            "nivel_estudios" => "required|max:11",
            "nivel_academico" => "required|max:11",
            "nivel_programacion" => "required|max:11",
            "servicio_internet" => "required|max:11",
            "idioma_extranjero" => "required|max:11",
            "horario_trabajo" => "required|max:11",
            "comentario" => "nullable|max:250",
        ]);
        $item = Crm_postulacion_forms::find($id);
        $item->postulaciones_id = $request->postulaciones_id;
        $item->nivel_estudios = $request->nivel_estudios;
        $item->nivel_academico = $request->nivel_academico;
        $item->nivel_programacion = $request->nivel_programacion;
        $item->servicio_internet = $request->servicio_internet;
        $item->idioma_extranjero = $request->idioma_extranjero;
        $item->horario_trabajo = $request->horario_trabajo;
        $item->comentario = $request->comentario;

        if ($item->save()) {
            return response()->json(["mensaje" => "Registro exitoso.", "datos" => $item], 200);
        } else {
            return response()->json(["mensaje" => "No se pudo realizaar el registro."], 422);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {}
}
