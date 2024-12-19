<?php

namespace App\Http\Controllers\api\web;

use App\Http\Controllers\Controller;
use App\Models\Web_empresas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;

class Web_empresasController extends Controller
{
    public function lecturaWebEmpresas()
    {
        try {
            $empresa = Web_empresas::findOrFail(1);

            if ($empresa->url_banner != "" || $empresa->url_banner != null) {
                $url = asset('img/empresaBanners/' . $empresa->url_banner);
                $empresa->url_banner = $url;
            }
            return response()->json($empresa, 200);
        } catch (\Exception $e) {
            return response()->json("hubo un error:" . $e, 401);
        }
    }
    public function edicionWebEmpresas(Request $request)
    {
        if (Auth::user()->hasPermissionTo('empresa ver')) {
            try {
                $empresa = Web_empresas::find(1);

                $validator = Validator::make($request->all(), [
                    'razon_social' => 'required',
                    'nit' => 'required',
                    'direccion' => 'required',
                    'telefono' => 'required',
                    'ciudad' => 'required',
                    'pais' => 'required',
                    'representante_legal' => 'required',
                    'url_banner' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                    'mision' => 'required',
                    'vision' => 'required',
                    'about' => 'required',
                    'longitud' => 'required',
                    'latitud' => 'required',
                    'historia' => 'required'
                ]);

                if ($validator->fails()) {
                    // Obtener los errores en un formato simple
                    $errors = $validator->errors()->toArray();

                    // Convertir el array multidimensional a un formato simple
                    $simpleErrors = [];
                    foreach ($errors as $key => $messages) {
                        $simpleErrors[$key] = $messages[0]; // Solo tomamos el primer mensaje de cada campo
                    }

                    return response()->json($simpleErrors, 422);
                }

                if ($request->hasFile('url_banner')) {
                    $file = $request->file('url_banner');
                    $filename = time() . '.' . $file->getClientOriginalExtension(); // Nombre del archivo único
                    $destinationPath = public_path('img/empresaBanners'); // Ruta completa a public/empresaBanners
                    File::cleanDirectory($destinationPath);
                    $file->move($destinationPath, $filename);
                    // Guarda el nombre del archivo o la ruta relativa en la base de datos
                    $empresa->url_banner = $filename;
                }
                $empresa->razon_social = $request->razon_social;
                $empresa->nit = $request->nit;
                $empresa->direccion = $request->direccion;
                $empresa->telefono = $request->telefono;
                $empresa->ciudad = $request->ciudad;
                $empresa->pais = $request->pais;
                $empresa->representante_legal = $request->representante_legal;
                $empresa->mision = $request->mision;
                $empresa->vision = $request->vision;
                $empresa->about = $request->about;
                $empresa->latitud = $request->latitud;
                $empresa->longitud = $request->longitud;
                $empresa->historia = $request->historia;
                if ($empresa->save()) {
                    return response()->json("Modificado correctamente!", 201);
                }
            } catch (ValidationException $e) {
                return response()->json($e->errors(), 422);
            } catch (\Exception $e) {
                return response()->json("existe un error:" . $e->getMessage(), 500);
            }
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }
}
