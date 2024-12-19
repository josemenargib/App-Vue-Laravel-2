<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_experiencias;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class ExperienciasController extends Controller
{

    public function index()
{
    $userId = Auth::id();
    if (!$userId) {
        return response()->json(["mensaje" => "Usuario no autenticado"], 401);
    }

    $items = Crm_experiencias::where('is_deleted', false)
                            ->with('user.datos_generales')
                            ->paginate(10);

    return response()->json(["mensaje" => "Datos cargados", "datos" => $items], 200);
}


    public function store(Request $request)
    {
        if (Auth::user()->hasPermissionTo('experiencia crear')) {
            Log::info('Payload recibido para almacenar experiencia: ', $request->all());
    
            $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                'actualidad' => 'nullable|boolean',
                'user_id' => 'required|exists:users,id'
            ]);
    
            $userId = $request->input('user_id');
    
            $data = [
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->actualidad ? null : $request->fecha_fin,
                'actualidad' => $request->actualidad ? 1 : 0,
                'user_id' => $userId,
                'is_deleted' => false,
            ];
    
            Log::info('Datos que se guardarán en la base de datos: ', $data);
    
            $experiencia = Crm_experiencias::create($data);
    
            return response()->json(["mensaje" => "Nueva experiencia registrada", "datos" => $experiencia], 201);
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta acción"], 403);
        }
    }
    
    
    public function show($id)
    {
        if (Auth::user()->hasPermissionTo('experiencia ver')) {
        
            $experiencia = Crm_experiencias::where('id', $id)
                                           ->with('user.datos_generales') 
                                           ->first();
 
            if (!$experiencia) {
                return response()->json(["mensaje" => "Experiencia no encontrada."], 404);
            }
            
            return response()->json(["mensaje" => "Datos cargados", "datos" => $experiencia], 200);
        } else {
            return response()->json(["mensaje" => "No tienes permiso para realizar esta acción"], 403);
        }
    }
    
    

    public function update(Request $request, $id)
    {
        if (Auth::user()->hasPermissionTo('experiencia editar')) {
            Log::info('Payload recibido para actualizar experiencia: ', $request->all());
    
            $request->validate([
                'nombre' => 'nullable|string|max:255',
                'descripcion' => 'nullable|string',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                'actualidad' => 'nullable|boolean',
                'user_id' => 'required|exists:users,id'
            ]);
    
            $userId = $request->input('user_id');
            
            $experiencia = Crm_experiencias::find($id);
    
            if (!$experiencia) {
                return response()->json(["mensaje" => "Experiencia no encontrada."], 404);
            }
    
    
            $data = [
                'nombre' => $request->input('nombre', $experiencia->nombre),
                'descripcion' => $request->input('descripcion', $experiencia->descripcion),
                'fecha_inicio' => $request->input('fecha_inicio', $experiencia->fecha_inicio),
                'fecha_fin' => $request->input('fecha_fin', $experiencia->fecha_fin),
                'actualidad' => $request->input('actualidad', $experiencia->actualidad) ? 1 : 0,
                'user_id' => $userId  
            ];
    
            Log::info('Datos actualizados en la base de datos: ', $data);
    
            $experiencia->update($data);
    
            return response()->json(["mensaje" => "Experiencia actualizada con éxito.", "datos" => $experiencia], 200);
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta acción"], 403);
        }
    }
    
    

    public function destroy($id)
    {
        // Verifica si el usuario tiene permiso para borrar la experiencia
        if (Auth::user()->hasPermissionTo('experiencia borrar')) {
            // Busca la experiencia por ID
            $experiencia = Crm_experiencias::find($id); // Usa find() para obtener el modelo por su ID
    
            // Si no se encuentra la experiencia, devuelve un error 404
            if (!$experiencia) {
                return response()->json(["mensaje" => "Experiencia no encontrada."], 404);
            }
    
            // Cambia el estado de la experiencia (habilitada/deshabilitada)
            $nuevoEstado = !$experiencia->is_deleted;
            $experiencia->update(['is_deleted' => $nuevoEstado]);
    
            // Mensaje basado en el nuevo estado
            $mensaje = $nuevoEstado ? "Experiencia inhabilitada." : "Experiencia activada.";
    
            // Devuelve la respuesta con el mensaje y estado
            return response()->json(["mensaje" => $mensaje], 200);
        } else {
            // Si el usuario no tiene permiso, devuelve un error 403
            return response()->json(["mensaje" => "No tienes permiso para realizar esta acción"], 403);
        }
    }
    


    public function experienciasByUser(Request $request)
{
    $userId = Auth::id();
    if (!$userId) {
        return response()->json(["mensaje" => "Usuario no autenticado"], 401);
    }

    $search = $request->input('search', '');
    $pagina = $request->input('page', 1);

    $query = Crm_experiencias::where('user_id', $userId);

    if ($search) {
        $query->where(function ($query) use ($search) {
            $query->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
        });
    }

    $query->orderByRaw('CASE WHEN actualidad = 1 THEN 0 ELSE 1 END')
          ->orderBy('fecha_fin', 'desc');

    $items = $query->with('user.datos_generales') 
                  ->paginate(10, ['*'], 'page', $pagina);

    return response()->json(["mensaje" => "Datos cargados", "datos" => $items], 200);
}

    
    

public function getAllExperiencias(Request $request)
{
    $userId = Auth::id();
    if (!$userId) {
        return response()->json(["mensaje" => "Usuario no autenticado"], 401);
    }

    $search = $request->input('search', '');
    $pagina = $request->input('page', 1);

    $query = Crm_experiencias::with('user.datos_generales')
                             ->orderByRaw('CASE WHEN actualidad = 1 THEN 0 ELSE 1 END')
                             ->orderBy('fecha_fin', 'desc');

    if ($search) {
        $query->where(function ($query) use ($search) {
            $query->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
        });
    }

    $items = $query->paginate(10, ['*'], 'page', $pagina);

    return response()->json(["mensaje" => "Datos cargados", "datos" => $items], 200);
}



   
    public function obtenerUsuariosNoEstudiantes()
    {
        try {

            $excludedRoles = Role::whereIn('name', ['estudiante', 'egresado', 'postulante'])
                ->pluck('id');
    
            if ($excludedRoles->isEmpty()) {
                return response()->json(["mensaje" => "No se encontraron los roles excluidos"], 404);
            }
    
            $usuarios = User::whereDoesntHave('roles', function ($query) use ($excludedRoles) {
                $query->whereIn('id', $excludedRoles);
            })
            ->with('datos_generales')
            ->get(['id', 'email']) 
            ->map(function ($usuario) {
                return [
                    'id' => $usuario->id,
                    'nombre' => $usuario->datos_generales->nombre ?? '',
                    'apellido' => $usuario->datos_generales->apellido ?? ''
                ];
            });
    
            return response()->json(["mensaje" => "Datos cargados", "datos" => $usuarios], 200);
        } catch (\Throwable $th) {
            Log::error('Error al obtener usuarios: ' . $th->getMessage());
            return response()->json(["mensaje" => "ERROR", "datos" => $th->getMessage()], 500);
        }
    }
    
    
    
}