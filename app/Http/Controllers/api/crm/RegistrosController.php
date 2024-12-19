<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_contratos;
use Illuminate\Http\Request;
use App\Models\Crm_registros;
use Illuminate\Support\Facades\DB;
use App\Models\Crm_documentos;
use App\Models\User;
use App\Models\Crm_batchs;
use App\Models\Crm_postulaciones;
use App\Models\Crm_datos_generales;
use Spatie\Permission\Models\Role;

class RegistrosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $registros = Crm_registros::paginate(10);
            return response()->json(["mensaje" => "OK", "datos" => $registros], 200);
        } catch (\Throwable $th) {
            return response()->json(["mensaje" => "ERROR", "datos" => $th], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'user_id.required' => 'El usuario es un atributo requerido',
            'user_id.exists' => 'El usuario no existe',
            'user_id.unique' => 'El usuario ya se encuentra registrado en este Batch',
            'estado.required' => 'El atributo estado es obligatorio',
            'batch_id.required' => "El atributo batch_id es requerido",
            'batch_id.required' => "El batch_id no esta registrado en la tabla Batchs"
        ];
        $request->validate([
            "estado" => "required",
            "batch_id" => "required|exists:crm_batchs,id",
            "user_id" => "required|exists:users,id|unique:crm_registros,user_id,NULL,id,batch_id,$request->batch_id"
        ], $messages);
        try {
            $new_registro = new Crm_registros;
            $new_registro->estado = $request->estado;
            $new_registro->batch_id = $request->batch_id;
            $new_registro->user_id = $request->user_id;
            if ($request->descripcion != null) {
                $new_registro->descripcion = $request->descripcion;
            }
            $new_registro->save();
            return response()->json(["mensaje" => "OK", "datos" => $new_registro], 201);
            //code...
        } catch (\Throwable $th) {
            return response()->json(["mensaje" => "ERROR", "datos" => $th], 400);
            //throw $th;
        }

        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $registros = Crm_registros::where('id', $id)->with('batch', 'batch.Crm_especialidades')->first();
            return response()->json(["mensaje" => "OK", "datos" => $registros], 200);
        } catch (\Throwable $th) {
            return response()->json(["mensaje" => "ERROR", "datos" => $th], 404);
        }
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $messages = [
            'user_id.required' => 'El usuario es un atributo requerido',
            'user_id.exists' => 'El usuario no existe',
            'user_id.unique' => 'El usuario ya se encuentra registrado en este Batch',
            'estado.required' => 'El atributo estado es obligatorio',
            'batch_id.required' => "El atributo batch_id es requerido",
            'batch_id.required' => "El batch_id no esta registrado en la tabla Batchs"
        ];
        $request->validate([
            "estado" => "required",
            "batch_id" => "required|exists:crm_batchs,id",
            "user_id" => "required|exists:users,id|unique:crm_registros,user_id,$id,id,batch_id,$request->batch_id"
        ], $messages);
        try {
            $registro = Crm_registros::find($id);
            $registro->estado = $request->estado;
            $registro->batch_id = $request->batch_id;
            $registro->user_id = $request->user_id;
            if ($request->descripcion != null) {
                $registro->descripcion = $request->descripcion;
            }
            $registro->save();
            return response()->json(["mensaje" => "Update", "datos" => $registro], 201);
            //code...
        } catch (\Throwable $th) {
            return response()->json(["mensaje" => "ERROR", "datos" => $th], 400);
            //throw $th;
        }
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        try {
            $registro = Crm_registros::find($id);
            $registro->delete();
            return response()->json(["mensaje" => "Hard Delete", "datos" => $registro], 204);
            //code...
        } catch (\Throwable $th) {
            return response()->json(["mensaje" => "ERROR", "datos" => $th], 400);
            //throw $th;
        }
        //
    }

    public function delete(string $id)
    {

        try {
            $registro = Crm_registros::find($id);
            $registro->is_deleted = !$registro->is_deleted;
            $registro->save();
            return response()->json(["mensaje" => "Soft Delete", "datos" => $registro], 200);
            //code...
        } catch (\Throwable $th) {
            return response()->json(["mensaje" => "ERROR", "datos" => $th], 400);
            //throw $th;
        }
        //
    }

    public function activos()
    {
        try {
            $registros = Crm_registros::where("is_deleted", false)->get();
            return response()->json(["mensaje" => "OK", "datos" => $registros], 200);
            //code...
        } catch (\Throwable $th) {
            return response()->json(["mensaje" => "ERROR", "datos" => $th], 400);
            //throw $th;
        }
    }

    public function register(Request $request)
    {
        $messages = [
            'user_id.required' => 'El usuario es un atributo requerido',
            'user_id.exists' => 'El usuario no existe',
            'user_id.unique' => 'El usuario ya se encuentra registrado en este Batch',
            'estado.required' => 'El atributo estado es obligatorio',
            'batch_id.required' => "El atributo batch_id es requerido",
            'batch_id.required' => "El batch_id no esta registrado en la tabla Batchs",
            'contrato_estado.required' => "El atributo contrato_id es obligatorio",
            'numero_contrato' => "El atributo numero_contrato es obligatorio",
            'documentos.*.nombre.required' => "El nombre de un documento es requerido",
            'documentos.*.numero_referencia.required' => "El numero de referencia de un documento es requerido",
            'documentos.*.documento.required' => "El archivo documento es requerido",
            'documentos.*.documento.mines' => "La extencion del archivo no es la correcta solo se permite pdf,xlx,csv,doc,docx,jpg,png",
            'documentos.*.documento.max' => "El tamaño del archivo excedio el limite de  2048 mb"

        ];
        $request->validate([
            "estado" => "required",
            "batch_id" => "required|exists:crm_batchs,id",
            "user_id" => "required|exists:users,id|unique:crm_registros,user_id,NULL,id,batch_id,$request->batch_id",
            "contrato_estado" => "required",
            "numero_contrato" => "required",
            "contrato_documento" => "required|mimes:pdf,xlx,csv,doc,docx|max:2048",
            "documentos.*.nombre" => "required",
            "documentos.*.numero_referencia" => "required",
            "documentos.*.documento" => "required|mimes:pdf,xlx,csv,doc,docx,jpg,png|max:2048"

        ], $messages);
        try {
            DB::beginTransaction();
            $new_registro = new Crm_registros;
            $new_registro->estado = $request->estado;
            $new_registro->batch_id = $request->batch_id;
            $new_registro->user_id = $request->user_id;
            if ($request->descripcion != null) {
                $new_registro->descripcion = $request->descripcion;
            }
            $new_registro->save();
            $Contrato = new Crm_contratos();
            $Contrato->estado = $request->contrato_estado;
            $Contrato->registro_id = $new_registro->id;
            $Contrato->numero_contrato = $request->numero_contrato;
            if ($request->file('contrato_documento')) {
                if ($Contrato->storage_url) {
                    unlink('Documentos/Contratos/' . $Contrato->storage_url);
                }
                $documento = $request->file('contrato_documento');
                $nombreDocumento = time() . $request->contrato_documento->extencion();
                $documento->move("Documentos/Contratos/", $nombreDocumento);
                $Contrato->storage_url = $nombreDocumento;
            }
            $Contrato->save();
            foreach ($request->documentos as $documento_aux) {
                $Documento = new Crm_documentos();
                $Documento->registro_id = $new_registro->id;
                $Documento->nombre = $documento_aux->nombre;
                $Documento->numero_referencia = $documento_aux->numero_referencia;
                if ($documento_aux->file('documento')) {
                    if ($Documento->storage_url) {
                        unlink('Documentos/Documentos/' . $Documento->storage_url);
                    }
                    $documento = $documento_aux->file('documento');
                    $nombreDocumento = time() . $documento_aux->documento->extencion();
                    $documento->move("Documentos/Documentos/", $nombreDocumento);
                    $Documento->storage_url = $nombreDocumento;
                }
                $Documento->save();
            }
            DB::commit();
            return response()->json(["mensaje" => "Created", "datos" => $new_registro], 201);
        } catch (\Throwable $th) {
            return response()->json(["mensaje" => "ERROR", "datos" => $th], 400);
            //throw $th;
        }
    }
    public function users_batch(string $id, Request $request)
    {
        try {
            $search = $request->input('search');

            $registros = Crm_registros::where("batch_id", $id)
                ->where("is_deleted", 0)
                ->when($search, function ($query, $search) {
                    $query->whereHas('users.datos_generales', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%")
                            ->orWhere('apellido', 'like', "%{$search}%");
                    });
                })
                ->orderBy("user_id")
                ->with('users', 'users.datos_generales')
                ->with('batch')
                ->paginate(10);

            return response()->json(["mensaje" => "Datos cargados", "datos" => $registros], 200);
        } catch (\Throwable $th) {
            return response()->json(["mensaje" => "ERROR", "datos" => $th], 400);
        }
    }
    public function batchs_user(string $id)
    {
        try {
            $registros = Crm_registros::where("user_id", $id)->orderBY("user_id")->with('users', 'users.datos_generales')->with('batch')->paginate(10);
            //->with('users', 'users.datos_generales')
            return response()->json(["mensaje" => "Datos cargados", "datos" => $registros], 200);
        } catch (\Throwable $th) {
            return response()->json(["mensaje" => "ERROR", "datos" => $th], 400);
        }
    }

    public function search_usuarios_no_registrados(Request $request)
    {
        try {
            $search = $request->get('search');
            $batch_id  = $request->get('batch_id');
            $usuarios = Crm_datos_generales::when($search, function ($query) use ($search) {
                $query->where('nombre', 'LIKE', '%' . $search . '%')->orWhere('apellido', 'LIKE', "%$search%")->orWhere('ci', 'LIKE', "%$search%");;
            })->get();
            $userIds = $usuarios->pluck('user_id');
            $registros = Crm_registros::where('batch_id', $batch_id)->get();
            $userIdsBatchs = $registros->pluck('user_id');
            $postulaciones = Crm_postulaciones::whereIn('user_id', $userIds)->whereNotIn('user_id', $userIdsBatchs)->with('users', 'users.datos_generales')->get();
            return response()->json(["mensaje" => "Ok", "datos" => $postulaciones], 200);
            //code...
        } catch (\Throwable $th) {
            return response()->json(["mensaje" => "Ok", "datos" => $th], 404);
            //throw $th;
        }
    }
    public function users_batch_all(string $id, Request $request)
    {
        try {
            $search = $request->input('search');

            $registros = Crm_registros::where("batch_id", $id)
                ->when($search, function ($query, $search) {
                    $query->whereHas('users.datos_generales', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%")
                            ->orWhere('apellido', 'like', "%{$search}%");
                    });
                })
                ->orderBy("user_id")
                ->with('users', 'users.datos_generales')
                ->with('batch')
                ->paginate(10);

            return response()->json(["mensaje" => "Datos cargados", "datos" => $registros], 200);
        } catch (\Throwable $th) {
            return response()->json(["mensaje" => "ERROR", "datos" => $th], 400);
        }
    }
    /*public function postulantes_aprobados()
    {
        try{
            $postulaciones = Crm_postulaciones::where('estado',1)->orderBY("batch_id")->with('users', 'users.datos_generales')->with('batch')->get();
            //->with('users', 'users.datos_generales')
            return response()->json(["mensaje" => "Datos cargados", "datos" => $postulaciones], 200);
        }catch (\Throwable $th){
            return response()->json(["mensaje"=>"ERROR","datos"=>$th],400); 
        }
    }
    public function postulantes_no_aprobados()
    {
        try{
            $postulaciones = Crm_postulaciones::where('estado','<>',1)->orderBY("batch_id")->with('users', 'users.datos_generales')->with('batch')->get();
            //->with('users', 'users.datos_generales')
            return response()->json(["mensaje" => "Datos cargados", "datos" => $postulaciones], 200);
        }catch (\Throwable $th){
            return response()->json(["mensaje"=>"ERROR","datos"=>$th],400); 
        }
    }
    public function postulantes_aprobados_batch(string $id){
        try{
            $postulaciones = Crm_postulaciones::where("batch_id",$id)->where('estado',1)->orderBY("batch_id")->with('users', 'users.datos_generales')->with('batch')->get();
            //->with('users', 'users.datos_generales')
            return response()->json(["mensaje" => "Datos cargados", "datos" => $postulaciones], 200);
        }catch (\Throwable $th){
            return response()->json(["mensaje"=>"ERROR","datos"=>$th],400); 
        }
    }
    public function postulantes_no_aprobados_batch(string $id){
        try{
            $postulaciones = Crm_postulaciones::where("batch_id",$id)->where('estado','<>',1)->where('user_id','<>',)->orderBY("batch_id")->with('users', 'users.datos_generales')->with('batch')->get();
            //->with('users', 'users.datos_generales')
            return response()->json(["mensaje" => "Datos cargados", "datos" => $postulaciones], 200);
        }catch (\Throwable $th){
            return response()->json(["mensaje"=>"ERROR","datos"=>$th],400); 
        }
    }*/
    public function users_batch_estudiantes(string $id, Request $request)
    {
        $search = $request->input('search', '');
    
        try {
            // Verifica que el rol 'estudiante' exista
            $estudianteRole = Role::where('name', 'estudiante')->first();
            if (!$estudianteRole) {
                return response()->json(["mensaje" => "Rol 'estudiante' no encontrado"], 404);
            }
    
            // Construye la consulta
            $query = Crm_registros::select('crm_registros.*')
                ->join('users', 'crm_registros.user_id', '=', 'users.id')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->where('model_has_roles.model_type', User::class)
                ->where('model_has_roles.role_id', $estudianteRole->id)
                ->where('crm_registros.batch_id', $id)
                ->where('crm_registros.is_deleted', 0);
    
            // Agrega la búsqueda si se proporciona un término de búsqueda
            if ($search) {
                $query->whereHas('users.datos_generales', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('apellido', 'like', "%{$search}%");
                });
            }
    
            // Ejecuta la consulta y obtiene los resultados paginados
            $registros = $query->orderBy('crm_registros.user_id')
                ->with('users', 'batch')
                ->with('users.datos_generales')
                ->paginate(10);
    
            // Retorna los datos
            return response()->json(["mensaje" => "Datos cargados", "datos" => $registros], 200);
    
        } catch (\Throwable $th) {
            // Maneja errores
            return response()->json(["mensaje" => "ERROR", "datos" => $th->getMessage()], 400);
        }
    }
}    
