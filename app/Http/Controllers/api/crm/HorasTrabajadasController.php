<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use App\Models\Crm_horas_trabajadas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HorasTrabajadasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->id();

    
    $listarHoras = Crm_horas_trabajadas::where('user_id', $userId)->get();

    return response()->json(["mensaje" => "Datos cargados", "datos" => $listarHoras], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "description"=>"required",
            "date_start"=>"required",
            "date_finish"=>"required"
        ]);

        $registro=new Crm_horas_trabajadas();
        $registro->user_id=Auth::id();
        $registro->description=$request->description;
        $registro->date_start=$request->date_start;
        $registro->date_finish=$request->date_finish;
        $registro->save();

        return response()->json(["mensaje"=>"se ha agregado correctamente las horas de trabajo","datos"=>$registro],200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function listarTodos(){
        $listaProductos=Crm_horas_trabajadas::all();
        return response()->json(["mensaje"=>"datos cargados","datos"=>$listaProductos],200);
    }
}
