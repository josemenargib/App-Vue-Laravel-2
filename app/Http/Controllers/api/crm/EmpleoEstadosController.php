<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_empleo_estados;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmpleoEstadosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $empleo_estados = Crm_empleo_estados::paginate(10);
        return response()->json(['mensaje' => "Información agregada", "datos" => $empleo_estados], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "curriculum" => "mimes:pdf",
            "url_portafolio" => "url",
            "url_linkedin" => "url",
            "url_github" => "url"
        ]);
        $item = new Crm_empleo_estados();
        $item->url_portafolio = $request->url_portafolio;
        $item->url_linkedin = $request->url_linkedin;
        $item->url_github = $request->url_github;
        $item->user_id = Auth::id();
        if($request->file('curriculum')){
            $curriculum = $request->file('curriculum');
            $nombreCurriculum = time() . '.pdf';
            $curriculum->move("Documentos/Curriculums/", $nombreCurriculum);
            $item->curriculum= $nombreCurriculum;
        }
        if($item->save()){
            return response()->json(["mensaje"=>"Registro correcto", "datos" =>$item], 200);
        }else{
            return response()->json(["mensaje"=>"No se pudo realizar el registro "], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $item = Crm_empleo_estados::where('user_id', Auth::id())->first();
        return response()->json(["mensaje" => "Dato en vista", "datos" =>$item], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            "curriculum" => "mimes:pdf",
            "url_portafolio" => "url",
            "url_linkedin" => "url",
            "url_github" => "url",
            "presentacion" => "required"
        ]);
        $item = Crm_empleo_estados::where('user_id', Auth::id())->first();
        $item->url_portafolio = $request->url_portafolio;
        $item->url_linkedin = $request->url_linkedin;
        $item->url_github = $request->url_github;
        $item->presentacion = $request->presentacion;
        $item->user_id = Auth::id();
        if($request->file('curriculum')){
            if ($item->curriculum) {
                unlink('Documentos/Curriculums/' . $item->curriculum);
            }
            $curriculum = $request->file('curriculum');
            $nombreCurriculum = time() . '.pdf';
            $curriculum->move("Documentos/Curriculums/", $nombreCurriculum);
            $item->curriculum= $nombreCurriculum;
        }
        if($item->save()){
            return response()->json(["mensaje"=>"Registro actualizado", "datos" =>$item], 201);
        }else{
            return response()->json(["mensaje"=>"No se pudo actualizar el registro "], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Crm_empleo_estados::find($id);
        $item->is_deleted = !$item->is_deleted;
        if($item->save()){
            return response()->json(["mensaje" => "Estado eliminado", "datos" =>$item], 202);
        }else{
            return response()->json(["mensaje" => "No se pudo eliminar el estado", "datos" =>$item], 422);
        }
    }

    public function empleoEstadosActivos (){
        $item = Crm_empleo_estados::where('is_deleted', true)->get();
        return response()->json(["mensaje" => "Datos cargados", "datos" =>$item]);
    }

    //egresados pagina web
    public function empleo_egresado(){
        $item = User::join('crm_datos_generales', 'users.id', '=', 'crm_datos_generales.user_id')
        ->join('crm_empleo_estados', 'users.id', '=', 'crm_empleo_estados.user_id')
        ->join('crm_postulaciones', 'users.id', '=', 'crm_postulaciones.user_id')
        ->join('crm_batchs', 'crm_postulaciones.batch_id', '=', 'crm_batchs.id')
        ->join('crm_especialidades', 'crm_batchs.especialidad_id', '=', 'crm_especialidades.id')
        ->where('users.is_deleted', false)
        ->where('crm_empleo_estados.is_deleted', false)
        ->where('crm_empleo_estados.estado', 'like', 'pendiente')
        ->select(
            'crm_datos_generales.user_id',
            'crm_datos_generales.nombre',
            'crm_datos_generales.apellido',
            'crm_datos_generales.telefono',
            'crm_datos_generales.pais',
            'crm_datos_generales.ciudad',
            'crm_datos_generales.direccion',
            'crm_datos_generales.foto_perfil',
            'crm_empleo_estados.curriculum',
            'crm_empleo_estados.presentacion',
            'crm_empleo_estados.url_portafolio',
            'crm_empleo_estados.url_linkedin',
            'crm_empleo_estados.url_github',
            'crm_especialidades.nombre AS especialidad'
        )->paginate(10);
        return response()->json(["mensaje"=>"Datos cargados","datos"=>$item]);
    }
}
