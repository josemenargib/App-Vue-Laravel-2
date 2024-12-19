<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_datos_generales;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            "email" => "required|email",
            "password" => "required|string"
        ]);
        if (!Auth ::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $usuario = Auth::user()->load('datos_generales')->load('roles');
        $token = $usuario->createToken('auth_token')->plainTextToken;
        return response()->json(['message' => 'Sesión iniciada', 'access_token' => $token, "user" => $usuario, 'token_type' => 'Bearer'], 200);
    }

    public function registerAndLogin(Request $request) 
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
            $role = Role::firstOrCreate(['name' => 'postulante']);
            $usuario->assignRole($role);
            DB::commit(); 
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $usuarioLoggeado = Auth::user()->load('datos_generales')->load('roles');
            $token = $usuarioLoggeado->createToken('auth_token')->plainTextToken;
            return response()->json(['message' => 'Sesión iniciada', 'access_token' => $token, "user" => $usuarioLoggeado, 'token_type' => 'Bearer'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'mensaje' => 'Error al registrar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout() {
        Auth::user()->tokens()->delete();
        return response()->json(["mensaje" => "Sesion finalizada"], 200);
    }
}
