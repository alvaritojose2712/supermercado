<?php

namespace App\Http\Controllers;

use App\Models\pedidos;
use App\Models\inventario;
use App\Models\items_pedidos;
use App\Models\factura;
use App\Models\items_factura;
use App\Models\fallas;
use App\Models\proveedores;
use App\Models\sucursal;
use App\Models\categorias;
use App\Models\clientes;
use App\Models\cierres;


use App\Models\movimientos;
use App\Models\items_movimiento;

use DB;


use Illuminate\Http\Request;
use Response;

class InventarioController extends Controller
{

    public function guardarDeSucursalEnCentral(Request $req)
    {
        $producto = $req->producto;
        try{
            $id = $this->guardarProducto([
                "codigo_proveedor" => $producto["codigo_proveedor"],
                "codigo_barras" => $producto["codigo_barras"],
                "id_proveedor" => $producto["id_proveedor"],
                "id_categoria" => $producto["id_categoria"],
                "id_marca" => $producto["id_marca"],
                "unidad" => $producto["unidad"],
                "descripcion" => $producto["descripcion"],
                "iva" => $producto["iva"],
                "precio_base" => $producto["precio_base"],
                "precio" => $producto["precio"],
                "cantidad" => $producto["cantidad"],
                "bulto" => $producto["bulto"],
                "precio1" => $producto["precio1"],
                "precio2" => $producto["precio2"],
                "precio3" => $producto["precio3"],
                "stockmin" => $producto["stockmin"],
                "stockmax" => $producto["stockmax"],
    
                "id_deposito" => "",
                "porcentaje_ganancia" => 0,
                
                "id_factura" => null,
                "origen"=>"localCopyCentral",
    
                "id" => null,
            ]);
            return Response::json(["msj"=>"Éxito","estado"=>true,"id"=>$id]);   
        } catch (\Exception $e) {

            $id_producto = inventario::where("codigo_barras", $producto["codigo_barras"])->first();
            $id = null;
            if ($id_producto) {
                $id = $id_producto->id;
            }
            return Response::json(["msj"=>"Err: ".$e->getMessage(),"estado"=>false, "id" => $id]);
        } 
    }
    public function saveChangeInvInSucurFromCentral(Request $req)
    {
        $inv = $req->inventarioModifiedCentralImport;
        $count = 0;
        $ids_true = [];
        $id_sucursal = null;
        foreach ($inv as $i => $e) {
            $obj = inventario::find($e["id_pro_sucursal_fixed"]);
            if ($obj) {
                $obj->id = $e["id_pro_sucursal"];
                $obj->codigo_barras = $e["codigo_barras"];
                $obj->codigo_proveedor = $e["codigo_proveedor"];
                $obj->id_proveedor = $e["id_proveedor"];
                $obj->id_categoria = $e["id_categoria"];
                $obj->id_marca = $e["id_marca"];
                $obj->unidad = $e["unidad"];
                $obj->id_deposito = $e["id_deposito"];
                $obj->descripcion = $e["descripcion"];
                $obj->iva = $e["iva"];
                $obj->porcentaje_ganancia = $e["porcentaje_ganancia"];
                $obj->precio_base = $e["precio_base"];
                $obj->precio = $e["precio"];
                /*  $obj->cantidad = $e["cantidad"];*/

                if ($obj->save()) {
                    $count++;
                    array_push($ids_true,$e["id"]);
                }

                $id_sucursal = $e["id_sucursal"];
            }
        }
        $changeEstatus = (new sendcentral)->changeEstatusProductoProceced($ids_true,$id_sucursal);
        return ["estado"=>true,"msj"=>"Éxito. $count productos modificados. "];
    }
    public function setCtxBulto(Request $req)
    {

        try {
            $id = $req->id;
            $bulto = $req->bulto;
            if ($id) {
                inventario::find($id)->update(["bulto"=>$bulto]);
                // code...
            }
            return Response::json(["msj"=>"Éxito. Bulto ".$bulto,"estado"=>true]);
            
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
            
        }
    }
    public function setStockMin(Request $req)
    {

        try {
            $id = $req->id;
            $min = $req->min;
            if ($id) {
                inventario::find($id)->update(["stockmin"=>$min]);
            }
            return Response::json(["msj"=>"Éxito. StockMin ".$min,"estado"=>true]);
            
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
            
        }
    }
    
