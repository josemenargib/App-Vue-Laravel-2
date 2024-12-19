<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Crm_contratos;

class ContratosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $Contratos = Crm_contratos::paginate(10); 
            return response()->json(["mensaje"=>"OK","datos"=>$Contratos],200);
        } catch (\Throwable $th) {
            return response()->json(["mensaje"=>"ERROR","datos"=>$th],404);
            //throw $th;
        }

        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "estado"=>"required",
            "registro_id"=>"required|exists:crm_registros,id",
            "numero_contrato"=>"required",
            "documento"=>"required|mimes:pdf,xlx,csv,doc,docx|max:2048"
        ]);
        try {
            $Contrato = new Crm_contratos();
            $Contrato->estado = $request->estado;
            $Contrato->registro_id=$request->registro_id;
            $Contrato->numero_contrato = $request->numero_contrato;
            if($request->file('documento')){
                if($Contrato->storage_url){
                    unlink('Documentos/Contratos/'.$Contrato->storage_url);
                }
                $documento_aux = $request->file('documento');
                $nombreDocumento = $request->file('documento')->getClientOriginalName();
                $documento_aux->move("Documentos/Contratos/".$request->registro_id.'/', $nombreDocumento);
                $Contrato->storage_url = $nombreDocumento;
            }
            $Contrato->save();
            return response()->json(["mensaje"=>"Created","datos"=>$Contrato],201);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["mensaje"=>"ERROR","datos"=>$th],404);
        }
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $Contratos = Crm_contratos::find($id); 
            return response()->json(["mensaje"=>"OK","datos"=>$Contratos],200);
        } catch (\Throwable $th) {
            return response()->json(["mensaje"=>"ERROR","datos"=>$th],404);
            //throw $th;
        }
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "estado"=>"required",
            "registro_id"=>"required|exists:crm_registros,id",
            "numero_contrato"=>"required",
            "documento"=>"required|mimes:pdf,xlx,csv,doc,docx|max:2048"
        ]);
        try {
            $Contrato = Crm_contratos::find($id);
            $Contrato->registro_id = $request->registro_id;
            $Contrato->estado = $request->estado;
            $Contrato->registro_id=$request->registro_id;
            $Contrato->numero_contrato = $request->numero_contrato;
            if($request->file('documento')){
                if($Contrato->storage_url){
                    unlink("Documentos/Contratos/".$request->registro_id.'/'.$Contrato->storage_url);
                }
                $documento_aux = $request->file('documento');
                $nombreDocumento = $request->file('documento')->getClientOriginalName();
                $documento_aux->move("Documentos/Contratos/".$request->registro_id.'/', $nombreDocumento);
                $Contrato->storage_url = $nombreDocumento;
            }
            $Contrato->save();
            return response()->json(["mensaje"=>"Updated","datos"=>$Contrato],200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["mensaje"=>"ERROR","datos"=>$th],404);
        }
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $Contratos = Crm_contratos::find($id);
            if($Contratos->storage_url){
                unlink("Documentos/Contratos/".$Contratos->registro_id.'/'.$Contratos->storage_url);
            }
            $Contratos->delete();
            return response()->json(["mensaje"=>"Hard Delete"],204);
        } catch (\Throwable $th) {
            return response()->json(["mensaje"=>"ERROR","datos"=>$th],404);
            //throw $th;
        }
        //
    }

    public function delete(string $id)
    {
        try {
            $Contratos = Crm_contratos::find($id); 
            $Contratos->is_deleted = !$Contratos->is_deleted;
            $Contratos->save();
            return response()->json(["mensaje"=>"Soft Delete","datos"=>$Contratos],201);
        } catch (\Throwable $th) {
            return response()->json(["mensaje"=>"ERROR","datos"=>$th],404);
            //throw $th;
        }
        //
    }

    public function activos(){
        try {
            $contratos = Crm_contratos::where("is_deleted",false)->get();
            return response()->json(["mensaje"=>"OK","datos"=>$contratos],200); 
            //code...
        } catch (\Throwable $th) {
            return response()->json(["mensaje"=>"ERROR","datos"=>$th],400); 
            //throw $th;
        }
    }

    public function contratos_registro(string $id){
        try {
            $contratos = Crm_contratos::where("registro_id",$id)->get();
            return response()->json(["mensaje"=>"OK","datos"=>$contratos],200); 
            //code...
        } catch (\Throwable $th) {
            return response()->json(["mensaje"=>"ERROR","datos"=>$th],400); 
            //throw $th;
        }
    }
    public function estados(){
        try {
            $estados = Crm_contratos::select('estado')->groupBy('estado')->get();
            return response()->json(["mensaje" => "Ok", "datos" => $estados], 200);
            //code...
        } catch (\Throwable $th) {
            return response()->json(["mensaje" => "Ok", "datos" => $th], 404);
        }
    }

}
