<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_certificaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CertificacionesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(["mensaje" => "Usuario no autenticado."], 401);
        }

        $search = $request->get('search');

        $items = Crm_certificaciones::where('is_deleted', false)
                                    ->when($search, function ($query) use ($search) {
                                        $query->where('nombre', 'LIKE', "%".e($search)."%");
                                    })
                                    ->paginate(10);
    
        return response()->json([
            "mensaje" => "Datos cargados",
            "datos" => $items
        ], 200);
    }

    public function store(Request $request)
    {

        if (Auth::user()->hasPermissionTo('certificacion crear')) {
            
            $request->validate([
                'nombre' => 'required|string|max:255',
                'user_id' => 'required|exists:users,id', 
                'storage_url' => 'nullable|file|mimes:pdf,doc,docx,jpg,png',
            ], [
                'nombre.required' => 'El nombre es requerido para registrar una nueva certificación',
                'user_id.required' => 'El ID de usuario es requerido',
                'user_id.exists' => 'El ID de usuario especificado no existe',
                'storage_url.mimes' => 'El formato del archivo no está permitido, debe ser PDF, DOC, DOCX, JPG o PNG',
            ]);
    
            try {
                DB::beginTransaction();
                
                $fileUrl = null;
                
                if ($request->hasFile('storage_url')) {
                    $file = $request->file('storage_url');
                    $fileName = time() . '.' . $file->extension();
                    $file->move(public_path('Documentos/Certificados'), $fileName);
                    $fileUrl = 'Documentos/Certificados/' . $fileName;
                }
    
                $certificacion = Crm_certificaciones::create([
                    'nombre' => $request->nombre,
                    'storage_url' => $fileUrl,
                    'user_id' => $request->user_id, 
                    'is_deleted' => false,
                ]);
    
                DB::commit();
                return response()->json([
                    "mensaje" => "Certificación registrada.",
                    "datos" => $certificacion,
                    "file_url" => $fileUrl
                ], 201);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json(["mensaje" => "Error: $th"], 422);
            }
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta acción"], 403);
        }
    }
    

    public function show($id)
    {
        // Obtener la certificación con la relación del usuario y datos generales
        $certificacion = Crm_certificaciones::with('user.datos_generales')
                                             ->where('id', $id)
                                             ->first();
        
        if (!$certificacion) {
            return response()->json(["mensaje" => "Certificación no encontrada."], 404);
        }
    
        return response()->json([
            "mensaje" => "Datos cargados",
            "datos" => [
                "certificacion" => $certificacion,

            ]
        ], 200);
    }
    

public function update(Request $request, $id)
{
    if (Auth::user()->hasPermissionTo('certificacion editar')) {
        $request->validate([
            'nombre' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'storage_url' => 'nullable|file|mimes:pdf,doc,docx,jpg,png',
        ], [
            'nombre.string' => 'El nombre debe ser una cadena de texto',
            'user_id.exists' => 'El ID de usuario especificado no existe',
            'storage_url.mimes' => 'El formato del archivo no está permitido, debe ser PDF, DOC, DOCX, JPG o PNG',
        ]);

        $certificacion = Crm_certificaciones::where('id', $id)
                                             ->first();

        if (!$certificacion) {
            return response()->json(["mensaje" => "Certificación no encontrada."], 404);
        }

        $fileUrl = $certificacion->storage_url;

        if ($request->hasFile('storage_url')) {

            if ($fileUrl && file_exists(public_path($fileUrl))) {
                unlink(public_path($fileUrl));
            }

            $file = $request->file('storage_url');
            $fileName = time() . '.' . $file->extension();
            $file->move(public_path('Documentos/Certificados'), $fileName);
            $fileUrl = 'Documentos/Certificados/' . $fileName;
        }
        $certificacion->update([
            'nombre' => $request->input('nombre', $certificacion->nombre),
            'user_id' => $request->input('user_id', $certificacion->user_id), 
            'storage_url' => $fileUrl,
        ]);

        return response()->json([
            "mensaje" => "Certificación actualizada.",
            "datos" => $certificacion,
            "file_url" => $fileUrl
        ], 200);
    } else {
        return response()->json(["message" => "No tienes permiso para realizar esta acción"], 403);
    }
}


public function destroy($id)
{
    if (Auth::user()->hasPermissionTo('certificacion borrar')) {
        $certificacion = Crm_certificaciones::where('id', $id)->first();
        if (!$certificacion) {
            return response()->json(["mensaje" => "Certificación no encontrada."], 404);
        }

        $nuevoEstado = !$certificacion->is_deleted;
        $certificacion->update(['is_deleted' => $nuevoEstado]);

        $mensaje = $nuevoEstado ? "Certificación inhabilitada." : "Certificación habilitada.";

        return response()->json(["mensaje" => $mensaje], 200);
    } else {
        return response()->json(["mensaje" => "No tienes permiso para realizar esta acción"], 403);
    }
}


    public function certificacionesByUser(Request $request)
    {
        $userId = Auth::id(); 
        if (!$userId) {
            return response()->json(["mensaje" => "Usuario no autenticado"], 401);
        }
    
        $search = $request->input('search', '');
        $pagina = $request->input('page', 1);
    
        $query = Crm_certificaciones::where('user_id', $userId);
      
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%");
            });
        }
    
        $query->orderBy('created_at', 'desc');
        $certificaciones = $query->paginate(10, ['*'], 'page', $pagina);
    
        return response()->json(["mensaje" => "Datos cargados", "datos" => $certificaciones], 200);
    }
    

    public function getAllCertificaciones(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(["mensaje" => "Usuario no autenticado"], 401);
        }
    
        $search = $request->input('search', '');
        $pagina = $request->input('page', 1);
    
        $query = Crm_certificaciones::with('user.datos_generales') // Cargar datos generales del usuario
            ->orderBy('updated_at', 'desc');
    
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                      ->orWhereHas('user', function($query) use ($search) {
                          $query->where('nombre', 'like', "%{$search}%");
                      });
            });
        }
    
        $certificaciones = $query->paginate(10, ['*'], 'page', $pagina);
    
        return response()->json([
            "mensaje" => "Datos cargados",
            "datos" => $certificaciones
        ], 200);
    }
    
}    