    public function setPrecioAlterno(Request $req)
    {
        
        try {
            $id = $req->id;
            $type = $req->type;
            $precio = $req->precio;

            $arr = ["precio1"=>$precio];

            switch ($type) {
                case 'p1':
                    // code...
                        $arr = ["precio1"=>$precio];
                    break;
                case 'p2':
                        $arr = ["precio2"=>$precio];
                    break;
                case 'p3':
                        $arr = ["precio3"=>$precio];
                    break;                
            }
            if ($id) {
                inventario::find($id)->update($arr);
                // code...
            }

            return Response::json(["msj"=>"Éxito. Precio ".$precio,"estado"=>true]);
            
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
            
        }
    }
    public function getEstaInventario(Request $req)
    {
        $fechaQEstaInve = $req->fechaQEstaInve;

        $fecha1pedido = $req->fechaFromEstaInve;
        $fecha2pedido = $req->fechaToEstaInve;
        
        $orderByEstaInv = $req->orderByEstaInv;
        $orderByColumEstaInv = $req->orderByColumEstaInv;
        $categoriaEstaInve = $req->categoriaEstaInve;
        
        
        $tipoestadopedido = 1;

        
        return inventario::with([
            "proveedor",
            "categoria",
            "marca",
            "deposito",
        ])
        ->whereIn("id",function($q) use ($fecha1pedido,$fecha2pedido,$tipoestadopedido){
            $q->from("items_pedidos")
            ->whereIn("id_pedido",function($q) use ($fecha1pedido,$fecha2pedido,$tipoestadopedido){
                $q->from("pedidos")
                ->whereBetween("created_at",["$fecha1pedido 00:00:01","$fecha2pedido 23:59:59"])
                
                ->select("id");
            })
            ->select("id_producto");

        })
        ->when (!empty($categoriaEstaInve) , function ($query) use($categoriaEstaInve){
            return $query->where('id_categoria',$categoriaEstaInve);
        })
        ->where(function($q) use ($fechaQEstaInve)
        {
            $q->orWhere("descripcion","LIKE","%$fechaQEstaInve%")
            ->orWhere("codigo_proveedor","LIKE","%$fechaQEstaInve%");
            
        })
        ->selectRaw("*,@cantidadtotal := (SELECT sum(cantidad) FROM items_pedidos WHERE id_producto=inventarios.id AND created_at BETWEEN '$fecha1pedido 00:00:01' AND '$fecha2pedido 23:59:59') as cantidadtotal,(@cantidadtotal*inventarios.precio) as totalventa")
        ->orderByRaw(" $orderByColumEstaInv"." ".$orderByEstaInv)
        ->get();
        // ->map(function($q)use ($fecha1pedido,$fecha2pedido){
        //     $items = items_pedidos::whereBetween("created_at",["$fecha1pedido 00:00:01","$fecha2pedido 23:59:59"])
        //     ->where("id_producto",$q->id)->sum("cantidad");

        //     $q->cantidadtotal = $items
        //     // $q->items = $items->get();

        //     return $q;
        // })->sortBy("cantidadtotal");



    }
    public function hacer_pedido($id,$id_pedido,$cantidad,$type,$typeafter=null,$usuario=null)
    {   
        try {
            if ($cantidad<0) {
                exit;
            }
            $cantidad = $cantidad==""?1:$cantidad;
            $old_ct = 0;
            if ($type=="ins") {
                if ($id_pedido=="nuevo") {
                    //Crea Pedido

                    $pro = inventario::find($id);
                    $loquehabra = $pro->cantidad - $cantidad;

                    if ($loquehabra<0) {
                        throw new \Exception("No hay disponible la cantidad solicitada", 1);
                        
                    }

                    $new_pedido = new pedidos;
                    $new_pedido->estado = 0;
                    $new_pedido->id_cliente = 1;
                    $new_pedido->id_vendedor = $usuario;
                    $new_pedido->save();
                    $id_pedido = $new_pedido->id;
                }


                $producto = inventario::select(["cantidad","precio"])->find($id);
                $precio = $producto->precio;
                
                $setcantidad = $cantidad;
                $setprecio = $precio;
                
                $checkIfExits = items_pedidos::select(["cantidad"])
                ->where("id_producto",$id)
                ->where("id_pedido",$id_pedido)
                ->first();

                (new PedidosController)->checkPedidoAuth($id_pedido);
                (new PedidosController)->checkPedidoPago($id_pedido);

                if ($checkIfExits) {
                    $old_ct = $checkIfExits["cantidad"];

                    if ($cantidad=="1000000") { //codigo para sumar de uno en uno
                        $setcantidad = 1 + $old_ct; //Sumar cantidad a lo que ya existe en carrito
                    }else{
                        $setcantidad = $cantidad;
                    }

                    $setprecio = $setcantidad*$precio;
                }else{
                    if ($cantidad=="1000000") { //codigo para sumar de uno en uno
                        $setcantidad = 1; //Sumar cantidad a lo que ya existe en carrito
                    }

                    $setprecio = $setcantidad*$precio;
                }

                $ctquehabia = $producto->cantidad + $old_ct;
                
                $ctSeter = ($ctquehabia - $setcantidad);
                
                $this->descontarInventario($id,$ctSeter, $ctquehabia, $id_pedido, "inItPd");
                
                $this->checkFalla($id,$ctSeter);
                items_pedidos::updateOrCreate([
                    "id_producto"=>$id,
                    "id_pedido"=>$id_pedido,
                ],[
                    "id_producto" => $id,
                    "id_pedido" => $id_pedido,
                    "cantidad" => $setcantidad,
                    "monto" => $setprecio,
                ]);

                return ["msj"=>"Agregado al pedido #".$id_pedido." || Cant. ".$cantidad,"estado"=>"ok","num_pedido"=>$id_pedido,"type"=>$typeafter];


            }else if($type=="upd"){
                $checkIfExits = items_pedidos::select(["id_producto","cantidad"])->find($id);
                (new PedidosController)->checkPedidoAuth($id,"item");
                (new PedidosController)->checkPedidoPago($id,"item");
                

                $producto = inventario::select(["precio","cantidad"])->find($checkIfExits->id_producto);
                $precio = $producto->precio;

                $old_ct = $checkIfExits->cantidad;

                $setprecio = $cantidad*$precio;

                $ctSeter = (($producto->cantidad + $old_ct) - $cantidad);
                
                
                $this->descontarInventario($checkIfExits->id_producto,$ctSeter, ($producto->cantidad), $id_pedido, "updItemPedido");
                $this->checkFalla($checkIfExits->id_producto,$ctSeter);
                
                items_pedidos::updateOrCreate(["id"=>$id],[
                    "cantidad" => $cantidad,
                    "monto" => $setprecio
                ]);
                return ["msj"=>"Actualizado Prod #".$checkIfExits->id_producto." || Cant. ".$cantidad,"estado"=>"ok"];


            }else if($type=="del"){
                (new PedidosController)->checkPedidoAuth($id,"item");
                (new PedidosController)->checkPedidoPago($id,"item");
                
                
                    $item = items_pedidos::find($id);
                    $old_ct = $item->cantidad;
                    $id_producto = $item->id_producto;
                    $pedido_id = $item->id_pedido;

                
                    $producto = inventario::select(["cantidad"])->find($id_producto);
                    
                if($item->delete()){
                    
                    $ctSeter = $producto->cantidad + ($old_ct);
                    
                    
                    $this->descontarInventario($id_producto,$ctSeter, $producto->cantidad, $pedido_id, "delItemPedido");

                    $this->checkFalla($id_producto,$ctSeter);
                    return ["msj"=>"Item Eliminado","estado"=>true];

                }
            }
            
        } catch (\Exception $e) {

            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
            
        }

    }
    public function descontarInventario($id_producto,$cantidad, $ct1,$id_pedido,$origen)
    {
        $inv = inventario::find($id_producto);

        if ($cantidad<0) {
            throw new \Exception("No hay disponible la cantidad solicitada", 1);
        }
        $inv->cantidad = $cantidad;
        if($inv->save()){
            (new MovimientosInventariounitarioController)->setNewCtMov([
                "id_producto" => $id_producto,
                "cantidadafter" => $cantidad,

                "ct1" => $ct1,
                "id_pedido" => $id_pedido,
                "origen" => $origen." #".$id_pedido,
            ]);
            return true;
        };

        
    }
    public function getProductosSerial(Request $req)
    {
        try {
            $ids = $req->ids_productos;
            $count = $req->count;
            $uniques = array_unique($ids);

            if (count($uniques)!==count($ids)) {
                throw new \Exception("¡Productos duplicados!", 1);
            }
            if ($count!=count($uniques)) {
                throw new \Exception("¡Faltan/Sobran productos! ".$count." | ".count($uniques), 1);
            } 
            $where = inventario::whereIn("id",$ids)->get();
            if ($where->count()!=count($uniques)) {
                throw new \Exception("¡Algunos productos no están registrados!", 1);
            }
            return Response::json(["msj"=>$where,"estado"=>true]);   
            
        } catch (\Exception $e) {

            return Response::json(["msj"=>"Error. ".$e->getMessage(),"estado"=>false]);
            
        }
    }
    public function checkPedidosCentral(Request $req)
    {   
        $pedido = $req->pedido;
        $pathcentral = $req->pathcentral;
        
        try {
            //Check Items
            foreach ($pedido["items"] as $i => $item) {
                if (!isset($item["aprobado"])) {
                    throw new \Exception("¡Falta verificar productos!", 1);
                }

                if (isset($item["ct_real"])) {
                    if ($item["ct_real"]<=0 OR $item["ct_real"]==$item["cantidad"]) {
                        throw new \Exception("¡Error con cantidad verificada ".$item["ct_real"]."!", 1);
                    }
                }
            }
            ///Check items mal vinculados
            foreach ($pedido["items"] as $i => $item) {
                $checkbarras = inventario::where("codigo_barras",$item["producto"]["codigo_barras"])->first();
                $id_viculacionFromCentral = $item["producto"]["id"];
                if ($checkbarras) {
                    if (($checkbarras->id_vinculacion!=$id_viculacionFromCentral)) {
                        return Response::json(["msj"=>"Error: producto malvinculado. BarrasCentral ".$item["producto"]["codigo_barras"],"estado"=>false]);
                    }
                }
            }   
            //Check Proveedor
            
                $id = $pedido["id"];
                
                $factInpnumfact = $pedido["id"];
                $factInpdescripcion = "De ".$pedido["origen"]["codigo"]." ".$pedido["created_at"];
                $factInpmonto = $pedido["venta"];
                $factInpfechavencimiento = $pedido["created_at"];
                $factInpestatus = 1;


                $checkIfExitsFact = factura::find($id);
                if (!$checkIfExitsFact) {
                    $fact = new factura;
                    $fact->id = $id;
                    $fact->id_proveedor = 1;
                    $fact->numfact = $factInpnumfact;
                    $fact->descripcion = $factInpdescripcion;
                    $fact->monto = $factInpmonto;
                    $fact->fechavencimiento = $factInpfechavencimiento;
                    $fact->estatus = $factInpestatus;

                    if ($fact->save()) {
                        $num = 0;
                        foreach ($pedido["items"] as $i => $item) {
                            $id_pro = $item["producto"]["id"];
                            $ctNew = $item["cantidad"];

                            $minivinculacioncheck = inventario::where("id_vinculacion",$id_pro)->first();
                            if (!$minivinculacioncheck) {
                                $minivinculacionset = inventario::where("codigo_barras",$item["producto"]["codigo_barras"])->first();
                                if ($minivinculacionset) {
                                    $minivinculacionset->id_vinculacion = $id_pro;
                                    $minivinculacionset->save();
                                }
                            }

                            $checkoldCt = inventario::where("id_vinculacion",$id_pro)->first();
                            $match_ct = 0;
                            if ($checkoldCt) {
                                $match_ct = $checkoldCt->cantidad;
                            }

                            $insertOrUpdateInv = $this->guardarProducto([
                                "id_factura" => $id,
                                "id" => "id_vinculacion",
                                "id_vinculacion" => $id_pro,
                                "cantidad" => $match_ct + $ctNew,
                                "codigo_barras" => $item["producto"]["codigo_barras"],
                                "codigo_proveedor" => $item["producto"]["codigo_proveedor"],
                                "unidad" => $item["producto"]["unidad"],
                                "id_categoria" => $item["producto"]["id_categoria"],
                                "descripcion" => $item["producto"]["descripcion"],
                                "precio_base" => $item["producto"]["precio_base"],
                                "precio" => $item["producto"]["precio"],
                                "iva" => $item["producto"]["iva"],
                                "id_proveedor" => $item["producto"]["id_proveedor"],
                                "id_marca" => $item["producto"]["id_marca"],
                                "id_deposito" => /*$req->inpInvid_deposito*/"",
                                "porcentaje_ganancia" => 0,
                                "origen"=>"central",

                                "precio1" => $item["producto"]["precio1"],
                                "precio2" => $item["producto"]["precio2"],
                                "precio3" => $item["producto"]["precio3"],
                                "stockmin" => $item["producto"]["stockmin"],
                                "stockmax" => $item["producto"]["stockmax"],
                            ]);


                            if ($insertOrUpdateInv) 
                            {
                                items_factura::updateOrCreate([
                                    "id_factura" => $id,
                                    "id_producto" => $insertOrUpdateInv,
                                ],[
                                    "cantidad" => $ctNew,
                                    "tipo" => "Actualización",
                                ]);
                                $num++;
                            }
                        }
                        
                        (new sendCentral)->setFacturasCentral();
                        (new sendCentral)->changeExportStatus($pathcentral,$id);
                        return Response::json(["msj"=>"¡Éxito ".$num." productos procesados!","estado"=>true]);

                    }
                }else{
                    throw new \Exception("¡Factura ya existe!", 1);
                }
            
            

            
        } catch (\Exception $e) {
            
            return Response::json(["msj"=>"Error. ".$e->getMessage(),"estado"=>false]);
        }
    }
    public function reporteFalla(Request $req)
    {
        $id_proveedor = $req->id;

        $sucursal = sucursal::all()->first();
        $proveedor = proveedores::find($id_proveedor);

        if ($proveedor&&$id_proveedor) {
            $fallas = fallas::With("producto")->whereIn("id_producto",function($q) use ($id_proveedor)
            {
                $q->from("inventarios")->where("id_proveedor",$id_proveedor)->select("id");
            })->get();

            return view("reportes.fallas",[
                "fallas"=>$fallas, 
                "sucursal"=>$sucursal,
                "proveedor"=>$proveedor,
            ]);
        }


    }
    public function getUniqueProductoById(Request $req)
    {
        return inventario::find($req->id);
    }
    public function getInventarioFun($req)
    {
        $exacto = false;

        if (isset($req["exacto"])) {
            if ($req["exacto"]=="si") {
                $exacto = "si";
            }
            if ($req["exacto"]=="id_only") {
                $exacto = "id_only";
            }
        }
        $mon = (new PedidosController)->get_moneda();
        $cop = $mon["cop"];
        $bs = $mon["bs"];


        $data = [];

        $q = $req["qProductosMain"];
        $num = $req["num"];
        $itemCero = $req["itemCero"];

        $orderColumn = $req["orderColumn"];
        $orderBy = $req["orderBy"];

        


        if ($req["busquedaAvanazadaInv"]) {
            $busqAvanzInputs = $req["busqAvanzInputs"];
            $data = inventario::with([
                    "proveedor",
                    "categoria",
                    "marca",
                    "deposito",
                    "lotes"=>function($q){
                        $q->orderBy("vence","asc");
                    },
                ])
                ->where(function($e) use($busqAvanzInputs){
    
                    
                    if ($busqAvanzInputs["codigo_barras"]!="") {
                        $e->where("codigo_barras","LIKE",$busqAvanzInputs["codigo_barras"]."%");
                    }
                    if ($busqAvanzInputs["codigo_proveedor"]!="") {
                        $e->where("codigo_proveedor","LIKE",$busqAvanzInputs["codigo_proveedor"]."%");
                    }
                    if ($busqAvanzInputs["id_proveedor"]!="") {
                        $e->where("id_proveedor","LIKE",$busqAvanzInputs["id_proveedor"]);
                    }
                    if ($busqAvanzInputs["id_categoria"]!="") {
                        $e->where("id_categoria","LIKE",$busqAvanzInputs["id_categoria"]);
                    }
                    if ($busqAvanzInputs["unidad"]!="") {
                        $e->where("unidad","LIKE",$busqAvanzInputs["unidad"]."%");
                    }
                    if ($busqAvanzInputs["descripcion"]!="") {
                        $e->where("descripcion","LIKE",$busqAvanzInputs["descripcion"]."%");
                    }
                    if ($busqAvanzInputs["iva"]!="") {
                        $e->where("iva","LIKE",$busqAvanzInputs["iva"]."%");
                    }
                    if ($busqAvanzInputs["precio_base"]!="") {
                        $e->where("precio_base","LIKE",$busqAvanzInputs["precio_base"]."%");
                    }
                    if ($busqAvanzInputs["precio"]!="") {
                        $e->where("precio","LIKE",$busqAvanzInputs["precio"]."%");
                    }
                    if ($busqAvanzInputs["cantidad"]!="") {
                        $e->where("cantidad","LIKE",$busqAvanzInputs["cantidad"]."%");
                    }
    
                })
                ->limit($num)
                ->orderBy($orderColumn,$orderBy)
                ->get();
                

        }else{
            if ($q=="") {
                $data = inventario::with([
                    "proveedor",
                    "categoria",
                    "marca",
                    "deposito",
                    "lotes"=>function($q){
                        $q->orderBy("vence","asc");
                    },
                ])
                ->selectRaw("*,@bs := (inventarios.precio*$bs) as bs, @cop := (inventarios.precio*$cop) as cop")
                ->where(function($e) use($itemCero){
                    if (!$itemCero) {
                        $e->where("cantidad",">",0);
                    }
    
                })
                ->limit($num)
                ->orderBy($orderColumn,$orderBy)
                ->get();
            }else{
                $data = inventario::with([
                    "proveedor",
                    "categoria",
                    "marca",
                    "deposito",
                    "lotes"=>function($q){
                        $q->orderBy("vence","asc");
                    },
                ])
                ->selectRaw("*,@bs := (inventarios.precio*$bs) as bs, @cop := (inventarios.precio*$cop) as cop")

                ->when(!$itemCero, function($e) use($itemCero){
                    $e->where("cantidad",">",0);
    
                })
                ->where(function($e) use($itemCero,$q,$exacto){
    
                    if ($exacto=="si") {
                        $e->orWhere("codigo_barras","LIKE","$q")
                        ->orWhere("codigo_proveedor","LIKE","$q");
                    }elseif($exacto=="id_only"){
    
                        $e->where("id","$q");
                    }else{
                        $e->orWhere("descripcion","LIKE","%$q%")
                        ->orWhere("codigo_proveedor","LIKE","%$q%")
                        ->orWhere("codigo_barras","LIKE","%$q%");
    
                    }
    
                })
                ->limit($num)
                ->orderBy($orderColumn,$orderBy)
                ->get();
            }

        }

        return $data;
    }
    public function index(Request $req)
    {
        $req = [
            "exacto"=> $req->exacto,
            "qProductosMain"=> $req->qProductosMain,
            "num"=> $req->num,
            "itemCero"=> $req->itemCero,
            "orderColumn"=> $req->orderColumn,
            "orderBy"=> $req->orderBy,
            "busquedaAvanazadaInv"=> $req->busquedaAvanazadaInv,
            "busqAvanzInputs"=> $req->busqAvanzInputs,
        ];

        return $this->getInventarioFun($req);
        
    }
    public function changeIdVinculacionCentral(Request $req)
    {
        try {

            inventario::where("id_vinculacion",$req->idincentral)->update(["id_vinculacion"=>null]);
            $inv = inventario::find($req->idinsucursal);
            $inv->id_vinculacion = $req->idincentral;
            if ($inv->save()) {
                
                $pedido = $req->pedioscentral;
    
                foreach ($pedido["items"] as $keyitem => $item) {
                    ///id central ID VINCULACION
                    $pedido["items"][$keyitem]["match"] = inventario::where("id_vinculacion",$item["producto"]["id"])->get()->first();
                }
                return Response::json([
                    "msj"=>"Éxito", 
                    "estado"=>true,
                    "pedido"=>$pedido,
                ]);
                
            }
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error ".$e->getMessage(), "estado"=>false]);
        }
    }
    public function setCarrito(Request $req)
    {
        $type = $req->type;
        $cantidad = $req->cantidad;
        $numero_factura = $req->numero_factura;
        
        if (isset($numero_factura)) {
            $id = $numero_factura;

            $id_producto = $req->id;
            $cantidad = $cantidad==""?1:$cantidad;

            $usuario = session()->has("id_usuario")? session("id_usuario"): $req->usuario;
            $iscentral = session("iscentral");
            

            if (!$usuario) {
                return Response::json(["msj"=>"Debe iniciar Sesión", "estado"=>false,"num_pedido"=>0,"type"=>""]);
            }
            $today = (new PedidosController)->today();
            $fechaultimocierre = (new CierresController)->getLastCierre();
            if ($fechaultimocierre && $iscentral==0) {
                if($fechaultimocierre->fecha == $today){
                    return Response::json(["msj"=>"¡Imposible hacer pedidos! Cierre procesado", "estado"=>false,"num_pedido"=>0,"type"=>""]);
                }
            }
            
            $id_return = $id=="nuevo"?"nuevo":$id;
            
            return  $this->hacer_pedido($id_producto,$id_return,$cantidad,"ins",$type,$usuario);
        }
        
    }
    public function setMovimientoNotCliente($id_pro,$des,$ct,$precio,$cat)
    {   
        $mov = new movimientos;
        $mov->id_usuario = session("id_usuario");
            
            if ($mov->save()) {
               $items_mov = new items_movimiento;
               $items_mov->descripcion = $des;
               $items_mov->id_producto = $id_pro;

               $items_mov->cantidad = $ct;
               $items_mov->precio = $precio;
               $items_mov->tipo = 2;
               $items_mov->categoria = $cat;
               $items_mov->id_movimiento = $mov->id;
               $items_mov->save();
            }
    }
    public function delProductoFun($id,$origen="local")
    {
        try {
            $i = inventario::find($id);
            $id_usuario = session("id_usuario");
            (new MovimientosInventarioController)->newMovimientosInventario([
                "antes" => $i,
                "despues" => null,
                "id_usuario" => $id_usuario,
                "id_producto" => $id,
                "origen" => $origen,
            ]);

            if ($i->delete()) {
                return true;   
            }
        } catch (\Exception $e) {
            throw new \Exception("Error al eliminar. ".$e->getMessage(), 1);
            
        }
    }
    public function delProducto(Request $req)
    {
        $id = $req->id;
        try {
            $this->delProductoFun($id);
            return Response::json(["msj"=>"Éxito al eliminar","estado"=>true]);   
        } catch (\Exception $e) {
            return Response::json(["msj"=>$e->getMessage(),"estado"=>false]);
            
        }  
    }
    public function guardarNuevoProductoLote(Request $req)
    {
      try {
          foreach ($req->lotes as $key => $ee) {
            if (isset($ee["type"])) {
                if ($ee["type"]==="update"||$ee["type"]==="new") {

                    $this->guardarProducto([
                            "id_factura" => $req->id_factura,
                            "cantidad" => $ee["cantidad"],
                            "codigo_barras" => $ee["codigo_barras"],
                            "codigo_proveedor" => $ee["codigo_proveedor"],
                            "descripcion" => $ee["descripcion"],
                            "id" => $ee["id"],
                            "id_categoria" => $ee["id_categoria"],
                            "id_marca" => $ee["id_marca"],
                            "id_proveedor" => $ee["id_proveedor"],
                            "iva" => $ee["iva"],
                            "precio" => $ee["precio"],
                            "precio_base" => $ee["precio_base"],
                            "unidad" => $ee["unidad"],
                            "push" => isset($ee["push"])?$ee["push"]:1,
                            "id_deposito" => "",
                            "porcentaje_ganancia" => 0,
                            
                            "origen"=>"local",
                    ]);
                }else if ($ee["type"]==="delete") {
                    $this->delProductoFun($ee["id"]);
                }
            }   
          }
                return Response::json(["msj"=>"Éxito","estado"=>true]);   
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Err: ".$e,"estado"=>false]);
        }  
    }
    public function getSyncProductosCentralSucursal(Request $req)
    {
        $datasucursal = $req->obj; 
        foreach ($datasucursal as $k => $v) {
            $datacentral =  inventario::where("codigo_barras", "LIKE", $v["codigo_barras"]."%")->get()->first();
            if ($datacentral) {
                $datasucursal[$k]["type"] = "update";
                $datasucursal[$k]["estatus"] = 1;
                $datasucursal[$k]["codigo_barras"] = $datacentral["codigo_barras"];
                $datasucursal[$k]["codigo_proveedor"] = $datacentral["codigo_proveedor"];
                $datasucursal[$k]["id_proveedor"] = $datacentral["id_proveedor"];
                $datasucursal[$k]["id_categoria"] = $datacentral["id_categoria"];
                $datasucursal[$k]["id_marca"] = $datacentral["id_marca"];
                $datasucursal[$k]["unidad"] = $datacentral["unidad"];
                $datasucursal[$k]["id_deposito"] = $datacentral["id_deposito"];
                $datasucursal[$k]["descripcion"] = $datacentral["descripcion"];
                $datasucursal[$k]["iva"] = $datacentral["iva"];
                $datasucursal[$k]["porcentaje_ganancia"] = $datacentral["porcentaje_ganancia"];
                $datasucursal[$k]["precio_base"] = $datacentral["precio_base"];
                $datasucursal[$k]["precio"] = $datacentral["precio"];
                $datasucursal[$k]["precio1"] = $datacentral["precio1"];
                $datasucursal[$k]["precio2"] = $datacentral["precio2"];
                $datasucursal[$k]["precio3"] = $datacentral["precio3"];
                $datasucursal[$k]["bulto"] = $datacentral["bulto"];
                $datasucursal[$k]["stockmin"] = $datacentral["stockmin"];
                $datasucursal[$k]["stockmax"] = $datacentral["stockmax"];
                $datasucursal[$k]["id_vinculacion"] = $datacentral["id"];;
            }
        }
        return $datasucursal;
        
    }
    public function guardarNuevoProducto(Request $req)
    {   
        try {
            $this->guardarProducto([
                "id_factura" => $req->id_factura,
                "cantidad" => $req->inpInvcantidad,
                "id" => $req->id,
                "codigo_barras" => $req->inpInvbarras,
                "codigo_proveedor" => $req->inpInvalterno,
                "unidad" => $req->inpInvunidad,
                "id_categoria" => $req->inpInvcategoria,
                "descripcion" => $req->inpInvdescripcion,
                "precio_base" => $req->inpInvbase,
                "precio" => $req->inpInvventa,
                "iva" => $req->inpInviva,
                "id_proveedor" => $req->inpInvid_proveedor,
                "id_marca" => $req->inpInvid_marca,
                "id_deposito" => $req->inpInvid_deposito,
                "porcentaje_ganancia" => $req->inpInvporcentaje_ganancia,
                
                "origen"=>"local",
            ]);
                return Response::json(["msj"=>"Éxito","estado"=>true]);   
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
        }



         
    }
    function saveReplaceProducto(Request $req) {
        try {
            $replaceProducto = $req->replaceProducto;
            $este = $replaceProducto["este"];
            $poreste = $replaceProducto["poreste"];
    
            $productoeste = inventario::find($este);
            $ct = $productoeste->cantidad;
            $id_vinculacion = $productoeste->id_vinculacion;
    
            $productoporeste = inventario::find($poreste);
            $productoporeste->cantidad = $productoporeste->cantidad + ($ct);
            $productoeste->cantidad = 0;
            if ($id_vinculacion) {
                $productoeste->id_vinculacion = NULL;
                $productoporeste->id_vinculacion = $id_vinculacion;
            }
            
            $productoeste->save();
            $productoporeste->save();
    
            items_pedidos::where("id_producto",$este)->update(["id_producto" => $poreste]);

            return Response::json(["estado" => true, "msj" => "Éxito"]);
        } catch (\Exception $e) {
            return Response::json(["estado" => false, "msj" => "Error: ".$e->getMessage()]);
        }
    }
    public function guardarProducto($arrproducto){
        try {

            /* if (!session("iscentral")) {
                throw new \Exception("No tiene permisos para gestionar Inventario", 1);
                
            } */
            $id_factura = $arrproducto["id_factura"];
            $req_inpInvcantidad = $arrproducto["cantidad"];
            $req_inpInvbarras = $arrproducto["codigo_barras"];
            $req_inpInvalterno = $arrproducto["codigo_proveedor"];
            $req_inpInvunidad = $arrproducto["unidad"];
            $req_inpInvcategoria = $arrproducto["id_categoria"];
            $req_inpInvdescripcion = $arrproducto["descripcion"];
            $req_inpInvbase = $arrproducto["precio_base"];
            $req_inpInvventa = $arrproducto["precio"];
            $req_inpInviva = $arrproducto["iva"];
            $req_inpInvid_proveedor = $arrproducto["id_proveedor"];
            $req_inpInvid_marca = $arrproducto["id_marca"];
            $req_inpInvid_deposito = $arrproducto["id_deposito"];
            $req_inpInvporcentaje_ganancia = $arrproducto["porcentaje_ganancia"];
            
            $push = isset($arrproducto["push"])?$arrproducto["push"]:null; 
            $precio1 = isset($arrproducto["precio1"])?$arrproducto["precio1"]:null; 
            $precio2 = isset($arrproducto["precio2"])?$arrproducto["precio2"]:null; 
            $precio3 = isset($arrproducto["precio3"])?$arrproducto["precio3"]:null; 
            $stockmin = isset($arrproducto["stockmin"])?$arrproducto["stockmin"]:null; 
            $stockmax = isset($arrproducto["stockmax"])?$arrproducto["stockmax"]:null; 
            $id_vinculacion = isset($arrproducto["id_vinculacion"])?$arrproducto["id_vinculacion"]:null; 
            $req_id = $arrproducto["id"];
            
            $ctInsert = $req_inpInvcantidad;
            $id_usuario = session("id_usuario");

            
            $beforecantidad = 0;
            $ctNew = 0;
            $tipo = "";
            
            $before = null;
            if (!$req_id) {
                $ctNew = $ctInsert;
                $tipo = "Nuevo";
            }else{
                $before = $req_id==="id_vinculacion"? inventario::where("id_vinculacion", $id_vinculacion)->first(): inventario::find($req_id);
                if ($before) {
                    $beforecantidad = $before->cantidad;
                    $ctNew = $ctInsert - $beforecantidad;
                    $tipo = "Actualización";
                }else{
                    $tipo = "Nuevo";
                }
            }

            $insertOrUpdateInv = inventario::updateOrCreate(
                ($req_id==="id_vinculacion")? ["id_vinculacion" => $id_vinculacion]: ["id" => $req_id],
                
                ["codigo_barras" => $req_inpInvbarras,
                "cantidad" => $ctInsert,
                "codigo_proveedor" => $req_inpInvalterno,
                "unidad" => $req_inpInvunidad,
                "id_categoria" => $req_inpInvcategoria,
                "descripcion" => $req_inpInvdescripcion,
                "precio_base" => $req_inpInvbase,
                "precio" => $req_inpInvventa,
                "iva" => $req_inpInviva,
                "id_proveedor" => $req_inpInvid_proveedor,
                "id_marca" => $req_inpInvid_marca,
                "id_deposito" => $req_inpInvid_deposito,
                "porcentaje_ganancia" => $req_inpInvporcentaje_ganancia,

                "precio1" => $precio1,
                "precio2" => $precio2,
                "precio3" => $precio3,
                "stockmin" => $stockmin,
                "stockmax" => $stockmax,
                "id_vinculacion" => $id_vinculacion,
                "push" => $push,
            ]);

            $this->checkFalla($insertOrUpdateInv->id,$ctInsert);
            $this->insertItemFact($id_factura,$insertOrUpdateInv,$ctInsert,$beforecantidad,$ctNew,$tipo);
            if ($insertOrUpdateInv) {
                
                
                //Registrar moviento de producto
                $origen = $arrproducto["origen"];

                (new MovimientosInventarioController)->newMovimientosInventario([
                    "antes" => $before,
                    "despues" => json_encode($arrproducto),
                    "id_usuario" => $id_usuario,
                    "id_producto" => $insertOrUpdateInv->id,
                    "origen" => $origen,
                ]);
                

                (new MovimientosInventariounitarioController)->setNewCtMov([
                    "id_producto" => $insertOrUpdateInv->id,
                    "cantidadafter" => $arrproducto["cantidad"],
                    "ct1" => isset($before["cantidad"])?$before["cantidad"]:0,
                    "origen" => $origen,
                ]);
                

                return $insertOrUpdateInv->id;   
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                throw new \Exception("Código Duplicado. ".$req_inpInvbarras, 1);
            }else{
                throw new \Exception("Error: ".$e->getMessage(), 1);

            }


            
        }
    }
    public function insertItemFact($id_factura,$insertOrUpdateInv,$ctInsert,$beforecantidad,$ctNew,$tipo)
    {
        $find_factura = factura::find($id_factura);

        if($insertOrUpdateInv && $find_factura){

            $id_pro = $insertOrUpdateInv->id;
            $check_fact = items_factura::where("id_factura",$id_factura)->where("id_producto",$id_pro)->first();

            if ($check_fact) {
                $ctNew = $ctInsert - ($beforecantidad - $check_fact->cantidad);
            }


            if ($ctNew==0) {
                items_factura::where("id_factura",$id_factura)->where("id_producto",$id_pro)->delete();
            }else{
                items_factura::updateOrCreate([
                    "id_factura" => $id_factura,
                    "id_producto" => $id_pro,
                ],[
                    "cantidad" => $ctNew,
                    "tipo" => $tipo,

                ]);

            }

        }
    }

