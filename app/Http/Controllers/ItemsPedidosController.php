<?php

namespace App\Http\Controllers;

use App\Models\items_pedidos;
use App\Models\tareaslocal;

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
            (new PedidosController)->checkPedidoPago($iditem,"item");
            


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
            (new PedidosController)->checkPedidoPago($iditem,"item");
            

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

            $item = items_pedidos::find($req->index);

            $descuento = floatval($req->descuento);
            $isPermiso = (new TareaslocalController)->checkIsResolveTarea([
                "id_pedido" => $item->id_pedido,
                "tipo" => "descuentoUnitario",
            ]);

            if ((new UsuariosController)->isAdmin()) {
                (new PedidosController)->checkPedidoAuth($req->index,"item");
                (new PedidosController)->checkPedidoPago($req->index,"item");
                
                $item->descuento = $descuento;
                $item->save();
                return Response::json(["msj"=>"¡Éxito!","estado"=>true]);
                
            }elseif($isPermiso["permiso"]){

                if ($isPermiso["valoraprobado"]==$descuento) {
                    $item->descuento = $descuento;
                    $item->save();
                    return Response::json(["msj"=>"¡Éxito!","estado"=>true]);
                }else{
                    return Response::json(["msj"=>"Error: Valor no aprobado","estado"=>false]);

                }
            }else{

                $nuevatarea = (new TareaslocalController)->createTareaLocal([
                    "id_pedido" =>  $item->id_pedido,
                    "valoraprobado" => $descuento,
                    "tipo" => "descuentoUnitario",
                    "descripcion" => "Solicitud de descuento Unitario: ".$req->descuento."%",
                ]);
                if ($nuevatarea) {
                    return Response::json(["msj"=>"Debe esperar aprobación del Administrador","estado"=>false]);
                }

            }
            
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

            $descuento = floatval($req->descuento);
            $isPermiso = (new TareaslocalController)->checkIsResolveTarea([
                "id_pedido" => $req->index,
                "tipo" => "descuentoTotal",
            ]);
            
            if ((new UsuariosController)->isAdmin()) {
                (new PedidosController)->checkPedidoAuth($req->index);
                (new PedidosController)->checkPedidoPago($req->index);

                items_pedidos::where("id_pedido",$req->index)->update(["descuento"=>$descuento]);

                return Response::json(["msj"=>"¡Éxito!","estado"=>true]);
                
            }elseif($isPermiso["permiso"]){
                
                if ($isPermiso["valoraprobado"]==round($descuento,0)) {
                    items_pedidos::where("id_pedido",$req->index)->update(["descuento"=>$descuento]);

                    return Response::json(["msj"=>"¡Éxito!","estado"=>true]);
                }else{
                    return Response::json(["msj"=>"Error: Valor no aprobado","estado"=>false]);

                }
            }else{

                $nuevatarea = (new TareaslocalController)->createTareaLocal([
                    "id_pedido" =>  $req->index,
                    "valoraprobado" => round($descuento,0),
                    "tipo" => "descuentoTotal",
                    "descripcion" => "Solicitud de descuento Total: ".round($descuento,0)." %",
                ]);
                if ($nuevatarea) {
                    return Response::json(["msj"=>"Debe esperar aprobación del Administrador","estado"=>false]);
                }

            }
            
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
        }


    }


    
}
