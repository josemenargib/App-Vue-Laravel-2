<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_curriculas;
use Illuminate\Http\Request;

class CurriculaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $search = $request->input('search', '');

    $item = Crm_curriculas::orderBy('id', 'desc')
        ->with([
            'tecnologia' => function ($query) use ($search) {
                $query->select('id', 'nombre')
                    ->where('nombre', 'like', '%' . $search . '%');
            },
            'especialidad' => function ($query) use ($search) {
                $query->select('id', 'nombre')
                    ->where('nombre', 'like', '%' . $search . '%');
            },
            'modulo' => function ($query) use ($search) {
                $query->select('id', 'nombre')
                    ->where('nombre', 'like', '%' . $search . '%');
            },
        ])
        ->where(function ($query) use ($search) {
            $query->where('id', 'like', '%' . $search . '%')
                ->orWhereHas('tecnologia', function ($query) use ($search) {
                    $query->where('nombre', 'like', '%' . $search . '%');
                })
                ->orWhereHas('especialidad', function ($query) use ($search) {
                    $query->where('nombre', 'like', '%' . $search . '%');
                })
                ->orWhereHas('modulo', function ($query) use ($search) {
                    $query->where('nombre', 'like', '%' . $search . '%');
                });
        })
        ->paginate(10);

    return response()->json(["mensaje"=> "Datos cargados", "datos" => $item], 200);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "tecnologia_id" => "required",
            "especialidad_id" => "required",
            "modulo_id" => "required"
        ]);
        $item = new Crm_curriculas();
        $item->tecnologia_id = $request->tecnologia_id;
        $item->especialidad_id = $request->especialidad_id;
        $item->modulo_id = $request->modulo_id;
        if ($item->save()) {
            return response()->json(["mensaje" => "Registro exitoso", "datos" => $item], 200);
        } else {
            return response()->json(["mensaje" => "No se pudo realizar el registro"], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Crm_curriculas::find($id);
        return response()->json(["mensaje" => "Dato cargado", "datos" => $item], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "tecnologia_id" => "required",
            "especialidad_id" => "required",
            "modulo_id" => "required"
        ]);
        $item = Crm_curriculas::find($id);
        $item->tecnologia_id = $request->tecnologia_id;
        $item->especialidad_id = $request->especialidad_id;
        $item->modulo_id = $request->modulo_id;
        if ($item->save()) {
            return response()->json(["mensaje" => "Registro modificado", "datos" => $item], 201);
        } else {
            return response()->json(["mensaje" => "No se pudo realizar la modificacion"], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Crm_curriculas::find($id);
        $item->is_deleted = !$item->is_deleted;
        if ($item->save()) {
            return response()->json(["mensaje" => "Estado modificado", "datos" => $item], 202);
        } else {
            return response()->json(["mensaje" => "No se pudo modifcar el estado"], 422);
        }
    }
    public function curriculasActivos()
    {
        $item = Crm_curriculas::where('is_deleted', false)->get();
        return response()->json(["mensaje" => "Datos cargados", "datos" => $item]);
    }
}
