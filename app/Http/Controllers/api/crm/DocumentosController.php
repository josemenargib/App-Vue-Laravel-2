<?php

namespace App\Http\Controllers\api\crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Crm_documentos;


class DocumentosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $Documentos = Crm_documentos::paginate(10);
            return response()->json(["mensaje"=>"OK","datos"=>$Documentos],200);
            //code...
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
            "registro_id"=>"required|exists:crm_registros,id",
            "nombre"=>"required",
            "numero_referencia"=>"required",
            "documento"=>"required|mimes:pdf,xlx,csv,doc,docx,jpg,png|max:2048"
        ]);
        try {
            $Documento = new Crm_documentos();
            $Documento->registro_id=$request->registro_id;
            $Documento->nombre = $request->nombre;
            $Documento->numero_referencia = $request->numero_referencia;
            if($request->file('documento')){
                $documento_aux = $request->file('documento');
                $nombreDocumento = $request->nombre.'.'.$request->file('documento')->extension();
                $documento_aux->move("Documentos/Documentos/".$request->registro_id.'/', $nombreDocumento);
                $Documento->storage_url = $nombreDocumento;
            }
            $Documento->save();
            return response()->json(["mensaje"=>"Created","datos"=>$Documento],201);
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
            $Documentos = Crm_documentos::find($id);
            return response()->json(["mensaje"=>"OK","datos"=>$Documentos],200);
            //code...
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
            "registro_id"=>"required|exists:crm_registros,id",
            "nombre"=>"required",
            "numero_referencia"=>"required",
            "documento"=>"required|mimes:pdf,xlx,csv,doc,docx,jpg,png|max:2048"
        ]);
        try {
            $Documento = Crm_documentos::find($id);
            $Documento->registro_id=$request->registro_id;
            $Documento->nombre = $request->nombre;
            $Documento->numero_referencia = $request->numero_referencia;
            if($request->file('documento')){
                if($Documento->storage_url){
                    unlink("Documentos/Documentos/".$Documento->registro_id.'/'.$Documento->storage_url);
                }
                $documento_aux = $request->file('documento');
                $nombreDocumento = $request->nombre.'.'.$request->file('documento')->extension();
                $documento_aux->move("Documentos/Documentos/".$request->registro_id.'/', $nombreDocumento);
                $Documento->storage_url = $nombreDocumento;
            }
            $Documento->save();
            return response()->json(["mensaje"=>"Updated","datos"=>$Documento],201);
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
            $Documento = Crm_documentos::find($id);
            if($Documento->storage_url){
                unlink("Documentos/Documentos/".$Documento->registro_id.'/'.$Documento->storage_url);
            }
            $Documento->delete();
            return response()->json(["mensaje"=>"Hard Delete","datos"=>$Documento],201);
        } catch (\Throwable $th) {
            return response()->json(["mensaje"=>"ERROR","datos"=>$th],404);
            //throw $th;
        }
        //
    }

    public function delete(string $id)
    {
        try {
            $Documento = Crm_documentos::find($id); 
            $Documento->is_deleted = !$Documento->is_deleted;
            $Documento->save();
            return response()->json(["mensaje"=>"Soft Delete","datos"=>$Documento],201);
        } catch (\Throwable $th) {
            return response()->json(["mensaje"=>"ERROR","datos"=>$th],404);
            //throw $th;
        }
        //
    }

    public function activos(){
        try {
            $documentos = Crm_documentos::where("is_deleted",false)->get();
            return response()->json(["mensaje"=>"OK","datos"=>$documentos],200); 
            //code...
        } catch (\Throwable $th) {
            return response()->json(["mensaje"=>"ERROR","datos"=>$th],400); 
            //throw $th;
        }
    }

    public function documentos_registro(string $id){
        try {
            $documentos = Crm_documentos::where("registro_id",$id)->get();
            return response()->json(["mensaje"=>"OK","datos"=>$documentos],200); 
            //code...
        } catch (\Throwable $th) {
            return response()->json(["mensaje"=>"ERROR","datos"=>$th],400); 
            //throw $th;
        }
    }
}
