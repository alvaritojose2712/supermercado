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
            $inv->cantidad = $inv->cantidad - ($items_factura->cantidad);
            if ($inv->save()) {
                $items_factura->delete();
                return Response::json(["msj"=>"Éxito al eliminar","estado"=>true]);
            }


            
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
            
        }
    }
}
