<?php

namespace App\Http\Controllers\api\crm;

use App\Models\Crm_solicitud_estados;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SolicitudEstadosController extends Controller
{
   
    public function getSolicitudEstados(){
        if (Auth::user()->hasPermissionTo('solicitud estado ver')) {
            $estados = Crm_solicitud_estados::paginate(10);
            if($estados->isEmpty()){
                $data = [
                    'message' => 'Nose encontraron solicitudes',
                    'status'  => 200
                ];
                return response()->json($data,404);
            } 
            return response()->json(["mensaje" => "Estados cargados", "datos" => $estados], 200);
        } else {
           return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }
    public function getSolicitudEstadosActivos(){
        if (Auth::user()->hasPermissionTo('solicitud estado ver')) {
            $estados = Crm_solicitud_estados::where('is_deleted', false)->get();;
            if($estados->isEmpty()){
                $data = [
                    'message' => 'Nose encontraron solicitudes',
                    'status'  => 200
                ];
                return response()->json($data,404);
            } 
            return response()->json(["mensaje" => "Estados cargados", "datos" => $estados], 200);
        } else {
           return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }
    public function getEstado(string $id){
        $estado = Crm_solicitud_estados::find($id);
        return response()->json(["mensaje" => "Dato cargado", "datos" => $estado], 200);
    }
    public function setEstado(Request $request, string $id){
        if(Auth::user()->hasPermissionTo('solicitud estado editar')){
            $request->validate([
                "estado" => "required|max:50"
            ]);
            $item = Crm_solicitud_estados::find($id);
            $item->estado = $request->estado;
            if($item->save()){
                return response()->json(["mensaje" => "Modificacion con exitoso del estado", "datos" => $item], 200);
            }else{
                return response()->json(["mensaje" => "Error al modificar estado"], 422);
            }
        }else{
            return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }
    public function addEstado(Request $request){
        if (Auth::user()->hasPermissionTo('solicitud estado crear')) {
            $request->validate([
                "estado" => "required|max:50"
            ]);
            $item = new Crm_solicitud_estados();
            $item->estado = $request->estado;
            if($item->save()){
                return response()->json(["mensaje" => "Registro exitoso del estado", "datos" => $item], 200);
            }else{
                return response()->json(["mensaje" => "Error al registrar estado"], 422);
            }
        } else {
           return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }
    public function deleteEstado($id){
        if (Auth::user()->hasPermissionTo('solicitud estado borrar')) {
            $item = Crm_solicitud_estados::find($id);
            $item->is_deleted = !$item->is_deleted;
            if ($item->save()) {
                return response()->json(["mensaje" => "Estado cambiado exitosamente"], 200);
            } else {
                return response()->json(["mensaje" => "Estado no encontrado"], 404);
            }
        } else {
           return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }
}