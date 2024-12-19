<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Imports\PostulacionesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;

class ImportPostulacionController extends Controller
{
    public function import(Request $request){
        $request->validate([
            "archivo" => "required|mimes:xlsx,xls",
        ]);
        
        //return response()->json(["message" => 'Registro Agregado correctamente'], 200);
        if ($request->file("archivo")) {
            $file = $request->file('archivo');
            Excel::import(new PostulacionesImport(), $file);
            return response()->json(["message" => 'Registro Agregado correctamente'], 200);
        } else {
            return response()->json(["message" => 'Debes enviar un archivo'], 422);
        }        
    }
    public function downloadFile()
    {
        $filePath = public_path('Documentos/PlantillaImport/Plantilla-Postulacion.xlsx');
        return Response::download($filePath, 'Plantilla-Postulacion.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);
    }
}
