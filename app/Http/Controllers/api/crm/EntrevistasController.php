<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Mail\PruebasMailable;
use App\Models\Crm_entrevista_detalles;
use App\Models\Crm_entrevistas;
use App\Models\Crm_postulaciones;
use App\Models\Crm_registros;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EntrevistasController extends Controller
{


    public function index()
{
    $entrevistas = Crm_entrevistas::with([
        'postulaciones',
        'postulaciones.users',
        'postulaciones.users.datos_generales',
        'postulaciones.users.roles',
    ])
        ->orderBy('id', 'desc')
        ->paginate(10);

    return response()->json(["mensaje" => "Datos cargados Entrevistas", "datos" => $entrevistas], 200);
}
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "postulaciones_id" => "required|integer",
            "nombre" => "nullable|string|max:15",
            "tipo" => "required|string|max:15",
            "fecha" => "required|date",
            "hora_inicio" => "required|date_format:H:i",
            "hora_fin" => "required|date_format:H:i",
            "enlace" => "required|url",
            "email" => "required|email"
        ]);
        try {
            DB::beginTransaction();
            // Obtener la postulación relacionada con postulaciones_id
            $postulacion = Crm_postulaciones::with('users', 'batch')->find($request->postulaciones_id);

            if (!$postulacion) {
                return response()->json(["mensaje" => "La postulación no existe."], 404);
            }
            $item = new Crm_entrevistas();
            $item->postulaciones_id = $request->postulaciones_id;
            $item->nombre = $request->nombre; // Puedes usar el nombre si es proporcionado
            $item->tipo = $request->tipo;
            $item->fecha = $request->fecha;
            $item->hora_inicio = $request->hora_inicio;
            $item->hora_fin = $request->hora_fin;
            $item->enlace = $request->enlace;
            $item->estado = 'pendiente'; // Proporcionar un valor para 'estado'
            $item->save();
            // Verificar si 'entrevista_detalle' está presente y es un array
            if (is_array($request->entrevista_detalle)) {
                foreach ($request->entrevista_detalle as $row) {
                    $item_detalle = new Crm_entrevista_detalles();
                    $item_detalle->entrevista_id = $item->id;
                    $item_detalle->user_id = $row['id'];
                    $item_detalle->observacion = '';
                    $item_detalle->save();
                }
            }
            DB::commit();
            $tipo = "Hola futuro Bootcamper, a continuación podrás acceder a tu entrevista el: $request->fecha, en el horario: $request->hora_inicio - $request->hora_fin.";
            $correo = new PruebasMailable($request->mensaje, $request->enlace, $tipo);
            Mail::to($request->email)->send($correo);
            return response()->json(["mensaje" => "Registro exitoso.", "datos" => $item, "postulacion" => [
                "postulacion_id" => $postulacion->id,
                "user" => $postulacion->user,
                "batch" => $postulacion->batch // Información del batch asociado
            ]], 200);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json(["mensaje" => "Hubo un error al guardar los datos.", "datos" => $th->getMessage()], 500);
        }
    }

    public function show(string $user_id)
    {
        $item[] = Crm_entrevistas::with([
            'postulaciones',
            'postulaciones.users',
            'postulaciones.users.roles',
            'postulaciones.users.datos_generales',
            'entrevista_detalle',
            'usuario',
        ])->find($user_id);
        return response()->json(["datos" => $item], 200);
    }
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $request->validate([
        "tipo" => "required|string|max:15",
        "fecha" => "required|date",
        "hora_inicio" => "required",
        "hora_fin" => "required",
        "enlace" => "required|url",
        "email" => "required|email"
    ]);
    try {
        DB::beginTransaction();
        $item =  Crm_entrevistas::find($id);
        $item->postulaciones_id = $request->postulaciones_id;
        $item->nombre = $request->nombre;
        $item->tipo = $request->tipo;
        $item->fecha = $request->fecha;
        $item->hora_inicio = $request->hora_inicio;
        $item->hora_fin = $request->hora_fin;
        $item->enlace = $request->enlace;
        $item->estado = 'pendiente'; // Proporcionar un valor para 'estado'
        $item->save();
        $item->save();
        // Verificar si 'entrevista_detalle' está presente y es un array
        if (is_array($request->entrevista_detalle)) {
            foreach ($request->entrevista_detalle as $row) {
                $item_detalle = Crm_entrevista_detalles::updateOrCreate(
                    ['entrevista_id' => $item->id, 'user_id' => $row['id']],
                    ['entrevista_id' => $item->id, 'user_id' => $row['id']]
                );
                $item_detalle->save();
            }
        }
        DB::commit();
        $tipo = "Hola futuro Bootcamper. Se modificó tu entrevista para el: $request->fecha, en el horario: $request->hora_inicio - $request->hora_fin.";
        $correo = new PruebasMailable($request->mensaje, $request->enlace, $tipo);
        Mail::to($request->email)->send($correo);
        return response()->json(["mensaje" => "Registro modificado exitosamente.", "datos" => $item], 200);
    } catch (\Throwable $th) {
        DB::rollBack();
        return response()->json(["mensaje" => "Hubo un error al guardar los datos.", "datos" => $th->getMessage()], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function agregarARegistros(Request $request, string $id) {
        $request->validate([
            "resultado" => "required"
        ]);
        try {
            DB::beginTransaction();
    
            $entrevista = Crm_entrevistas::find($id);
            if (!$entrevista) {
                throw new \Exception('Entrevista no encontrada.');
            }
            $entrevista->estado = $request->resultado;
            $entrevista->save();
    
            $postulacion = Crm_postulaciones::find($entrevista->postulaciones_id);
            if (!$postulacion) {
                throw new \Exception('Postulación no encontrada.');
            }
            $postulacion->estado = $request->resultado;
            $postulacion->save();
            $idUser = $postulacion->user_id;
            if ($request->resultado == 'aprobado') {
                $registro = new Crm_registros();
                $registro->batch_id = $postulacion->batch_id;
                $registro->user_id = $idUser;
                $registro->estado = 'Bootcamper';
                $registro->descripcion = 'descripcion...'; 
                $registro->save();
                $rol = Role::where('name', 'Estudiante')->first();
                $usuario = User::find($idUser);
                $usuario->syncRoles($rol);
            }
            DB::commit();
            return response()->json(["mensaje" => "Registro agregado exitosamente"], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["mensaje" => "Error: {$th->getMessage()}"], 422);
        }    
    }
    
}

