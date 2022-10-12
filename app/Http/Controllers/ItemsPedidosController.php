<?php

namespace App\Http\Controllers;

use App\Models\items_pedidos;
use Illuminate\Http\Request;
use Response;

class ItemsPedidosController extends Controller
{
    

    public function changeEntregado(Request $req)
    {
       try {
            $id = $req->id;
            $item = items_pedidos::find($id);
            if ($item) {
                if ($item->entregado) {
                    $item->entregado = false;
                }else{
                    $item->entregado = true;
                }
                $item->save();
                

            }

            
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
        } 
    }
    public function delItemPedido(Request $req)
    {   
        return (new InventarioController)->hacer_pedido($req->index,null,99,"del");
    }
    public function setCtxBultoCarrito(Request $req)
    {
        try {
            $iditem = $req->iditem;
            (new PedidosController)->checkPedidoAuth($iditem,"item");


            $item = items_pedidos::with("producto")->find($iditem);
            if ($item) {
                $ct = intval($req->ct);
                $bulto = $item->producto->bulto;
                if ($ct&&$bulto) {
                    return (new InventarioController)->hacer_pedido($iditem,null,floatval($ct*$bulto),"upd");
                }

            }

            
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
        }
    }
    public function setPrecioAlternoCarrito(Request $req)
    {

        try {
            $iditem = $req->iditem;
            $p = $req->p;

            (new PedidosController)->checkPedidoAuth($iditem,"item");

            $item = items_pedidos::with("producto")->find($iditem);
            if ($p=="p1"||$p=="p2") {
                if ($item) {
                    if ($p=="p1") {
                        $p1 = $item->producto->precio1;
                    }
                    if ($p=="p2") {
                        $p1 = $item->producto->precio2;
                    }
                    $ct = $item->cantidad;
                    $monto = $item->monto;

                    $objetivo = $ct*$p1;

                    $porcentaje_objetivo = (100-((floatval($objetivo)*100)/$monto));

                    // let total = parseFloat(pedidoData.clean_subtotal)

                    // descuento = (100-((parseFloat(descuento)*100)/total))

                    if ($porcentaje_objetivo) {
                        $item->descuento = floatval($porcentaje_objetivo);
                        $item->save();
                    }
                    

                    // return Response::json(["msj"=>"¡Éxito!","estado"=>true]);
                    return Response::json(["msj"=>"p1 $p1","estado"=>true]);
                }
            }

            
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
        }
    }


    public function setDescuentoUnitario(Request $req)
    {


        try {
            (new PedidosController)->checkPedidoAuth($req->index,"item");

            $item = items_pedidos::find($req->index);
            $item->descuento = floatval($req->descuento);
            $item->save();

            return Response::json(["msj"=>"¡Éxito!","estado"=>true]);
            
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
        }
    }

    public function setCantidad(Request $req)
    {
        return (new InventarioController)->hacer_pedido($req->index,null,floatval($req->cantidad),"upd");
    }

    
    public function setDescuentoTotal(Request $req)
    {
        try {
            (new PedidosController)->checkPedidoAuth($req->index);
            
            $item = items_pedidos::where("id_pedido",$req->index)->update(["descuento"=>floatval($req->descuento)]);
            return Response::json(["msj"=>"¡Éxito!","estado"=>true]);
            
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
        }


    }


    
}
