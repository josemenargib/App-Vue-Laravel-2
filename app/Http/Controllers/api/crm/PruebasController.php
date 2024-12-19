<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Mail\PruebasMailable;
use App\Models\Crm_datos_generales;
use App\Models\Crm_postulaciones;
use App\Models\Crm_pruebas;
use App\Models\Crm_registros;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class PruebasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datos = Crm_pruebas::orderBy('id', 'desc')->with('tipo_pruebas', 'postulaciones.users.datos_generales', 'users')->paginate(5);
        return response()->json(['mensaje' => 'Datos cargados', 'datos' => $datos]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->hasPermissionTo('prueba crear')) {

            $request->validate([
                "tipo_prueba_id" => "required",
                "fecha" => "required",
                "enlace" => "required|url",

            ]);
            $item = new Crm_pruebas();
            $item->postulacion_id = $request->postulacion_id;
            $item->tipo_prueba_id = $request->tipo_prueba_id;
            $item->responsable_id = Auth::id();
            $item->fecha = $request->fecha;
            $item->enlace = $request->enlace;
            $item->enlace_alternativo = $request->enlace_alternativo;
            $item->save();
            $tipo = "Hola futuro Bootcamper, a continuación podrás acceder a tu prueba el: $request->fecha";
            $correo = new PruebasMailable($request->mensaje, $request->enlace, $tipo);
            Mail::to($request->email)->send($correo);
            return response()->json(["mensaje" => "Registro Exitoso", "datos" => $item], 200);
        } else {
            return response()->json(["mensaje" => "No tienes permiso para realizar esta accion"], 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (Auth::user()->hasPermissionTo('prueba ver')) { {
                $datos = Crm_pruebas::with(['tipo_pruebas', 'postulaciones.datos_generales', 'users' => function ($query) {
                    $query->select('id', 'email');
                }])
                    ->find($id);
                return response()->json(['mensaje' => 'datos cargados', 'datos' => $datos], 200);
            }
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (Auth::user()->hasPermissionTo('prueba editar')) {

            $request->validate([
                "puntaje" => "required",
                "rendimiento" => "required"
            ]);

            // $tipo_prueba_id = $request->tipo_prueba_id;
            // $contador = Crm_pruebas::where('tipo_prueba_id', $tipo_prueba_id)->count() + 1;
            // $codigo_evaluacion = $tipo_prueba_id . '-' . str_pad($contador, 3, '0', STR_PAD_LEFT);


            try {
                DB::beginTransaction();
                $item = Crm_pruebas::find($id);
                $item->puntaje = $request->puntaje;
                $item->rendimiento = $request->rendimiento;
                //$item->codigo_evaluacion = $codigo_evaluacion;

                $item->save();

                // Asume que $id_postulacion se obtiene de alguna manera
                $item2 = Crm_postulaciones::find($item->postulacion_id);
                if ($item->rendimiento === 'aprobado') {
                    $item2->estado = "entrevista";
                } else if ($item->rendimiento === 'reprobado') {
                    $item2->estado = "reprobado";
                }
                $item2->save();


                DB::commit();
                return response()->json(['mensaje' => 'registro modificado exitosamente', 'datos' => $item], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['mensaje' => 'Error al modificar el registro', 'error' => $e->getMessage()], 400);
            }
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function buscar(Request $request)
    {
        $usuarioAutenticado = auth()->id();
        if ($usuarioAutenticado) {
            $search = $request->get('search');
            // return response()->json($search);
            $item = Crm_datos_generales::when($search, function ($query) use ($search) {
                $query->where('nombre', 'LIKE', "%$search%");
            })->get();

            return response()->json(['mensaje' => 'Datos cargados', 'datos' => $item], 200);
        } else {
            return response()->json("no autenticado");
        }
    }
    public function selectPersona(string $id)
    {
        $item = Crm_datos_generales::find($id);
        return response()->json(["mensaje" => "Datos cargados", "datos" => $item], 200);
    }

    public function listarEstadoPrueba()
    {
        $datos = Crm_postulaciones::where('estado', 'prueba')
            ->orderBy('id', 'desc')
            ->with('users.datos_generales', 'batch.Crm_especialidades', 'users',)
            ->paginate(5);

        return response()->json(['mensaje' => 'Datos cargados', 'datos' => $datos]);
    }
}
