<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_curriculas;
use App\Models\Crm_especialidades;
use App\Models\Crm_modulos;
use App\Models\Crm_tecnologias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EspecialidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $item = Crm_especialidades::when($search, function ($query) use ($search) {
            $query->where('nombre', 'LIKE', "%{$search}%");
        })
            ->orderBy("id", "desc")
            ->paginate(10);
        $item->transform(function ($item) {
            $item->imagen = asset('img/img_especialidad/' . $item->imagen);
            return $item;
        });
        return response()->json(["mensaje" => "Datos cargados", "datos" => $item], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->hasPermissionTo('especialidad crear')) {
            $request->validate([
                "nombre" => "required",
                "imagen" => "required|mimes:png,jpg,jpeg",
                "cardModulos" => "required",
            ]);
            $cardModulos = json_decode($request->cardModulos);
            /* return response()->json($cardModulos[1]->tecnologias[0]->nombre); */
            if(count($cardModulos) > 0 ){
                try {
                    DB::beginTransaction();
                    $item = new Crm_especialidades();
                    $item->nombre = $request->nombre;
                    $item->descripcion_corta = $request->descripcion_corta;
                    $item->descripcion_larga = $request->descripcion_larga;
                    if ($request->file('imagen')) {
                        $imagen = $request->file('imagen');
                        $nombreImagen = time() . '.png';
                        $imagen->move("img/img_especialidad/", $nombreImagen);
                        $item->imagen = $nombreImagen;
                    }
                    $item->save();
                    foreach ($cardModulos as $row) {
                        foreach ($row->tecnologias as $tecno) {
                            $item2 = new Crm_curriculas();
                            $item2->tecnologia_id = $tecno->id;
                            $item2->especialidad_id = $item->id;
                            $item2->modulo_id = $row->id;
                            $item2->save();
                        }
                    }
                    DB::commit();
                    return response()->json(["mensaje" => "Registro exitoso", "datos" => $item], 200);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return response()->json(["mensaje" => "No se pudo realizar el registro: $th"], 406);
                }
            }else{
                return response()->json(["mensaje" => "No se ha seleccionado ningún módulo"], 406);
            }
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (Auth::user()->hasPermissionTo('especialidad ver')) {
            $item = Crm_especialidades::find($id);
            $curricula = $item->curriculas->groupBy('modulo.id')->map(function ($curriculas, $moduloId) {
                return [
                    'nombre' => $curriculas->first()->modulo->nombre,
                    'id' => $moduloId,
                    'tecnologias' => $curriculas->map(function ($curricula) {
                        return [
                            'id' => $curricula->tecnologia->id,
                            'nombre' => $curricula->tecnologia->nombre,
                        ];
                    })->values()->all(),
                ];
            })->values()->all();
            $imagen = asset('img/img_especialidad/' . $item->imagen);
            return response()->json([
                "mensaje" => "Dato cargado",
                "dato" => $item,
                'imagen' => $imagen,
                'curriculas' => $curricula
            ], 200);
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (Auth::user()->hasPermissionTo('especialidad editar')) {
            $request->validate([
                "nombre" => "required",
                "cardModulos" => "required",
            ]);
            $cardModulos = json_decode($request->cardModulos);
            $item = Crm_especialidades::find($id);
            if (!$item) {
                return response()->json(["mensaje" => "Registro no encontrado"], 404);
            }
            try {
                DB::beginTransaction();
                $item->nombre = $request->nombre;
                $item->descripcion_corta = $request->descripcion_corta;
                $item->descripcion_larga = $request->descripcion_larga;
                if ($request->hasFile('imagen')) {
                    if ($item->imagen) {
                        unlink('img/img_especialidad/' . $item->imagen);
                    }
                    $imagen = $request->file('imagen');
                    $nombreImagen = time() . '.png';
                    $imagen->move("img/img_especialidad/", $nombreImagen);
                    $item->imagen = $nombreImagen;
                }
                $item->save();
                // Eliminar los módulos actuales
                $item->curriculas()->delete();
                foreach ($cardModulos as $row) {
                    foreach ($row->tecnologias as $tecno) {
                        $item2 = new Crm_curriculas();
                        $item2->tecnologia_id = $tecno->id;
                        $item2->especialidad_id = $item->id;
                        $item2->modulo_id = $row->id;
                        $item2->save();
                    }
                }
                DB::commit();
                $imageUrl = asset('img/img_especialidad/' . $item->imagen);
                return response()->json(["mensaje" => "Registro modificado", "datos" => $item, "imagen_url" => $imageUrl], 201);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json(["mensaje" => "No se pudo realizar la modificación: $th"], 406);
            }
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }
    public function destroy(string $id) //+
    {
        if (Auth::user()->hasPermissionTo('especialidad borrar')) {
            $item = Crm_especialidades::find($id);
            $item->is_deleted = !$item->is_deleted;
            if ($item->save()) {
                return response()->json(["mensaje" => "Estado modificado", "datos" => $item], 202);
            } else {
                return response()->json(["mensaje" => "No se pudo modifcar el estado"], 422);
            }
        } else {
            return response()->json(["message" => "No tienes permiso para realizar esta accion"], 403);
        }
    }
    public function especialidadesActivas(Request $request)
    {
        $search = $request->input('search');
        $item = Crm_especialidades::where('is_deleted', false)
            ->with('curriculas.tecnologia', 'curriculas.modulo')
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'LIKE', "%{$search}%");
            })
            ->get();
        $item->transform(function ($item) {
            $imagenPath = 'img/img_especialidad/' . $item->imagen;
            $item->imagen = file_exists($imagenPath) ? asset($imagenPath) : null;
            if ($item->curriculas) {
                $item->curriculas->transform(function ($curricula) {
                    if ($curricula->tecnologia) {
                        $imagenPathTecnologia = 'img/img_tecnologias/' . $curricula->tecnologia->imagen;
                        $curricula->tecnologia->imagen = file_exists($imagenPathTecnologia) ? asset($imagenPathTecnologia) : null;
                    }
                    if ($curricula->modulo) {
                        $imagenPathModulo = 'img/img_modulos/' . $curricula->modulo->imagen;
                        $curricula->modulo->imagen = file_exists($imagenPathModulo) ? asset($imagenPathModulo) : null;
                    }
                    return $curricula;
                });
            }

            return $item;
        });
        return response()->json(["mensaje" => "Datos cargados", "datos" => $item]);
    }

    public function showActivas(string $id)
    {
        $item = Crm_especialidades::find($id);
        $curricula = $item->curriculas->groupBy('modulo.id')->map(function ($curriculas, $moduloId) {
            return [
                'nombre' => $curriculas->first()->modulo->nombre,
                'id' => $moduloId,
                'tecnologias' => $curriculas->map(function ($curricula) {
                return [
                    'id' => $curricula->tecnologia->id,
                    'nombre' => $curricula->tecnologia->nombre,
                ];
                })->values()->all(),
            ];
        })->values()->all();
        $imagen = asset('img/img_especialidad/' . $item->imagen);
        return response()->json([
            "mensaje" => "Dato cargado",
            "dato" => $item,
            'imagen' => $imagen,
            'curriculas' => $curricula
        ], 200);
    }
}
