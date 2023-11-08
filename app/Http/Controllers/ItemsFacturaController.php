<?php

namespace App\Http\Controllers;

use App\Models\items_factura;
use App\Models\inventario;
use Illuminate\Http\Request;
use Response;

class ItemsFacturaController extends Controller
{
    public function delItemFact(Request $req)
    {
        try {
            $id = $req->id;
            $items_factura = items_factura::find($id);
            $inv = inventario::find($items_factura->id_producto);
            $ctseter = $inv->cantidad - ($items_factura->cantidad);

            $descontar = (new InventarioController)->descontarInventario(
                $items_factura->id_producto,
                $ctseter, 

                $inv->cantidad, 
                null, 
                "delItemFact#".$items_factura->id_factura
            );

            if ($descontar) {
                $items_factura->delete();
                return Response::json(["msj"=>"Éxito al eliminar","estado"=>true]);
            }


            
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
            
        }
    }
}
