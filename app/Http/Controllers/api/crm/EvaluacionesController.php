<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_batchs;
use App\Models\Crm_evaluaciones;
use App\Models\Crm_registros;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluacionesController extends Controller
{

    public function index()
    {
        $batches = Crm_batchs::where('is_deleted', false)
        ->orderBy('id', 'desc') 
        ->get();
        return response()->json([
            'mensaje' => 'OK',
            'datos' => $batches
        ]);
    }

    public function indexEvaluaciones()
    {
        
        $evaluaciones = Crm_evaluaciones::with('modulo','tipo_prueba','registro')
            ->orderBy('id', 'desc')
            ->paginate(10);
    
        return response()->json([
            "mensaje" => "Datos cargados",
            "datos" => $evaluaciones
        ], 200);
    
    }

    public function store(Request $request, $id)
    {
        if (AutH::user()->hasPermissionTo('evaluacion crear')) {
            
            $request->validate([
                'modulo_id' => 'required',
                'tipo_prueba_id' => 'required',
                'puntaje' => 'required|numeric|min:0|max:100',
            ]);
    
            if (!Crm_registros::find($id)) {
                return response()->json(["mensaje" => "Registro no encontrado"], 404);
            }
    
            $evaluacion = new Crm_evaluaciones();
            $evaluacion->registro_id = $id;
            $evaluacion->modulo_id = $request->modulo_id;
            $evaluacion->tipo_prueba_id = $request->tipo_prueba_id;
            $evaluacion->puntaje = $request->puntaje;
            $evaluacion->save();
        
            return response()->json(["mensaje" => "Registro exitoso", "datos" => $evaluacion], 201);
        } else {
            return response()->json(["mensaje" => "No tienes permiso para realizar esta acción"], 403);
        }
    }

    public function show( string $id)
    {
        $evaluacion = Crm_evaluaciones::with('modulo','tipo_prueba','registro')->find($id);
        if ($evaluacion) {
            return response()->json(["mensaje" => "Datos cargados", "datos" => $evaluacion]);
        }else{
            return response()->json(["mensaje" => "Evaluacion No encontrada"],404);
        }
        
    }
    public function update(Request $request, $id)
    {
        if (Auth::user()->hasPermissionTo('evaluacion editar')) {
            
            $request->validate([
                'modulo_id' => 'required',
                'tipo_prueba_id' => 'required',
                'puntaje' => 'required|numeric|min:0|max:100',
            ]);
        
            $evaluacion = Crm_evaluaciones::find($id);
            if (!$evaluacion) {
                return response()->json(["mensaje" => "Evaluación no encontrada"], 404);
            }
        
            $evaluacion->modulo_id = $request->modulo_id;
            $evaluacion->tipo_prueba_id = $request->tipo_prueba_id;
            $evaluacion->puntaje = $request->puntaje;
            $evaluacion->save();
        
            return response()->json(["mensaje" => "Modificación exitosa", "datos" => $evaluacion], 202);
        } else {
            return response()->json(["mensaje" => "No tienes permiso para realizar esta acción"], 403);
        }
    }
    public function show_actives(){
        $evaluacion = Crm_evaluaciones::where('is_deleted',false)->paginate(10);
        return response()->json(["mensaje" => "Datos cargados", "datos" => $evaluacion],200);
    }
    public function showByRegistroId(string $registroId)
    {
        $evaluaciones = Crm_evaluaciones::with(['modulo', 'tipo_prueba', 'registro.users.datos_generales'])
                                        ->where('registro_id', $registroId)
                                        ->orderBy('id', 'desc')
                                        ->paginate(10);
        $usuario = $evaluaciones->items()[0]->registro->users->datos_generales ?? null;
        return response()->json([
            "mensaje" => "Datos cargados",
            "datos" => $evaluaciones->items(),
            "total" => $evaluaciones->total(),
            "current_page" => $evaluaciones->currentPage(),
            "last_page" => $evaluaciones->lastPage(),
            "usuario" => $usuario
        ], 200);
    }
};