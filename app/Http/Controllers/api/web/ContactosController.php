<?php

namespace App\Http\Controllers\api\web;

use App\Http\Controllers\Controller;
use App\Models\web_contactos;
use Illuminate\Http\Request;
use App\Mail\ContactosFormMail;
use Illuminate\Support\Facades\Mail;

class ContactosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $item = web_contactos::where('nombres', 'like', "%{$search}%")->orderBy('id', 'desc')->paginate(10);
        return response()->json(["mensaje"=>"Datos cargados", "datos"=>$item], 200);
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'required|string|max:20',
            'mensaje' => 'required|string',
        ]);
        // Guarda el mensaje en la base de datos
        $item =  new web_contactos();
        $item->nombres = $request->nombres;
        $item->apellidos = $request->apellidos;
        $item->email = $request->email;
        $item->telefono = $request->telefono;
        $item->mensaje = $request->mensaje;
        if ($item->save()) {
            $correo=new ContactosFormMail($request->mensaje, $request->email, $request->nombres, $request->apellidos);
            Mail::to(env('MAIL_FROM_ADDRESS'))->send($correo);
            return response()->json(["mensaje"=>"Envio Exitoso","datos"=>$item, "datos"=>$correo],200);
        }else{
            return response()->json(["mensaje" =>"No se logro realizar el registro"], 422);
        }
    }

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
        
        $item = web_contactos::find($id);
        $item->is_deleted = !$item->is_deleted;
        if ($item->save()) {
            return response()->json(["mensaje" => "Estado modificado", "datos" => $item], 202);
        }else{
            return response()->json(["mensaje" =>"No se logro realizar la modificación"], 422);
        }
        
    }
}