    public function getFallas(Request $req)
    {


        $qFallas = $req->qFallas;
        $orderCatFallas = $req->orderCatFallas;
        $orderSubCatFallas = $req->orderSubCatFallas;
        $ascdescFallas = $req->ascdescFallas;
       
        // $query_frecuencia = items_pedidos::with("producto")->select(['id_producto'])
        //     ->selectRaw('COUNT(id_producto) as en_pedidos, SUM(cantidad) as cantidad')
        //     ->groupBy(['id_producto']);

        // if ($orderSubCatFallas=="todos") {
        //     // $query_frecuencia->having('cantidad', '>', )
        // }else if ($orderSubCatFallas=="alta") {
        //     $query_frecuencia->having('cantidad', '>', )
        // }else if ($orderSubCatFallas=="media") {
        //     $query_frecuencia->having('cantidad', '>', )
        // }else if ($orderSubCatFallas=="baja") {
        //     $query_frecuencia->having('cantidad', '>', )
        // }

        // return $query_frecuencia->get();
        if ($orderCatFallas=="categoria") {
            
            return fallas::with(["producto"=>function($q){
                $q->with(["proveedor","categoria"]);
            }])->get()->groupBy("producto.categoria.descripcion");

        }else if ($orderCatFallas=="proveedor") {
            return fallas::with(["producto"=>function($q){
                $q->with(["proveedor","categoria"]);
            }])->get()->groupBy("producto.proveedor.descripcion");

        }
    }
    public function setFalla(Request $req)
    {   
        try {
            fallas::updateOrCreate(["id_producto"=>$req->id_producto],["id_producto"=>$req->id_producto]);
            
            return Response::json(["msj"=>"Falla enviada con Éxito","estado"=>true]);   
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
            
        } 
    }
    public function delFalla(Request $req)
    {   
        try {
            fallas::find($req->id)->delete();
            
            return Response::json(["msj"=>"Falla Eliminada","estado"=>true]);   
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
            
        } 
    }
    public function checkFalla($id,$ct)
    {   
        if ($id) {
            $stockmin = 0;
            $stockminquery = inventario::find($id)->first(["id","stockmin"]);
            if ($stockminquery) {
                $stockmin = $stockminquery->stockmin?$stockminquery->stockmin:0; 
            }
            if ($ct>=$stockmin) {

                $f = fallas::where("id_producto",$id)->first();
                if ($f) {
                    $f->delete();
                }
            }else if($ct<$stockmin){
    
                fallas::updateOrCreate(["id_producto"=>$id],["id_producto"=>$id]);
            }
        }
    }

