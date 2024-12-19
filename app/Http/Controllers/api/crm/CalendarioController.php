<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_entrevistas;
use Illuminate\Http\Request;

class CalendarioController extends Controller
{
    public function index()
    {
        //$item = Crm_entrevistas::where('estado', 'pendiente')->with('postulaciones.users.datos_generales')->get();
        $items = Crm_entrevistas::where('estado', 'pendiente')
            ->with('postulaciones.users.datos_generales')
            ->get();

        // Transformar los registros al formato deseado
        $formattedItems = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->postulaciones->users->datos_generales->nombre . ' ' . $item->postulaciones->users->datos_generales->apellido,
                'start' => $item->fecha . 'T' . $item->hora_inicio,
                'end' => $item->fecha . 'T' . $item->hora_fin,
            ];
        });
        return response()->json(["mensaje" => "Datos cargados", "datos" => $formattedItems], 200);
    }
    public function view(string $id){
        $item = Crm_entrevistas::with('postulaciones.users.datos_generales')->find($id);
        return response()->json(["mensaje"=>"Registro cargado","datos"=>$item]);
    }
}
