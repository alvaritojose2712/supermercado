<?php

namespace App\Http\Controllers;

use App\Models\pagos_referencias;
use Illuminate\Http\Request;
use Response;

class PagosReferenciasController extends Controller
{
    public function addRefPago(Request $req)
    {
         try {
            
            (new PedidosController)->checkPedidoAuth($req->id_pedido);

            $item = new pagos_referencias;
            $item->tipo = $req->tipo;
            $item->descripcion = $req->descripcion;
            $item->monto = $req->monto;
            $item->banco = $req->banco;
            $item->id_pedido = $req->id_pedido;
            $item->save();

            return Response::json(["msj"=>"¡Éxito!","estado"=>true]);
            
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
        }
    }
    public function delRefPago(Request $req)
    {
         try {
            $id = $req->id;
            $pagos_referencias = pagos_referencias::find($id);
            if ($pagos_referencias) {
                $pagos_referencias->delete();
                return Response::json(["msj"=>"Éxito al eliminar","estado"=>true]);
            }


            
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
            
        }
    }
}
