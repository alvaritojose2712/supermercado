<?php

namespace App\Http\Controllers;

use App\Models\clientes;
use Illuminate\Http\Request;
use Response;

class ClientesController extends Controller
{
    public function getpersona(Request $req)
    {   
        $num = 25;
        if ($req->num) {
            $num = $req->num;
        }
        return clientes::where("nombre","LIKE","%".$req->q."%")->orWhere("identificacion","LIKE","%".$req->q."%")
        ->limit($num)
        ->orderBy("id","desc")
        ->get();
    }
    public function setClienteCrud(Request $req)
    {   
        try{
            $arr_sea = [];

            if (!$req->id) {
                $arr_sea = [
                    "identificacion" => $req->clienteInpidentificacion
                ];
            }else{
                $arr_sea = [
                    "id" => $req->id
                ];
            }
            $cli = clientes::updateOrCreate($arr_sea,[
                "identificacion" => $req->clienteInpidentificacion,
                "nombre" => $req->clienteInpnombre,
                "correo" => $req->clienteInpcorreo,
                "direccion" => $req->clienteInpdireccion,
                "telefono" => $req->clienteInptelefono,
                "estado" => $req->clienteInpestado,
                "ciudad" => $req->clienteInpciudad
            ]);

            return Response::json(["msj"=>"Éxito al cliente","estado"=>true, "id" => $cli->id]);   
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
            
        } 
    }
    public function delCliente(Request $req)
    {
        $id = $req->id;
        try {
            if ($id==1) {
                return Response::json(["msj"=>"Error: no puede eliminar CF","estado"=>false]);   

            }else{
                clientes::find($id)->delete();

            }
            return Response::json(["msj"=>"Éxito al eliminar","estado"=>true]);   
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error al eliminar. ".$e->getMessage(),"estado"=>false]);
            
        } 
    }
    
    
}
