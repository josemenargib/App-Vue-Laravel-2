<?php

namespace App\Http\Controllers\api\crm;

use App\Exports\ExportacionesBatch;
use App\Exports\PostulacionExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportacionesBatchController extends Controller
{
    public function export($id=null)
    {
        // Obtener el batch_id desde el request (puede ser null si no se selecciona ningún batch)
        

        // Pasar el batchId a la exportación
        return Excel::download(new PostulacionExport($id), 'Crm_postulaciones.xlsx');
    }
}
