<?php

namespace App\Exports;

use App\Models\Crm_postulaciones;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PostulacionExport implements FromCollection, WithHeadings, WithEvents, WithStyles
{
    protected $batchId;

    // Constructor que acepta un batchId para filtrar
    public function __construct($batchId = null)
    {
        $this->batchId = $batchId;
    }

    /**
    * Método para recoger los datos desde la base de datos con filtrado opcional por batch_id.
    * Se muestra solo el nombre de la especialidad en la primera columna.
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
      // Seleccionar los datos incluyendo el nombre de la especialidad concatenado con la versión del batch
    $query = Crm_postulaciones::select(
        DB::raw("CONCAT(crm_especialidades.nombre, ' - ', crm_batchs.version) as especialidad_version"), // Concatenar especialidad y versión
        'crm_postulaciones.batch_id',  // Batch ID
        'crm_postulaciones.estado',    // Estado
        DB::raw('count(*) as total')   // Contar el total
    )
    // Realizar el join necesario para obtener el nombre de la especialidad y la versión del batch
    ->join('crm_batchs', 'crm_postulaciones.batch_id', '=', 'crm_batchs.id')  // Unir crm_postulaciones con crm_batchs
    ->join('crm_especialidades', 'crm_batchs.especialidad_id', '=', 'crm_especialidades.id')  // Unir crm_batchs con crm_especialidades
    ->groupBy('crm_especialidades.nombre', 'crm_batchs.version', 'crm_postulaciones.batch_id', 'crm_postulaciones.estado');

    // Aplica el filtro de batch_id si se proporciona
    if ($this->batchId) {
        $query->where('crm_postulaciones.batch_id', $this->batchId);
    }

    return $query->get();
    }

    /**
     * Encabezados para el archivo Excel.
     * @return array
     */
    public function headings(): array
    {
        return ['Especialidad - Version', 'Batch ID', 'Estado', 'Total']; // Encabezados claros para las columnas
    }

    /**
     * Registra eventos para modificar la hoja después de la exportación.
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Aplica estilos adicionales, si es necesario
                $sheet->getStyle('A1:D1')->getFont()->setBold(true); // Negrita en los encabezados

                // Escribir el batchId en una celda si se proporcionó
                if ($this->batchId) {
                    $sheet->setCellValue('A20', 'BATCH ID: ' . $this->batchId);
                }
            },
        ];
    }

    /**
     * Aplica estilos básicos a la hoja.
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            'A1:D1' => ['font' => ['bold' => true]], // Estilo de encabezados en negrita
        ];
    }
}
