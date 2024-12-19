<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_postulaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function getTodosLosUsuariosConBatchYFases()
    {
        $postulaciones = Crm_postulaciones::with([
            'batch:id,version',
            'users:id'
        ])->get();
        $postulacionesResponse = [];
        foreach ($postulaciones as $postulacion) {
            $postulacionesResponse[] = [
                'user_id' => $postulacion->user_id,
                'batch_id' => $postulacion->batch->id,
                'batch_version' => $postulacion->batch->version,
                'estado' => $postulacion->estado
            ];
        }
        return response()->json(["datos" => $postulacionesResponse, "mensaje" => "Postulantes obtenidos con éxito"], 200);
    }
    public function getEstudiantesPorBatch()
    {
        try {
            // Obtenemos las especialidades con sus batchs y registros asociados
            $especialidades = DB::table('crm_especialidades')
                ->join('crm_batchs', 'crm_especialidades.id', '=', 'crm_batchs.especialidad_id')
                ->join('crm_registros', 'crm_batchs.id', '=', 'crm_registros.batch_id')
                ->where('crm_registros.is_deleted', false)  // Filtramos registros no eliminados
                ->select('crm_especialidades.nombre', DB::raw('COUNT(crm_registros.id) as total_estudiantes'))
                ->groupBy('crm_especialidades.nombre')
                ->get();
            if ($especialidades->isEmpty()) {
                return response()->json(["mensaje" => "No se encontraron estudiantes"], 404);
            }
            // Estructuramos la respuesta
            $respuesta = ['mensaje' => 'Estudiantes por Batch', 'batch' => []];
            foreach ($especialidades as $especialidad) {
                $respuesta['batch'][] = [
                    'nombre' => $especialidad->nombre,
                    'total_estudiantes' => $especialidad->total_estudiantes
                ];
            }
            return response()->json($respuesta, 200);
        } catch (\Throwable $th) {
            return response()->json(["mensaje" => "ERROR", "datos" => $th->getMessage()], 400);
        }
    }
    public function totalPostulantes()
    {
        $totalPostulantes = Crm_postulaciones::count();
        if ($totalPostulantes === 0) {
            return response()->json(["mensaje" => "No hay postulantes registrados"], 404);
        }
        return response()->json(["total_postulantes" => $totalPostulantes, "mensaje" => "Total de postulantes obtenido con éxito"], 200);
    }
    public function totalEstudiantes()
    {
        $totalEstudiantes = Crm_postulaciones::where('estado', 'aprobado')->count();
        return response()->json(["total_estudiantes" => $totalEstudiantes, "mensaje" => "Total de estudiantes aprobado con éxito"], 200);
    }
    public function obtenerBatches()
    {
        $batches = Crm_postulaciones::with('batch:id,version')
            ->get()
            ->pluck('batch')
            ->unique();
        $batchesArray = [];
        foreach ($batches as $batch) {
            $batchesArray[] = [
                'batch_id' => $batch->id,
                'batch_version' => $batch->version,
            ];
        }
        return response()->json(["batches" => $batchesArray, "mensaje" => "Batches obtenidos con éxito"], 200);
    }
    public function postulantesPorBatch()
    {
        $batchesConPostulantes = Crm_postulaciones::with('batch:id,version')
            ->get()
            ->groupBy('batch.id');
        $resultado = [];
        foreach ($batchesConPostulantes as $batchId => $postulaciones) {
            $resultado[$batchId] = count($postulaciones);
        }
        return response()->json(["postulantesPorBatch" => $resultado, "mensaje" => "batch_id y Postulantes por batch obtenidos con éxito"], 200);
    }
    public function estudiantesPorBatch()
    {
        $batchesConEstudiantes = Crm_postulaciones::with('batch:id,version')
            ->where('estado', 'aprobado')
            ->get()
            ->groupBy('batch.id');
        $resultado = [];
        foreach ($batchesConEstudiantes as $batchId => $postulaciones) {
            $resultado[$batchId] = count($postulaciones);
        }
        return response()->json(["estudiantesPorBatch" => $resultado, "mensaje" => "batch_id y Estudiantes por batch obtenidos con éxito"], 200);
    }
}