    public function reporteInventario(Request $req)
    {
        $costo = 0;
        $venta = 0;

        $descripcion = $req->descripcion;
        $precio_base = $req->precio_base;
        $precio = $req->precio;
        $cantidad = $req->cantidad;
        $proveedor = $req->proveedor;
        $categoria = $req->categoria;
        $marca = $req->marca;

        $codigo_proveedor = $req->codigo_proveedor;
        $codigo_barras = $req->codigo_barras;

        $data= inventario::with("lotes","proveedor","categoria")->where(function($q) use ($codigo_proveedor,$codigo_barras,$descripcion,$precio_base,$precio,$cantidad,$proveedor,$categoria,$marca)
        {

            if($descripcion){$q->where("descripcion","LIKE",$descripcion."%");}
            if($codigo_proveedor){$q->where("codigo_proveedor","LIKE",$codigo_proveedor."%");}
            if($codigo_barras){$q->where("codigo_barras","LIKE",$codigo_barras."%");}

            if($precio_base){$q->where("precio_base",$precio_base);}
            if($precio){$q->where("precio",$precio);}
            if($cantidad){$q->where("cantidad",$cantidad);}
            if($proveedor){$q->where("id_proveedor",$proveedor);}
            if($categoria){$q->where("id_categoria",$categoria);}
            if($marca){$q->where("id_marca",$marca);}
        })
        ->orderBy("descripcion","asc")
        ->get()
        ->map(function($q) use (&$costo,&$venta)
        {
            if (count($q->lotes)) {
                $q->cantidad = $q->lotes->sum("cantidad"); 
            }
            $c = $q->cantidad*$q->precio_base;
            $v = $q->cantidad*$q->precio;

            $q->t_costo = number_format($c,"2"); 
            $q->t_venta = number_format($v,"2");
            
            $costo += $c;
            $venta += $v;

            return  $q;
        });
        $sucursal = sucursal::all()->first();
        $proveedores = proveedores::all();
        $categorias = categorias::all();
        
        
        return view("reportes.inventario",[
            "data"=>$data,
            "sucursal"=>$sucursal,
            "categorias"=>$categorias,
            "proveedores"=>$proveedores,

            "descripcion"=>$descripcion,
            "precio_base"=>$precio_base,
            "precio"=>$precio,
            "cantidad"=>$cantidad,
            "proveedor"=>$proveedor,
            "categoria"=>$categoria,
            "marca"=>$marca,

            "count" => count($data),
            "costo" => number_format($costo,"2"),
            "venta" => number_format($venta,"2"),

            "view_codigo_proveedor" => $req->view_codigo_proveedor==="off"?false:true,
            "view_codigo_barras" => $req->view_codigo_barras==="off"?false:true,
            "view_descripcion" => $req->view_descripcion==="off"?false:true,
            "view_proveedor" => $req->view_proveedor==="off"?false:true,
            "view_categoria" => $req->view_categoria==="off"?false:true,
            "view_id_marca" => $req->view_id_marca==="off"?false:true,
            "view_cantidad" => $req->view_cantidad==="off"?false:true,
            "view_precio_base" => $req->view_precio_base==="off"?false:true,
            "view_t_costo" => $req->view_t_costo==="off"?false:true,
            "view_precio" => $req->view_precio==="off"?false:true,
            "view_t_venta" => $req->view_t_venta==="off"?false:true,
           

        ]);
    }
            
}
