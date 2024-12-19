<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_datos_generales;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request) {
        $search = $request->input('search');
        $query = User::with('datos_generales');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%$search%")
                    ->orWhereHas('datos_generales', function($q) use ($search) {
                    $q->where('nombre', 'like', "%$search%")
                        ->orWhere('apellido', 'like', "%$search%");
                    });
            });
        }
        $usuarios = $query->paginate(10);
        return response()->json(["mensaje" => "Datos cargados", "datos" => $usuarios], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'apellido' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);
        DB::beginTransaction();    
        try {
            $usuario = User::create([
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);    
            $datos_generales = Crm_datos_generales::create([
                'user_id' => $usuario->id,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
            ]);
            $role = Role::firstOrCreate(['name' => 'Usuario']);
            $usuario->assignRole($role);
            DB::commit(); 
            return response()->json([
                'mensaje' => 'Usuario registrado',
                'usuario' => $usuario,
                'datos_generales' => $datos_generales
            ], 200); 
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'mensaje' => 'Error al registrar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeUserWithRole(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'apellido' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'rol' => 'required'
        ]);
        DB::beginTransaction();    
        try {
            $usuario = User::create([
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);    
            $datos_generales = Crm_datos_generales::create([
                'user_id' => $usuario->id,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
            ]);
            $role = Role::firstOrCreate(['name' => $request->rol]);
            $usuario->assignRole($role);
            DB::commit();    
            return response()->json([
                'mensaje' => 'Usuario registrado',
                'usuario' => $usuario,
                'datos_generales' => $datos_generales
            ], 200);            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'mensaje' => 'Error al registrar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id) {
        $usuario = User::with('datos_generales', 'roles')->find($id);
        return response()->json(["mensaje" => "Datos cargados", "datos" => $usuario], 200);
    }

    public function update_credentials(Request $request) {
        $usuario_id = Auth::id();
        $request->validate([
            "email" => "required|email|unique:users,email,$usuario_id",
            "password" => "min:8|confirmed"
        ]);
        $usuario = Auth::user();
        $usuario->email = $request->email;
        if ($request->filled('password')) {
            $usuario->password = bcrypt($request->password);
        }
        $usuario->save();
        return response()->json([
            'mensaje' => 'Registro modificado',
            'datos' => $usuario
        ], 200);
    }

    public function update_user_credentials(string $id, Request $request) {
        $request->validate([
            "email" => "required|email|unique:users,email,$id",
            "password" => "min:8|confirmed"
        ]);
        $usuario = User::findOrFail($id);
        $usuario->email = $request->email;
        if ($request->filled('password')) {
            $usuario->password = bcrypt($request->password);
        }
        $usuario->save();
        return response()->json([
            'mensaje' => 'Registro modificado',
            'datos' => $usuario
        ], 200);
    }

    public function update_datos_generales(Request $request) {
        dd($request->all()); 
        $datos_generales = Crm_datos_generales::where('user_id', Auth::id())->firstOrFail();
        $request->validate([
            'nombre' => 'required',
            'ci' => [
                'nullable',
                'unique:crm_datos_generales,ci,'.$datos_generales->id,
            ],
            'foto_perfil' => [
                'nullable',
                'mimes:jpg,png,jpeg|max:2048'            
            ]
        ]);
        $datos_generales->nombre=$request->nombre;
        if($request->hasFile('foto_perfil')) {
            if ($datos_generales->foto_perfil) {
                unlink('img/img_users/'.$datos_generales->foto_perfil);
            }
            $imagen = $request->file('foto_perfil');
            $nombreImagen = time().'.png';
            $imagen->move('img/img_users/', $nombreImagen);
            $datos_generales->foto_perfil = $nombreImagen;
        }
       
        $datos_generales->save();
        return response()->json([
            'mensaje' => 'Registro modificado',
            'datos' => $datos_generales
        ], 200);
    }

    public function update_datos_generales_admin(string $id, Request $request) {
        $datos_generales = Crm_datos_generales::where('user_id', $id)->firstOrFail();
        $request->validate([
            'nombre' => 'required',
            'ci' => [
                'nullable',
                'unique:crm_datos_generales,ci,'.$datos_generales->id,
            ],
            'fecha_nacimiento' => 'date',
        ]);
        $datos_generales->fill($request->only([
            'nombre',
            'apellido',
            'ci',
            'telefono',
            'pais',
            'ciudad',
            'direccion',
            'genero',
            'fecha_nacimiento'
        ]));
        $datos_generales->save();
        return response()->json([
            'mensaje' => 'Registro modificado',
            'datos' => $datos_generales
        ], 200);
    }

    public function destroy(string $id) {
        $usuario = User::find($id);
        $usuario->is_deleted = !$usuario->is_deleted;
        if($usuario->save()) {
            return response()->json([
                "mensaje" => "Estado modificado exitosamente.", 
                "datos" => $usuario]
            , 200);
        }else {
            return response()->json([
                "mensaje" => "Error al modificar estado."]
            , 422);
        } 
    }

    public function show_actives() {
        $usuarios = User::with('datos_generales')->where('is_deleted', false)->get();
        return response()->json(["mensaje" => "Datos cargados", "datos" => $usuarios], 200);
    }
    //OBTENER LOS PERMISOS DEL USUARIO AUTENTICADO
    public function obtenerPermisos(){
        $usuario = User::find(Auth::id());
        $permisos = $usuario->getPermissionsViaRoles();
        $nombresPermisos = $permisos->pluck('name')->toArray();
        return response()->json(['mensaje'=>'Permisos cargados', 'datos'=>$nombresPermisos],200);
    }

    public function getCantidadUsersEstudiantes()
    {
        try {
            // Obtén el rol de 'estudiante'
            $estudianteRole = Role::where('name', 'Estudiante')->first();

            if (!$estudianteRole) {
                return response()->json(["mensaje" => "Rol 'estudiante' no encontrado"], 404);
            }

            // Realiza el conteo de usuarios con el rol de 'estudiante'
            $numeroEstudiantes = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->where('model_has_roles.model_type', User::class)
                ->where('model_has_roles.role_id', $estudianteRole->id)
                ->where('users.is_deleted', 0) 
                ->count(); // Devuelve el número de usuarios

            return response()->json([
                "mensaje" => "Cantidad de estudiantes obtenida",
                "total_estudiantes" => $numeroEstudiantes
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(["mensaje" => "ERROR", "datos" => $th->getMessage()], 400);
        }
    }
}