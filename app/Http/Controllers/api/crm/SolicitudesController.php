<?php

namespace App\Http\Controllers\api\crm;

use App\Models\Crm_solicitudes;
use App\Models\Crm_solicitud_detalles;
use App\Models\Crm_solicitud_estados;
use App\Models\Crm_empleo_estados;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SolicitudesController extends Controller
{
    public function getSolicitudes(){
        $solicitudes = Crm_solicitudes::paginate(5);
        
         if($solicitudes->isEmpty()){
            $data = [
                'message' => 'Nose encontraron solicitudes',
                'status'  => 200
            ];
            return response()->json($data,404);
        } 
        return response()->json(["mensaje" => "Datos cargados", "datos" => $solicitudes], 200);
    }
    public function getSolicitudesTodosConEtapa(){
        // Obtener todas las solicitudes con la última actualización de estado
        $solicitudes = DB::table('crm_solicitudes')
            ->join('crm_solicitud_detalles', 'crm_solicitudes.id', '=', 'crm_solicitud_detalles.solicitud_id')
            ->join('crm_solicitud_estados', 'crm_solicitud_detalles.solicitud_estado_id', '=', 'crm_solicitud_estados.id')
            ->select(
                'crm_solicitudes.*',
                'crm_solicitud_estados.estado'
            )
            ->whereIn('crm_solicitud_detalles.id', function($query) {
                $query->selectRaw('MAX(id)')
                    ->from('crm_solicitud_detalles')
                    ->groupBy('solicitud_id');
            })
            ->get();
    
        if($solicitudes->isEmpty()){
            return response()->json([
                'message' => 'No se encontraron solicitudes',
                'status'  => 404
            ], 404);
        } 
        
        return response()->json([
            'mensaje' => 'Datos cargados',
            'datos'   => $solicitudes
        ], 200);
    }
    public function getSolicitud(string $id){
        $item = Crm_solicitudes::with('detalles')->find($id);
    
        if ($item) {
            return response()->json(["mensaje" => "Dato cargado", "datos" => $item], 200);
        } else {
            return response()->json(["mensaje" => "Solicitud no encontrada"], 404);
        }
    }
    public function getSolicitudesByUser(Request $request){
        if (Auth::user()->hasPermissionTo('solicitud ver')) {
            $estadoId = $request->query('estado_id');
            $userId = auth()->user()->id;
        
            // Obtener las solicitudes del usuario con la última actualización de estado
            $solicitudes = DB::table('crm_solicitudes')
                ->join('crm_solicitud_detalles', 'crm_solicitudes.id', '=', 'crm_solicitud_detalles.solicitud_id')
                ->join('crm_solicitud_estados', 'crm_solicitud_detalles.solicitud_estado_id', '=', 'crm_solicitud_estados.id')
                ->select(
                    'crm_solicitudes.*',
                    'crm_solicitud_estados.estado'
                )
                ->where('crm_solicitudes.user_id', $userId)
                ->whereIn('crm_solicitud_detalles.id', function($query) {
                    $query->selectRaw('MAX(id)')
                        ->from('crm_solicitud_detalles')
                        ->groupBy('solicitud_id');
                });
            
            if($estadoId!=2){
                $solicitudes->where('crm_solicitudes.is_deleted', '=', $estadoId);
            }
            $solicitudes = $solicitudes->paginate(10); 
                
            if($solicitudes->isEmpty()){
                return response()->json([
                    'message' => 'No se encontraron solicitudes para este usuario',
                    'status'  => 404
                ], 404);
            } 
        
            return response()->json([
                'message' => 'Datos cargados',
                'datos'   => $solicitudes
            ], 200);
        } else {
           return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
        
    }
    public function getCountOfAllSolicitudesEstado() {
        //if (Auth::user()->hasPermissionTo('solicitud ver')) {
            
            // Obtener el conteo de solicitudes por estado de todos los usuarios
            $solicitudes = DB::table('crm_solicitudes')
                ->join('crm_solicitud_detalles', 'crm_solicitudes.id', '=', 'crm_solicitud_detalles.solicitud_id')
                ->join('crm_solicitud_estados', 'crm_solicitud_detalles.solicitud_estado_id', '=', 'crm_solicitud_estados.id')
                ->select(
                    'crm_solicitud_estados.estado',
                    DB::raw('COUNT(crm_solicitudes.id) as total_solicitudes') // quiero que solo cuente las solicitudes con is_deleted=false
                )
                ->where('crm_solicitudes.is_deleted','=', false)
                ->whereIn('crm_solicitud_detalles.id', function($query) {
                    $query->selectRaw('MAX(id)')
                        ->from('crm_solicitud_detalles')
                        ->groupBy('solicitud_id');
                })
                ->groupBy('crm_solicitud_estados.estado')
                ->get();
    
            if ($solicitudes->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron solicitudes',
                    'status'  => 404
                ], 404);
            } 
    
            return response()->json([
                'message' => 'Datos cargados',
                'datos'   => $solicitudes
            ], 200);
        //} else {
         //  return response()->json(["message" => "No tienes permiso para realizar esta acción"], 403);
        //}
    }
    public function addSolicitud(Request $request){
        if (Auth::user()->hasPermissionTo('solicitud crear')) {
            $request->validate([
                "descripcion_estado" => "required|max:250",
                "empresa" => "required|max:25",
                "cargo_postulado" => "required|max:25",
                "url_imagen" => "required|mimes:png,jpg|max:2048",
                "estado_id" => "required|numeric"
            ]);
            try{
                DB::beginTransaction();
                $item = new Crm_solicitudes();
                $item->user_id = auth()->user()->id;
                $item->descripcion_estado = $request->descripcion_estado;
                $item->empresa = $request->empresa;
                $item->cargo_postulado = $request->cargo_postulado;
                if($request->file('url_imagen')){
                    $foto = $request->file('url_imagen');
                    $nombreFoto = time().'.png';
                    $foto->move('img/img_solicitudes/',$nombreFoto);
                    $item->url_imagen = $nombreFoto;
                }
                if ($item->save()) {
                    //agreagamos a la tabla Crm_solicitud_detalles
                    $detalle = new Crm_solicitud_detalles();
                    $detalle->solicitud_id = $item->id;
                    $detalle->solicitud_estado_id = $request->estado_id;
                    $detalle->fecha_postulacion = now()->toDateString();
                    if($detalle->save()){
                        DB::commit();
                        return response()->json(["mensaje" => "Registro exitoso", "datos" => $item], 200);
                    }else{
                        return response()->json(["mensaje" => "No se pudo realizar el registro, error en detalle"], 422);
                    }
                }else{
                    return response()->json(["mensaje" => "No se pudo realizar el registro"], 422);
                }
            }catch (\Throwable $th) {
                DB::rollBack();
                return response()->json(["mensaje" => "Error: $th"], 422);
            }
        } else {
           return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
        
    }
    public function setSolicitud(Request $request, string $id){
        if (Auth::user()->hasPermissionTo('solicitud editar')) {
            $request->validate([
                "descripcion_estado" => "required|max:250",
                "empresa" => "required|max:25",
                "cargo_postulado" => "required|max:25"
            ]);
            DB::beginTransaction();
            $item = Crm_solicitudes::find($id);
            $item->user_id = auth()->user()->id;
            $item->descripcion_estado = $request->descripcion_estado;
            $item->empresa = $request->empresa;
            $item->cargo_postulado = $request->cargo_postulado;
            try {
                if ($item->save()) {
                    if($request->estado_id!=$request->estado_id_inicial){
                        //agreagamos a la tabla Crm_solicitud_detalles
                        $detalle = new Crm_solicitud_detalles();
                        $detalle->solicitud_id = $item->id;
                        $detalle->solicitud_estado_id = $request->estado_id;
                        $detalle->fecha_postulacion = now()->toDateString();
                        if($detalle->save()){
                            //obtengo el registro empl0_estado por su estado_id y pregunto si su estado=contratado
                            $itemEstado = Crm_solicitud_estados::find($request->estado_id);
                            if($itemEstado->estado === "Contratado"){
                                //cambio el estado de la tabla empleo_estados
                                $itemEmpleo = Crm_empleo_estados::find(auth()->user()->id);
                                $itemEmpleo->estado = $itemEstado->estado;
                                $itemEmpleo->save();
                            }
                            DB::commit();
                            return response()->json(["mensaje" => "Registro modificado", "datos" => $item], 201);
                        }else{
                            return response()->json(["mensaje" => "No se pudo realizar la modificacion, error en detalle"], 422);
                        }
                    }else{
                        DB::commit();
                        return response()->json(["mensaje" => "Registro modificado pero no se agrego nada en detalle", "datos" => $item], 201);
                    }
               }else{
                   return response()->json(["mensaje" => "No se pudo realizar la modificacion"], 422);
               }
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json(["mensaje" => "Error: $th"], 422);
            }
        } else {
           return response()->json(["message" => "No tienes permiso para realizar esta          accion"], 403);
        }
    }
    public function setStatus(string $id){
        if (Auth::user()->hasPermissionTo('solicitud borrar')) {
            $item = Crm_solicitudes::find($id);
            $item->is_deleted = !$item->is_deleted;
            if ($item->save()) {
                return response()->json(["mensaje" => "Eliminado, se cambio el estado", "datos" => $item], 202);
            } else {
                return response()->json(["mensaje" => "No se pudo eliminar, no se cambio el estado"], 422);
            } 
        } else {
           return response()->json(["message" => "No tienes permiso para realizar esta          accion"], 403);
        }
    }
    public function buscarSolicitudes(Request $request){
        $query = $request->query('query');
        $select = $request->query('select_id');
        $estado = $request->query('estado_id');
        if($query!=''&&$select==0){
            $solicitudes = DB::table('crm_solicitudes')
            ->join('crm_datos_generales', 'crm_solicitudes.user_id', '=', 'crm_datos_generales.user_id')
            ->join('crm_solicitud_detalles', 'crm_solicitudes.id', '=', 'crm_solicitud_detalles.solicitud_id')
            ->join('crm_solicitud_estados', 'crm_solicitud_detalles.solicitud_estado_id', '=', 'crm_solicitud_estados.id')
            ->select(
                'crm_solicitudes.*',
                'crm_datos_generales.nombre',
                'crm_datos_generales.apellido',
                'crm_solicitud_estados.estado'
            )
            ->where(function($subquery) use ($query) {
                $subquery->where('crm_datos_generales.nombre', 'like', '%' . $query . '%')
                         ->orWhere('crm_solicitudes.empresa', 'like', '%' . $query . '%');
            });
        }
        if($select!=0&&$query==''){
            $solicitudes = DB::table('crm_solicitudes')
            ->join('crm_datos_generales', 'crm_solicitudes.user_id', '=', 'crm_datos_generales.user_id')
            ->join('crm_solicitud_detalles', 'crm_solicitudes.id', '=', 'crm_solicitud_detalles.solicitud_id')
            ->join('crm_solicitud_estados', 'crm_solicitud_detalles.solicitud_estado_id', '=', 'crm_solicitud_estados.id')
            ->select(
                'crm_solicitudes.*',
                'crm_datos_generales.nombre',
                'crm_datos_generales.apellido',
                'crm_solicitud_estados.estado'
            )
            ->where('crm_solicitud_detalles.solicitud_estado_id', '=', $select);          
        }
        if($estado!=2){
            $solicitudes->where('crm_solicitudes.is_deleted', '=', $estado);
        }
        $solicitudes = $solicitudes->paginate(10); 
           
        if($solicitudes->isEmpty()){
            return response()->json(['message' => 'No se encontraron solicitudes', 'status' => 404], 404);
        } 
        return response()->json(['mensaje' => 'Datos cargados', 'datos' => $solicitudes], 200);
    }

    public function getNroSolicitudesActivas(){
        // Obtener el conteo de solicitudes por estado de todos los usuarios
        $nroSolicitudes = DB::table('crm_solicitudes')
        ->join('crm_solicitud_detalles', 'crm_solicitudes.id', '=', 'crm_solicitud_detalles.solicitud_id')
        ->where('crm_solicitudes.is_deleted', false) // Filtrar por is_deleted = false
        ->whereIn('crm_solicitud_detalles.id', function($query) {
            $query->selectRaw('MAX(id)')
                ->from('crm_solicitud_detalles')
                ->groupBy('solicitud_id');
        })
        ->count('crm_solicitudes.id'); // Contar el número total de solicitudes

        return response()->json([
            'message' => 'Datos cargados',
            'nroSolicitudes' => $nroSolicitudes
        ], 200);
    }
}