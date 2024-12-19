<?php

namespace App\Imports;

use App\Models\Crm_batchs;
use App\Models\Crm_datos_generales;
use App\Models\Crm_especialidades;
use App\Models\Crm_postulacion_forms;
use App\Models\Crm_postulaciones;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Spatie\Permission\Models\Role;

class PostulacionesImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, WithBatchInserts
{
    protected $usuario;

    public function __construct()
    {
        $this->usuario = User::pluck('id', 'email');
    }
    public function model(array $row)
    {
        if (!isset($this->usuario[$row['email']])) {
            $item = User::create([
                'email' => $row['email'],
                'password' => bcrypt($row['email']),
            ]);
            $datos_generales = Crm_datos_generales::create([
                'user_id' => $item->id,
                'nombre' => $row['nombre'],
                'apellido' => $row['apellido'],
                'telefono' => $row['celular'],
                'pais' => $row['pais'],
                'ciudad' => $row['ciudad'],
            ]);
            $role = Role::firstOrCreate(['name' => 'Postulante']);
            $item->assignRole($role);
            $this->usuario[$row['email']] = $item->id;
        }
        $especialidad = Crm_especialidades::where('nombre', $row['especialidad'])->first();
        if ($especialidad) {
            $batchId = Crm_batchs::where('especialidad_id', $especialidad->id)
                ->where('version', $row['version'])
                ->pluck('id')
                ->first();
            $postulacion = Crm_postulaciones::create([
                'user_id' => $this->usuario[$row['email']],
                'batch_id' => $batchId,
                'estado' => 'prueba',
            ]);
            Crm_postulacion_forms::create([
                'postulaciones_id' => $postulacion->id,
                'nivel_estudios' => $row['nivel_estudio'],
                'nivel_academico' => $row['nivel_academico'],
                'nivel_programacion' => $row['nivel_programacion'],
                'servicio_internet' => $row['servicio_internet'],
                'idioma_extranjero' => $row['idioma_extranjero'],
                'horario_trabajo' => $row['horario_de_trabajo'],
                'comentario' => $row['comentario'],
            ]);
        }
        return;
    }
    public function rules(): array
    {
        return [
            '*.nombre'  => 'required|max:50',
            '*.apellido'  => 'required',
            '*.email'  => 'required|email',
            '*.celular'  => 'required',
            '*.pais'  => 'required',
            '*.ciudad'  => 'required',
            '*.especialidad'  => 'required',
            '*.version'  => 'required',
            '*.nivel_estudio'  => 'required|numeric',
            '*.nivel_academico'  => 'required|numeric',
            '*.nivel_programacion'  => 'required|numeric',
            '*.servicio_internet'  => 'required|numeric',
            '*.idioma_extranjero'  => 'required|numeric',
            '*.horario_de_trabajo'  => 'required|numeric',
        ];
    }
    public function batchSize(): int
    {
        return 500;
    }
    public function chunkSize(): int
    {
        return 500;
    }
}
