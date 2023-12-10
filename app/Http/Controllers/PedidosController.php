<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\cierres;
use App\Models\pedidos;
use App\Models\moneda;
use App\Models\inventario;
use App\Models\items_pedidos;
use App\Models\pago_pedidos;
use App\Models\clientes;
use App\Models\usuarios;


use App\Models\movimientos_caja;
use App\Models\sucursal;
use App\Models\movimientos;
use App\Models\items_movimiento;
use App\Models\pagos_referencias;
use App\Models\movimientosInventario;



use Illuminate\Support\Facades\Cache;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Mail\enviarCierre;
use App\Mail\enviarCuentaspagar;

use Response;

class PedidosController extends Controller
{   

    
    
    protected  $letras = [
                1=>"1",
                2=>"2",
                3=>"3",
                4=>"4",
                5=>"5",
                6=>"6",
                7=>"7",
                8=>"8",
                9=>"9",
                0=>"0",
            ];
    public function sends()
    {
        return (new sendCentral)->sends();
    }
    public function changepedidouser(Request $req)
    {
        
        $id_pedido = $req->id_pedido; 
        $id_usuario = $req->id_usuario;


        $isPermiso = (new TareaslocalController)->checkIsResolveTarea([
            "id_pedido" => $id_pedido,
            "tipo" => "transferirPedido",
        ]);
        
        if ((new UsuariosController)->isAdmin()) {

            
        }elseif($isPermiso["permiso"]){
            
        }else{

            $nuevatarea = (new TareaslocalController)->createTareaLocal([
                "id_pedido" =>  $id_pedido,
                "valoraprobado" => 0,
                "tipo" => "transferirPedido",
                "descripcion" => "Solicitud de Transferencia de pedido: #".$id_pedido,
            ]);
            if ($nuevatarea) {
                return Response::json(["msj"=>"Debe esperar aprobación del Administrador","estado"=>false]);
            }

        }

        

        $pedido = pedidos::find($id_pedido);
        $pedido->id_vendedor = $id_usuario;
        if($pedido->save()){
                $u = usuarios::find($id_usuario);
                $mov = new movimientos;
                $mov->id_usuario = session("id_usuario");
                $mov->tipo = "Transferencia de Pedido #".$id_pedido; 
                $mov->motivo = "Para: ".$u->usuario; 
                $mov->tipo_pago = ""; 
                $mov->monto = "";
                $mov->save();
        }
    }
    public function setexportpedido(Request $req)
    {
        $p = pedidos::find($req->id);
        $central = null;
        if ($p) {
            if ($p->export) {
                $p->export = 0;
                $central = (new sendCentral)->setPedidoInCentralFromMaster($req->id,"delete");
            }else {
                $p->export = 1;
                $central = (new sendCentral)->setPedidoInCentralFromMaster($req->id);
            }
            $p->save();
            return $central;
        }
    }
    public function getPedidosFast(Request $req)
    {
        $fecha = $req->fecha1pedido;

        if(isset($req->vendedor)) {
            // code...
            $vendedor = $req->vendedor;
        }else{

            $vendedor = [];
        }
        
        $ret = pedidos::whereBetween("created_at",["$fecha 00:00:01","$fecha 23:59:59"]);

        if (count($vendedor)) {
            $ret->whereIn("id_vendedor",$vendedor);
        }

        return $ret->limit(8)
        ->orderBy("id","desc")
        ->get(["id","estado"]);
    }
    public function get_moneda()
    {   
        if (Cache::has('cop')) {
            $cop = Cache::get('cop');
        }else{
            $cop = moneda::where("tipo",2)->orderBy("id","desc")->first()["valor"];
            Cache::put('cop', $cop);
        }

        
        if (Cache::has('bs')) {
            $bs = Cache::get('bs');
    //
        }else{
            $bs = moneda::where("tipo",1)->orderBy("id","desc")->first()["valor"];
            Cache::put('bs', $bs);
        }

        return ["cop"=>$cop, "bs"=>$bs];
    }
    public function today()
    {

        if (!Cache::has('today'))
        {
            date_default_timezone_set("America/Caracas"); 
            $today = date("Y-m-d");
            
            $fechafixedsql = (new CierresController)->getLastCierre();
    
            if ($fechafixedsql) {
                $Date1 = $fechafixedsql->fecha;
                $fechafixedsqlmas5 = date('Y-m-d', strtotime($Date1 . " + 15 day"));
    
                if (($today < $fechafixedsql->fecha) OR ($today > $fechafixedsqlmas5) ) {
                    throw new \Exception("Fecha incorrecta", 1);
                }else{
                    Cache::put('today', $today, 10800);
                    return $today;
                }
            }else{
                Cache::put('today', $today, 10800);
                return $today;
            }
        }
        else{
            return Cache::get('today');
        }


        

    } 
    public function sumpedidos(Request $req)
    {
        $cop = $this->get_moneda()["cop"];
        $bs = $this->get_moneda()["bs"];


        $ped = pedidos::with(["cliente","pagos","items"=>function($q)
        {

            $q->with("producto");
            $q->orderBy("id","desc");

        }])->whereIn("id",explode(",", $req->id))
        ->get();

        $items = [];

        $cliente = [];

        $subtotal = 0;
        $total_porciento = 0;
        $total_des = 0;
        $total = 0;
        foreach ($ped as $key => $val) {
            if ($key==0) {
                $cliente = $val->cliente;
            }
            foreach ($val["items"] as $item) {
                if (!$item->id_producto) {
                    return "No puede seleccioar un pago: #".$item->id_pedido;

                }
                $ct = 0;
                if (isset($items[$item->id_producto])) {
                    $ct = $items[$item->id_producto]->ct+$item->cantidad;
                }else{
                    $ct = $item->cantidad;
                }
                $item->ct = $ct;
                $items[$item->id_producto] = $item;

                $des = ($item->descuento/100)*$item->monto;
                $total += $item->monto-$des;
                $subtotal+=$item->monto;
                $total_porciento+=$item->descuento;
                $item->total_des = round($des,2);
                $total_des += $des;
            }
        }



        // return $items;

        $sucursal = sucursal::all()->first();

        return view("reportes.sumpedidos",[
            "sucursal" => $sucursal,
            "pedido"=>$items,
            "cliente"=>$cliente,

            "created_at"=> $this->today(),
            "id"=>time(),

            "subtotal"=> ($subtotal*$bs),
            "total_porciento"=> $total_porciento,
            "total_des"=> ($total_des*$bs),
            "total"=> ($total*$bs)
        ]);

    }
    public function getDiaVentaFun($fechaventas)
    {
        

        $arr = $this->cerrarFun($fechaventas,0,0,0,[],true, ( session("tipo_usuario")==1? true: false )  ,false);

        

        if ($fechaventas) {
            // foreach ($this->letras as $key => $value) {
                if (isset($arr["total"])) {
                    $arr["total"] = toLetras(number_format($arr["total"],2));
                }
                if (isset($arr["3"])) {
                    $arr["3"] = toLetras(number_format($arr["3"],2));
                }
                if (isset($arr["2"])) {
                    $arr["2"] = toLetras(number_format($arr["2"],2));
                }

                if (isset($arr["1"])) {
                    $arr["1"] = toLetras(number_format($arr["1"],2));
                }
            // }
        }
        

        return $arr;
    }
    public function getVentas(Request $req)
    {
        $fechaventas = $req->fechaventas;
        return $this->getDiaVentaFun($fechaventas);
        
    }
    public function getPedidos(Request $req)
    {   
        $fact = [];
        $prod = [];

        if(isset($req->vendedor)){
            $vendedor = $req->vendedor;

        }else{
            $vendedor = [];
        }
        $tipobusquedapedido = $req->tipobusquedapedido;
        $busquedaPedido = $req->busquedaPedido;
        $fecha1pedido = $req->fecha1pedido;
        $fecha2pedido = $req->fecha2pedido;

        $filterMetodoPagoToggle = $req->filterMetodoPagoToggle;



        $tipoestadopedido = $req->tipoestadopedido;
        $orderbycolumpedidos = $req->orderbycolumpedidos;
        $orderbyorderpedidos = $req->orderbyorderpedidos;

        $subtotal = 0;
        $desctotal = 0;
        $totaltotal = 0;
        $porctotal = 0;
        $itemstotal = 0;

        $totalventas = 0;

        $limit = 1000;
        if ($fecha1pedido=="" AND $fecha2pedido=="") {
            $fecha1pedido = "0000-00-00";
            $fecha2pedido = "9999-12-31";
            $limit = 25;
        }else if($fecha1pedido == ""){
            $fecha1pedido = "0000-00-00";
            $limit = 25;
        }else if($fecha2pedido == ""){
            $fecha2pedido = "9999-12-31";
            $limit = 25;
        }

        if ($tipobusquedapedido=="prod") {
            $prod = inventario::with([
                "proveedor",
                "categoria",
                "marca",
                "deposito",
            ])
            ->where(function($q) use ($busquedaPedido)
            {
                $q->orWhere("descripcion","LIKE","%$busquedaPedido%")
                ->orWhere("codigo_proveedor","LIKE","%$busquedaPedido%")
                ->orWhere("codigo_barras","LIKE","%$busquedaPedido%");
                
            })
            ->whereIn("id",function($q) use ($vendedor,$fecha1pedido,$fecha2pedido,$tipoestadopedido){
                $q->from("items_pedidos")
                ->whereIn("id_pedido",function($q) use ($vendedor,$fecha1pedido,$fecha2pedido,$tipoestadopedido){
                    $q->from("pedidos")
                    ->whereBetween("created_at",["$fecha1pedido 00:00:01","$fecha2pedido 23:59:59"])
                    ->where(function($q) use ($tipoestadopedido, $vendedor){

                        if (!$tipoestadopedido) {
                            $q->where("estado",false);
                        }
                        if($tipoestadopedido==1){
                            $q->where("estado",true);
                        }
                        if (count($vendedor)) {
                            $q->whereIn("id_vendedor",$vendedor);
                        }
                    })
                    ->select("id");
                })
                ->select("id_producto");

            })
            ->selectRaw("*,@cantidadtotal := (SELECT sum(cantidad) FROM items_pedidos WHERE id_producto=inventarios.id AND created_at BETWEEN '$fecha1pedido 00:00:01' AND '$fecha2pedido 23:59:59') as cantidadtotal,(@cantidadtotal*inventarios.precio) as totalventa")
            ->orderBy("cantidadtotal","desc")
            ->get()
            ->map(function($q)use ($fecha1pedido,$fecha2pedido,$vendedor){
                $items = items_pedidos::whereBetween("created_at",["$fecha1pedido 00:00:01","$fecha2pedido 23:59:59"])
                ->where("id_producto",$q->id);
                if (count($vendedor)) {
                    $items->whereIn("id_pedido",pedidos::whereIn("id_vendedor",$vendedor)->select("id"));
                }
                $q->items = $items->get();

                return $q;
            });


            // code...
        }else if ($tipobusquedapedido=="fact"||$tipobusquedapedido=="cliente") {
           
            $fact = pedidos::with(["pagos"=>function($q){

            },"items"=>function($q)
            {
                
            },"vendedor","cliente"])
            ->whereBetween("created_at",["$fecha1pedido 00:00:01","$fecha2pedido 23:59:59"]);
            
            if ($tipobusquedapedido=="fact") {
                $fact->where("id","LIKE","$busquedaPedido%");
            }

            if ($tipobusquedapedido=="cliente") {
                $fact->whereIn("id_cliente",function($q) use ($busquedaPedido){
                    $q->from("clientes")->orWhere("nombre","LIKE","%$busquedaPedido%")->orWhere("identificacion","LIKE","%$busquedaPedido%")->select("id");

                });
            }
            if (count($vendedor)) {
                $fact->whereIn("id_vendedor",$vendedor);
            }
            if (!$tipoestadopedido) {
                $fact->where("estado",false);
            }
            if($tipoestadopedido==1){
                $fact->where("estado",true);
            }


            if ($filterMetodoPagoToggle!="todos") {
                $fact->whereIn("id",function($q) use ($filterMetodoPagoToggle){
                    $q->select('id_pedido')
                    ->from("pago_pedidos")
                    ->where("tipo",$filterMetodoPagoToggle)
                    ->where("monto","<>",0);
                });
            }
            
            $fact = $fact->selectRaw("*, (SELECT ROUND(sum(monto-(monto*(descuento/100))),2) FROM items_pedidos WHERE id_pedido=pedidos.id) as totales")
            ->orderBy($orderbycolumpedidos, $orderbyorderpedidos)
            ->limit($limit)
            ->get();
            $totaltotal = $fact->sum("totales");

            // ->map(function($q) use (&$subtotal, &$desctotal, &$totaltotal,&$porctotal,&$itemstotal,&$totalventas,$filterMetodoPagoToggle){
            //     // global ;

            //     $fun = $this->getPedidoFun($q->id,$filterMetodoPagoToggle,1,1,1,true);
            //     $q->pedido = $fun;

            //     // $istrue = false; 
            //     if ($filterMetodoPagoToggle=="todos"||count($q->pagos->where("tipo",$filterMetodoPagoToggle)->where("monto","<>",0))) {
            //         $totalventas++;
            //         $itemstotal += count($fun->items);

            //         $subtotal += $fun->clean_subtotal;
            //         $desctotal += $fun->clean_total_des;
            //         $totaltotal += $fun->clean_total;
            //         $porctotal += $fun->clean_total_porciento;
            //         return $q;
            //     }else{
                    
            //     }
            // });  
        }
        return [
            "fact"=>$fact, 
            "prod"=>$prod,
            "subtotal"=>toLetras(number_format($subtotal,2,".",",")), 
            "desctotal"=>$desctotal, 
            "totaltotal"=>toLetras(number_format($totaltotal,2,".",",")),
            "itemstotal"=>$itemstotal,
            "totalventas"=>$totalventas,
        ];
    }
    public function getPedidosUser(Request $req)
    {
        $vendedor = $req->vendedor;
        return pedidos::where("estado",0)->where("id_vendedor",$vendedor)->orderBy("id","desc")->limit(4)->get(["id","estado"]);
    }
    public function pedidoAuth($id,$tipo="pedido")
    {
        $today = $this->today();

        if ($id===null) {
            $fecha_creada = $tipo;
            $estado = true;
        }else{

            $pedido = $tipo=="pedido"? pedidos::select(["estado","created_at"])->find($id): pedidos::select(["estado","created_at"])->find(items_pedidos::find($id)->id_pedido);
            if ($pedido) {
                $fecha_creada = date("Y-m-d",strtotime($pedido->created_at));
               
                $estado = $pedido->estado;
            }else{
                return false;
            }
        }
        //si el pedido no es de hoy, no se puede hacer nada
        if ($fecha_creada != $today) {
            return false;
        }
       //Si no se ha pagado
       //si la fecha de entrada no existe en los cierres
       //si la fecha del ultimo cierre es igual la fecha de entrada


        if (!Cache::has('cierreCount')){
            $cierreCount = cierres::where("fecha",$fecha_creada)->get()->count();
            Cache::put('cierreCount', $cierreCount, 7200);
        }else{
            $cierreCount = Cache::get('cierreCount');
        }

        
    
       if ((!$estado AND $today===$fecha_creada) || !$cierreCount) {
        return true;   
       }else{
        return false;   
       }
    }
    public function checkPedidoPago($id, $tipo = "pedido")
    {
        $pedidomodify = $tipo == "pedido" ? pedidos::find($id) : pedidos::find(items_pedidos::find($id)->id_pedido);
        if ($pedidomodify->estado) {
            $isPermiso = (new TareaslocalController)->checkIsResolveTarea([
                "id_pedido" => $pedidomodify->id,
                "tipo" => "modped",
            ]);

            if ((new UsuariosController)->isAdmin()) {
            } elseif ($isPermiso["permiso"]) {
            } else {
                $nuevatarea = (new TareaslocalController)->createTareaLocal([
                    "id_pedido" => $pedidomodify->id,
                    "tipo" => "modped",
                    "valoraprobado" => 0,
                    "descripcion" => "Modificar pedido",
                ]);
                if ($nuevatarea) {
                    throw new \Exception("Debe esperar aprobación del Administrador", 1);
                }
            }
            $pedidomodify->estado = 0;
            if ($pedidomodify->save()) {
                pago_pedidos::where("id_pedido", $pedidomodify->id)->delete();
            }
        }
    }
    public function checkPedidoAuth($id,$tipo="pedido")
    {   
        if (!$this->pedidoAuth($id,$tipo)) {
            throw new \Exception("No se puede hacer movimientos en esta fecha. ¡Cierre Procesado!", 1);
        }

       
    }
    public function checksipedidoprocesado($id, $tipo="pedido")
    {
        $pedidomodify = $tipo=="pedido"? pedidos::find($id): pedidos::find(items_pedidos::find($id)->id_pedido);
        return $pedidomodify->estado;
    }
    function delpedidoForce(Request $req) {
        //$this->checkPedidoAuth($req->id);
        $this->delPedidoFun($req->id, $req->motivo);
    }
    public function delpedido(Request $req)
    {
        try {
            
            $id = $req->id;
            $motivo = $req->motivo;

            $isPermiso = (new TareaslocalController)->checkIsResolveTarea([
                "id_pedido" => $id,
                "tipo" => "eliminarPedido",
            ]);
            
            if ((new UsuariosController)->isAdmin()) {

                
            }elseif($isPermiso["permiso"]){
                
            }else{

                $nuevatarea = (new TareaslocalController)->createTareaLocal([
                    "id_pedido" =>  $id,
                    "valoraprobado" => 0,
                    "tipo" => "eliminarPedido",
                    "descripcion" => "Solicitud de eliminación de pedido: #".$id,
                ]);
                if ($nuevatarea) {
                    return Response::json(["msj"=>"Debe esperar aprobación del Administrador","estado"=>false]);
                }

            }

            //$this->checkPedidoAuth($id);
            if ($id) {
                $this->delPedidoFun($id, $motivo);
            }
            return Response::json(["msj"=>"Éxito al eliminar. Pedido #".$id,"estado"=>true]);
            
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
            
        }
    }
    public function delPedidoFun($id, $motivo) {
        $mov = new movimientos;
        $mov->id_usuario = session("id_usuario");

        $items = items_pedidos::where("id_pedido",$id)->get();
        $monto_pedido = pago_pedidos::where("id_pedido",$id)->where("monto","<>",0)->get();
        $monto = 0;
        $pagos = "";
        foreach ($monto_pedido as $k => $v) {
            $monto += $v->monto;
            if($v->tipo==1){$pagos .= "Transferencia ";} 
            if($v->tipo==2){$pagos .= "Debito ";}  
            if($v->tipo==3){$pagos .= "Efectivo ";}  
            if($v->tipo==4){$pagos .= "Credito ";}   
            if($v->tipo==5){$pagos .= "Biopago ";} 
            if($v->tipo==6){$pagos .= "vuelto ";} 
        }
        
        $mov->tipo = "Eliminación de Pedido #".$id; 
        $mov->motivo = $motivo; 
        $mov->tipo_pago = $pagos; 
        $mov->monto = $monto;
        $mov->save();


        foreach ($items as $key => $value) {
            (new InventarioController)->hacer_pedido($value->id,null,99,"del");
            

            $items_mov = new items_movimiento;
            $items_mov->id_producto = $value->id_producto;
            $items_mov->cantidad = $value->cantidad;
            $items_mov->tipo = 2;
            $items_mov->categoria = "Eliminación de pedido - Item";
            $items_mov->id_movimiento = $mov->id;
            $items_mov->save();

        }
        pedidos::find($id)->delete();
    }
    public function getPedidoFun($id_pedido,$filterMetodoPagoToggle="todos",$cop=1,$bs=1,$factor=1,$clean=false)
    {
        
        $pedido = pedidos::with(["referencias"=>function($q){
            $q->select(["id","tipo","descripcion","monto","id_pedido","banco"]);
        },"vendedor"=>function($q){
            $q->select(["id","usuario","tipo_usuario","nombre"]);
        },"cliente"=>function($q){
            $q->select(["id","identificacion","nombre"]);
        },"pagos"=>function($q) use ($filterMetodoPagoToggle) 
        {
            // if ($filterMetodoPagoToggle!="todos") {
            //     $q->where("tipo",$filterMetodoPagoToggle);
            // }
            $q->select(["id","tipo","monto","cuenta","id_pedido"]);

        },"items"=>function($q)
        {
            $q->select([
                "id",
                "lote",
                "id_producto",
                "id_pedido",
                "abono",
                "cantidad",
                "descuento",
                "monto",
                "entregado",
            ]);
            $q->with(["producto"=>function($q){
                $q->select([
                "id",
                "codigo_barras",
                "codigo_proveedor",
                "descripcion",
                "precio",
                "precio_base",
                "iva",
                "precio1",
                "bulto",
            ]);
            },"lotedata"]);
            $q->orderBy("id","asc");

        }])->where("id",$id_pedido)->first();

        if ($pedido) {

            $total_des_ped = 0;
            $subtotal_ped = 0;
            $total_ped = 0;            
            $exento = 0;
            $gravable = 0;
            $ivas = "";
            $monto_iva = 0;
            $pedido->items->map(function($item) use (&$exento,&$gravable,&$ivas,&$monto_iva,&$total_des_ped,&$subtotal_ped,&$total_ped,$factor)
            {
                
                if (!$item->producto) {
                    $item->monto = $item->monto*$factor;
                    $subtotal = ($item->monto*$item->cantidad);
                    $iva_val = "0";
                    $iva_m = 0;
                }else{
                    $des_unitario = 0;
                    if ($item->descuento<0) {
                        $item->des_unitario = (($item->descuento/100)*$item->producto["precio"]);
                    }

                    $item->producto["precio"] = ($item->producto["precio"])*$factor;
                    $subtotal = ($item->producto["precio"]*$item->cantidad);
                    $iva_val = $item->producto["iva"];
                    $item->producto["precio_base"] = $item->producto["precio_base"]*$factor;
                    $iva_m = $iva_val/100;

                }
                $total_des = (($item->descuento/100)*$subtotal);

                $total_des_ped += $total_des;
                $subtotal_ped += $subtotal;

                $subtotal_c_desc = $subtotal-$total_des;
                
                if (!$iva_m) {
                    $exento += ($subtotal_c_desc);
                }else{
                    $gravable += ($subtotal_c_desc);
                    $monto_iva += ($subtotal_c_desc)*$iva_m;
                }
                if (strpos($ivas,$iva_val)===false) {
                    $ivas .= $iva_val."%,";
                }
                
                $total_ped += ($subtotal_c_desc)+(($subtotal_c_desc)*$iva_m);

                $item->total_des = number_format($total_des,2,".",",");
                $item->subtotal = number_format($subtotal,2,".",",");
                $item->total = number_format($subtotal_c_desc,2,".",",");




                return $item;
                
            });
            $pedido->tot_items =count($pedido->items);
            $pedido->total_des = number_format(($total_des_ped>0?$total_des_ped:0),2,".",",");
            $pedido->subtotal = number_format($subtotal_ped,2,".",",");
            $pedido->total = number_format(round( $total_ped,3),2,".",",");

            $pedido->exento = number_format($exento,"2");
            $pedido->gravable = number_format($gravable,"2");
            $pedido->ivas = substr($ivas,0,-1);
            $pedido->monto_iva = number_format($monto_iva,"2");

            $pedido->clean_total_des = $total_des_ped;
            $pedido->clean_subtotal = $subtotal_ped;
            $pedido->clean_total = round($total_ped,3);

            
            $pedido->editable = $this->pedidoAuth($id_pedido);
            

            $timestamp = strtotime($pedido->created_at);
            $fecha_separada = date("Y-m-d", $timestamp);

            $pedido->vuelto_entregado = movimientos_caja::where("id_pedido",$pedido->id)->get();
             
            
            if ($subtotal_ped==0) {
                // code...
                $porcen = 0;
            }else{
                $porcen = ($total_des_ped*100)/$subtotal_ped;

            }

            $pedido->total_porciento = number_format($porcen,2,".",",");
            $pedido->clean_total_porciento = $porcen;


            $pedido->cop = number_format($total_ped*$cop,2,".",",");
            $pedido->bs = number_format($total_ped*$bs,2,".",",");

            $pedido->cop_clean = $total_ped*$cop;
            $pedido->bs_clean = $total_ped*$bs;


        }

        if ($clean) {
            return $pedido->makeHidden("items");
        }else{
            return $pedido;
        }
    }
    public function getPedido(Request $req,$factor=1)
    {   
        $cop = $this->get_moneda()["cop"];
        $bs = $this->get_moneda()["bs"];
        

        if ($req->id=="ultimo") {
            $vendedor = session("id_usuario");

            $check = pedidos::where("estado",0)->where("id_vendedor",$vendedor)->orderBy("id","desc")->first();
            // if (!$check) {
            //     $check = pedidos::where("estado",1)->where("id_vendedor",$vendedor)->orderBy("id","desc")->first();
            // }
            
            if (!$check) {
                return [];
            }else{
                $id = $check->id; 
            }
        }else{
            $id = $req->id;
        }
        return $this->getPedidoFun($id,"todos",$cop,$bs,$factor);
    }
    
    public function notaentregapedido(Request $req)
    {
        $sucursal = sucursal::all()->first();
        $bs = $this->get_moneda()["bs"];
        

        return view("reportes.notaentrega",[
            "sucursal"=>$sucursal,
            "pedido"=>$this->getPedido($req,1),
            "bs"=>$bs,
        ]);
    }

    public function setpersonacarrito(Request $req)
    {
        try {
            $this->checkPedidoAuth($req->numero_factura);

            $pedido_select = pedidos::find($req->numero_factura);
            $pedido_select->id_cliente = $req->id_cliente;
            $pedido_select->save();
            return Response::json(["msj"=>"¡Éxito al agregar cliente!","estado"=>true]);

            
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
            
        }
    }

    public function entregadoPendi($fecha,$id_vendedor)
    {
        $entregado = movimientos_caja::where("created_at","LIKE",$fecha."%")->where("tipo",1)->whereIn("id_vendedor",$id_vendedor);
        $entregado_sum = $entregado->sum("monto");

        $pendiente = movimientos_caja::where("created_at","LIKE",$fecha."%")->where("tipo",0)->whereIn("id_vendedor",$id_vendedor);
        $pendiente_sum = $pendiente->sum("monto");

        return [
            "entregado"=>$entregado_sum,
            "pendiente"=>$pendiente_sum,
            "entre_pend_get"=> movimientos_caja::where("created_at","LIKE",$fecha."%")->whereIn("id_vendedor",$id_vendedor)->get(),
        ];
    }
    public function ultimoCierre($fecha,$id_vendedor)
    {
        $fecha_pasadaquery = cierres::where("fecha","<",$fecha)->whereIn("id_usuario",$id_vendedor)->orderBy("fecha","desc")->first();
        if ($fecha_pasadaquery) {
            $fecha_pasada = $fecha_pasadaquery->fecha;
        }else {
            $fecha_pasada = "2023-01-01";
            $id_vendedor = [1];
        }
        //toma a todos los cierres tipo cajero

        return cierres::where("fecha",$fecha_pasada)->whereIn("id_usuario",$id_vendedor)->where("tipo_cierre",0)->orderBy("fecha","desc");
    }

    public function selectUsersTotalizar($totalizarcierre)
    {
        if ($totalizarcierre) {
            return pedidos::select('id_vendedor')->distinct()->get()->map(function($e){
                return $e->id_vendedor;
            });
            
        }else{
            return [session("id_usuario")];
        } 
    }
    
    public function cerrarFun($fecha,$total_caja_neto,$total_punto,$total_biopago,$dejar=[],$grafica=false,$totalizarcierre=false,$check_pendiente=true,$usuario=null)
    {   
        if (!$fecha) {return Response::json(["msj"=>"Error: Fecha inválida","estado"=>false]);}

        // if ($check_pendiente) {
        //     $pedido_pendientes_check = pedidos::where("estado",0)->get();
        //     if (count($pedido_pendientes_check)) {
        //         return Response::json([
        //             "msj" => "Error: Hay pedidos pendientes ".$pedido_pendientes_check->map(function($q){
        //                 return $q->id;
        //             }) ,
        //             "estado" => false
        //         ]);
        //     }
        // }


        $id_vendedor = $usuario?[$usuario]:$this->selectUsersTotalizar($totalizarcierre); 
        
        $usuariosget = usuarios::whereIn("id",$id_vendedor)->get(["id","usuario","tipo_usuario","nombre"]);
        $entregado_fun = $this->entregadoPendi($fecha,$id_vendedor);
        $ultimo_cierre = $this->ultimoCierre($fecha,$id_vendedor);

        $cop = $this->get_moneda()["cop"];
        $bs = $this->get_moneda()["bs"];
            
        $caja_inicial = 0;
        if ($ultimo_cierre) {
            $caja_inicial = round($ultimo_cierre->sum("dejar_dolar") + ($ultimo_cierre->sum("dejar_peso")/$cop) + ($ultimo_cierre->sum("dejar_bss")/$bs),3);
        }
        $pedido = pedidos::where("created_at","LIKE",$fecha."%")->whereIn("id_vendedor",$id_vendedor);

        /////Montos de ganancias
            //Var vueltos_des
            //Var precio
            //Var precio_base
            //Var desc_total
            //Var ganancia
            //Var porcentaje
            $vueltos_des = pago_pedidos::where("tipo",6)->where("monto","<>",0)
            ->whereIn("id_pedido", pedidos::where("created_at","LIKE",$fecha."%")->whereIn("id_vendedor",$id_vendedor)->select("id") )
            ->get()
            ->map(function($q){
                $q->cliente = pedidos::with("cliente")->find($q->id_pedido);
                return $q;
            });


            $inv = items_pedidos::with(["producto","pedido"=>function($q){
                $q->with("cliente");
            }])
            ->whereIn(
                "id_pedido",
                pedidos::where("created_at","LIKE",$fecha."%")
                ->whereIn("id_vendedor",$id_vendedor)
                ->where("estado",1)
                ->select("id")
            )
            ->get()
            ->map(function($q){
                $q->monto_abono = 0;
                if (isset($q->producto)) {
                    
                    $base_total = $q->producto->precio_base*$q->cantidad;
                    $venta_total = $q->producto->precio*$q->cantidad;

                    $descuentopromedio = ($q->descuento/100);

                    $q->base_total = $base_total - ($base_total*$descuentopromedio);
                    $q->venta_total = $venta_total - ($venta_total*$descuentopromedio);

                    $q->sin_base_total = $base_total;
                    $q->sin_venta_total = $venta_total;
                    
                }else{
                    $q->monto_abono = $q->monto;
                }
                return $q;
            });

            $total_credito = pago_pedidos::whereIn("id_pedido",pedidos::where("created_at","LIKE",$fecha."%")->whereIn("id_vendedor",$id_vendedor)->where("tipo",4)->select("id"))
            ->get()
            ->sum("monto");

            $base_total = $inv->sum("base_total");
            $venta_total = $inv->sum("venta_total");

            $sin_base_total = $inv->sum("sin_base_total");
            $sin_venta_total = $inv->sum("sin_venta_total");

            $monto_abono = $inv->sum("monto_abono");

            $ganancia_bruta = ($venta_total-$base_total);
            $porcentaje = $base_total==0? 100: round( (($ganancia_bruta*100) / $base_total),2 ); 
            
            $divisor = ($porcentaje/100) + 1;

            $base_credito = round($total_credito/$divisor,2);
            $venta_credito = $total_credito;
            
            $base_abono = round($monto_abono/$divisor,2);
            $venta_abono = $monto_abono;
             

            $sin_precio_base = ($sin_base_total + $base_abono) - $base_credito ;
            $sin_precio = ($sin_venta_total + $venta_abono) - $venta_credito ;


            $precio_base = ($base_total + $base_abono) - $base_credito ;
            $precio =  $sin_precio;



            $desc_total = ($venta_total + $venta_abono) - $venta_credito;

            $ganancia = $desc_total - $precio_base;
            
            


            
        /////End Montos de ganancias
        $tipo_accion = cierres::where("fecha",$fecha)->where("id_usuario",session("id_usuario"))->first();
        if ($tipo_accion) {
            $tipo_accion = "editar"; 
        }else{
            $tipo_accion = "guardar"; 
        }

        ////Monto Inventario
        $total_inventario = DB::table("inventarios")
        ->select(DB::raw("sum(precio*cantidad) as suma"))->first()->suma;
        $total_inventario_base = DB::table("inventarios")
        ->select(DB::raw("sum(precio_base*cantidad) as suma"))->first()->suma;
        
        ////Creditos totales
        $cred_total = clientes::selectRaw("*,@credito := (SELECT COALESCE(sum(monto),0) FROM pago_pedidos WHERE id_pedido IN (SELECT id FROM pedidos WHERE id_cliente=clientes.id) AND tipo=4) as credito,
        @abono := (SELECT COALESCE(sum(monto),0) FROM pago_pedidos WHERE id_pedido IN (SELECT id FROM pedidos WHERE id_cliente=clientes.id) AND cuenta=0) as abono,
        (@credito-@abono) as saldo")
        ->get(["saldo"])
        ->sum("saldo");

        
        ////Vueltos totales
        $vuelto_entregado = movimientos_caja::where('categoria',1)
        ->where('tipo',1)
        ->whereIn('id_vendedor',$id_vendedor)
        ->sum('monto');

        $vueltos_pendiente = pago_pedidos::where("tipo",6)
        ->where("monto","<>",0)
        ->whereIn('id_pedido',function($q) use ($id_vendedor){
            $q->from('pedidos')->whereIn('id_vendedor',$id_vendedor)->select('id');
        })
        ->sum("monto");

        $vueltos_totales = $vueltos_pendiente-$vuelto_entregado;


        ///Abonos del Dia
        $abonosdeldia = 0;
        $pedidos_abonos = pedidos::with(["pagos","cliente"])
        ->where(function($q){
            $q
            ->whereIn("id",pago_pedidos::orWhere(function($q){
                $q->orWhere("cuenta",0); //Abono
            })
            ->where("monto","<>",0)
            ->select("id_pedido"));
        })
        ->where("created_at","LIKE",$fecha."%")
        ->get()
        ->map(function($q) use (&$abonosdeldia){
            $saldoDebe = $q->pagos->where("tipo",4)->sum("monto");
            $saldoAbono = $q->pagos->where("cuenta",0)->sum("monto");

            $q->saldoDebe = $saldoDebe;
            $q->saldoAbono = $saldoAbono;

            $abonosdeldia += $q->pagos->sum("monto");
            return $q;
        });

        $arr_pagos = [
            "total_inventario" => $total_inventario,
            "total_inventario_base" => $total_inventario_base,
            
            "cred_total" => $cred_total,
            "vueltos_totales" => $vueltos_totales,
            "pedidos_abonos" => $pedidos_abonos,
            "abonosdeldia" => $abonosdeldia,

            "total"=>0,
            "fecha"=>$fecha,
            "caja_inicial"=>$caja_inicial,

            "numventas"=>0,
            "grafica"=>[],
            "ventas"=>[],

            "entregadomenospend"=>0,
            "entregado" => $entregado_fun["entregado"],
            "pendiente" => $entregado_fun["pendiente"],
            "entre_pend_get" => $entregado_fun["entre_pend_get"],

            "total_caja" => 0,
            "total_punto" => 0,
            "total_biopago" => 0,
            

            "estado_efec" => 0,
            "msj_efec" => "",

            "estado_punto" => 0,
            "msj_punto" => "",
            //Montos de ganancias
            "vueltos_des" => $vueltos_des,
            "precio" => $precio,
            "precio_base" => $precio_base,
            "desc_total" => $desc_total,
            "ganancia" => $ganancia,
            "porcentaje" => $porcentaje,
            //
            1=>0,
            2=>0,
            3=>0,
            4=>0,
            5=>0,
            6=>0,

            "usuariosget" => $usuariosget,
            "tipo_accion" => $tipo_accion,
            
        ];
        $numventas_arr = [];
        pago_pedidos::whereIn("id_pedido",$pedido->select("id"))
        ->where("monto","<>",0)
        ->orderBy("id","desc")
        ->get()
        ->map(function($q) use (&$arr_pagos,&$numventas_arr){
            if (array_key_exists($q->tipo,$arr_pagos)) {
                $arr_pagos[$q->tipo] += $q->monto;
            }else{
                $arr_pagos[$q->tipo] = $q->monto;
            }
            if ($q->tipo!=4&&$q->tipo!=6) {
                $hora = date("h:i",strtotime($q->updated_at));
                if (!array_key_exists($q->id_pedido,$numventas_arr)) {
                    $numventas_arr[$q->id_pedido] = ["hora"=>$hora,"monto"=>$q->monto,"id_pedido"=>$q->id_pedido];
                }else {
                    $numventas_arr[$q->id_pedido]["monto"] = $numventas_arr[$q->id_pedido]["monto"]+$q->monto;
                }
                $arr_pagos["total"] += $q->monto;
            }
        });
        $arr_pagos["numventas"] = count($numventas_arr);
        $arr_pagos["ventas"] = array_values($numventas_arr);
        if ($grafica) {
            $arr_pagos["grafica"] = array_values($numventas_arr);
        }
        if (isset($arr_pagos[6])) {
            // Sumar vuelto a pendientes
            $arr_pagos["pendiente"] += $arr_pagos[6]; 
        }
        $entregadomenospend = $arr_pagos["entregado"]-$arr_pagos["pendiente"];
        $arr_pagos["entregadomenospend"] = $entregadomenospend; 


        if (isset($arr_pagos[2])) {
            $this->msj_cuadre($total_punto,$arr_pagos[2],"punto",$arr_pagos);
            $arr_pagos["total_punto"] = round($total_punto,3); 
        }
        if (isset($arr_pagos[5])) {
            $this->msj_cuadre($total_biopago,$arr_pagos[5],"biopago",$arr_pagos);
            $arr_pagos["total_biopago"] = round($total_biopago,3); 
        }
        if (isset($arr_pagos[3])) {
            $total_caja = ($total_caja_neto - $caja_inicial) + $entregadomenospend;
            $arr_pagos["total_caja"] = round($total_caja,3); 

            $this->msj_cuadre($total_caja,$arr_pagos[3],"efec",$arr_pagos);
        }


        $dejar_usd = 0;
        $dejar_cop = 0;
        $dejar_bs = 0;

        if ($dejar) {
            $dejar_usd = floatval($dejar["dejar_usd"]);
            $dejar_cop = floatval($dejar["dejar_cop"]);
            $dejar_bs = floatval($dejar["dejar_bs"]);
        }

        
        $efectivo_guardado = floatval($arr_pagos["total_caja"])+floatval($caja_inicial)-($entregadomenospend)-floatval($dejar_usd + ($dejar_cop/$cop) + ($dejar_bs/$bs));

        $arr_pagos["efectivo_guardado"] = round($efectivo_guardado,2);

        return $arr_pagos;
    }
    public function getCierres(Request $req)
    {

        $fechaGetCierre = $req->fechaGetCierre;
        $fechaGetCierre2 = $req->fechaGetCierre2;
        $tipoUsuarioCierre = $req->tipoUsuarioCierre;
        
        if (!$fechaGetCierre&&!$fechaGetCierre2) {
            $cierres = cierres::with("usuario")
            ->when($tipoUsuarioCierre!="",function($q) use ($tipoUsuarioCierre){
                $q->where("tipo_cierre",$tipoUsuarioCierre);
            })
            ->orderBy("fecha","desc");
        }else{
            $cierres = cierres::with("usuario")
            ->whereBetween("fecha",[$fechaGetCierre,$fechaGetCierre2])
            ->when($tipoUsuarioCierre!="",function($q) use ($tipoUsuarioCierre){
                $q->where("tipo_cierre",$tipoUsuarioCierre);
            })
            ->orderBy("fecha","desc");
        }
        
        
        return [
            "cierres"=>$cierres->get(),
            "numventas"=>$cierres->sum("numventas"),
            
            "debito" => number_format($cierres->sum("debito"),2),
            "efectivo" => number_format($cierres->sum("efectivo"),2),
            "transferencia" => number_format($cierres->sum("transferencia"),2),
            "caja_biopago" => number_format($cierres->sum("caja_biopago"),2),
            

            "precio" => number_format($cierres->sum("precio"),2),
            "precio_base" => number_format($cierres->sum("precio_base"),2),

            "ganancia" => number_format($cierres->sum("ganancia"),2),
            "porcentaje" => number_format($cierres->avg("porcentaje"),2),

            


            "dejar_dolar" => number_format($cierres->sum("dejar_dolar"),2),
            "dejar_peso" => number_format($cierres->sum("dejar_peso"),2),
            "dejar_bss" => number_format($cierres->sum("dejar_bss"),2),
            "efectivo_guardado" => number_format($cierres->sum("efectivo_guardado"),2),
            "efectivo_guardado_cop" => number_format($cierres->sum("efectivo_guardado_cop"),2),
            "efectivo_guardado_bs" => number_format($cierres->sum("efectivo_guardado_bs"),2),
            "efectivo_actual" => number_format($cierres->sum("efectivo_actual"),2),
            "efectivo_actual_cop" => number_format($cierres->sum("efectivo_actual_cop"),2),
            "efectivo_actual_bs" => number_format($cierres->sum("efectivo_actual_bs"),2),
            "puntodeventa_actual_bs" => number_format($cierres->sum("puntodeventa_actual_bs"),2),
            
        ];
        
    }
    public function cerrar(Request $req)
    {
        $today = (new PedidosController)->today();
        
        return $this->cerrarFun(
            $today,
            $req->total_caja_neto,
            $req->total_punto,
            $req->total_biopago,
            
            ["dejar_bs"=>$req->dejar_bs, "dejar_usd"=>$req->dejar_usd, "dejar_cop"=>$req->dejar_cop],
            false,
            filter_var($req->totalizarcierre, FILTER_VALIDATE_BOOLEAN),

        );
    }

    public function msj_cuadre($total_entregado,$monto_facturado,$clave,&$arr_pagos,$tolerancia=10)
    {
        if ($monto_facturado OR $monto_facturado===0) {
            $diff = round($total_entregado-$monto_facturado,3);
            if (($diff>=0) && ($diff<=$tolerancia)) {
                $arr_pagos["msj_".$clave] = "Cuadrado. Sobran ".$diff;
                $arr_pagos["estado_".$clave] = 1;
            }
            elseif($diff>=$tolerancia){
                $arr_pagos["msj_".$clave] = "Err. Sobran ".$diff;
                $arr_pagos["estado_".$clave] = 0;
            }

            elseif(($diff<=0)){
                $arr_pagos["msj_".$clave] = "Faltan ".$diff;
                $arr_pagos["estado_".$clave] = 0;
            }
        }
    }

    

    public function guardarCierre(Request $req)
    {
        try {
            $today = (new PedidosController)->today();
            
            $cop = $this->get_moneda()["cop"];
            $bs = $this->get_moneda()["bs"];

            $totalizarcierre = filter_var($req->totalizarcierre, FILTER_VALIDATE_BOOLEAN);
            
            $id_vendedor = $this->selectUsersTotalizar($totalizarcierre);
            $tipo_cierre = $totalizarcierre?1:0;


            $last_cierre = cierres::whereIn("id_usuario",$id_vendedor)->orderBy("fecha","desc")->first();
            $check = cierres::whereIn("id_usuario",$id_vendedor)->where("fecha",$today)->first();
            
            $fecha_ultimo_cierre = "0";
            if ($last_cierre) {
                $fecha_ultimo_cierre = $last_cierre["fecha"];
            }
            
            $id_usuario = session("id_usuario");
            if ($check===null || $fecha_ultimo_cierre==$today) {
                Cache::forget('lastcierres');
                Cache::forget('cierreCount');
                Cache::forget('today');
                
                if ($req->tipo_accionCierre=="guardar") {
                    $objcierres = new cierres;
                    
                    $objcierres->caja_biopago = floatval($req->total_biopago);
                    $objcierres->debito = floatval($req->total_punto);
                    $objcierres->efectivo = floatval($req->efectivo);
                    $objcierres->transferencia = floatval($req->transferencia);
                    $objcierres->dejar_dolar = floatval($req->dejar_usd);
                    $objcierres->dejar_peso = floatval($req->dejar_cop);
                    $objcierres->dejar_bss = floatval($req->dejar_bs);

                    $objcierres->efectivo_guardado = floatval($req->guardar_usd);
                    $objcierres->efectivo_guardado_cop = floatval($req->guardar_cop);
                    $objcierres->efectivo_guardado_bs = floatval($req->guardar_bs);
                    $objcierres->tasa = $bs;
                    $objcierres->nota = $req->notaCierre;
                    $objcierres->id_usuario = $id_usuario;
                    $objcierres->fecha = $today;

                    $objcierres->precio = floatval($req->precio);
                    $objcierres->precio_base = floatval($req->precio_base);
                    $objcierres->ganancia = floatval($req->ganancia);
                    $objcierres->porcentaje = floatval($req->porcentaje);
                    $objcierres->numventas = intval($req->numventas);
                    $objcierres->desc_total = floatval($req->desc_total);
                    
                    $objcierres->efectivo_actual = floatval($req->caja_usd);
                    $objcierres->efectivo_actual_cop = floatval($req->caja_cop);
                    $objcierres->efectivo_actual_bs = floatval($req->caja_bs);
                    $objcierres->puntodeventa_actual_bs = floatval($req->caja_punto);
                    
                    $objcierres->tipo_cierre = $tipo_cierre;

                    $objcierres->tasacop = $cop;

                    $objcierres->numreportez = $req->numreportez;
                    $objcierres->ventaexcento = floatval($req->ventaexcento);
                    $objcierres->ventagravadas = floatval($req->ventagravadas);
                    $objcierres->ivaventa = floatval($req->ivaventa);
                    $objcierres->totalventa = floatval($req->totalventa);
                    $objcierres->ultimafactura = $req->ultimafactura;
                    
                    $objcierres->efecadiccajafbs = floatval($req->efecadiccajafbs);
                    $objcierres->efecadiccajafcop = floatval($req->efecadiccajafcop);
                    $objcierres->efecadiccajafdolar = floatval($req->efecadiccajafdolar);
                    $objcierres->efecadiccajafeuro = floatval($req->efecadiccajafeuro);

                    
                    $objcierres->inventariobase = floatval($req->inventariobase);
                    $objcierres->inventarioventa = floatval($req->inventarioventa);

                    $objcierres->credito = floatval($req->credito);
                    $objcierres->creditoporcobrartotal = floatval($req->creditoporcobrartotal);
                    $objcierres->vueltostotales = floatval($req->vueltostotales);
                    $objcierres->abonosdeldia = floatval($req->abonosdeldia);
                    
                    $objcierres->save();

                }else if($req->tipo_accionCierre=="editar"){
                    
                    cierres::updateOrCreate(
                        ["fecha"=>$today, "id_usuario" => $id_usuario],
                        [
                            "caja_biopago" => floatval($req->total_biopago),
                            "debito" => floatval($req->total_punto),
                            "efectivo" => floatval($req->efectivo),
                            "transferencia" => floatval($req->transferencia),
                            "dejar_dolar" => floatval($req->dejar_usd),
                            "dejar_peso" => floatval($req->dejar_cop),
                            "dejar_bss" => floatval($req->dejar_bs),
        
                            "efectivo_guardado" => floatval($req->guardar_usd),
                            "efectivo_guardado_cop" => floatval($req->guardar_cop),
                            "efectivo_guardado_bs" => floatval($req->guardar_bs),
                            "tasa" => $bs,
                            "nota" => $req->notaCierre,
                            "id_usuario" => $id_usuario,
        
                            "precio" => floatval($req->precio),
                            "precio_base" => floatval($req->precio_base),
                            "ganancia" => floatval($req->ganancia),
                            "porcentaje" => floatval($req->porcentaje),
                            "numventas" => intval($req->numventas),
                            "desc_total" => floatval($req->desc_total),

                            "efectivo_actual" => floatval($req->caja_usd),
                            "efectivo_actual_cop" => floatval($req->caja_cop),
                            "efectivo_actual_bs" => floatval($req->caja_bs),
                            "puntodeventa_actual_bs" => floatval($req->caja_punto),

                            "tasacop" => $cop,
                            "inventariobase" => floatval($req->inventariobase),
                            "inventarioventa" => floatval($req->inventarioventa),
                            "numreportez" => $req->numreportez,
                            "ventaexcento" => floatval($req->ventaexcento),
                            "ventagravadas" => floatval($req->ventagravadas),
                            "ivaventa" => floatval($req->ivaventa),
                            "totalventa" => floatval($req->totalventa),
                            "ultimafactura" => $req->ultimafactura,
                            "credito" => floatval($req->credito),
                            "creditoporcobrartotal" => floatval($req->creditoporcobrartotal),
                            "vueltostotales" => floatval($req->vueltostotales),
                            "abonosdeldia" => floatval($req->abonosdeldia),
                            "efecadiccajafbs" => floatval($req->efecadiccajafbs),
                            "efecadiccajafcop" => floatval($req->efecadiccajafcop),
                            "efecadiccajafdolar" => floatval($req->efecadiccajafdolar),
                            "efecadiccajafeuro" => floatval($req->efecadiccajafeuro),
                            
                            
                        ]
        
                    );
                }
            }else {
                throw new \Exception("Cierre de la fecha: ".$fecha_ultimo_cierre." procesado. No se pueden hacer cambios.", 1);
            }

            return Response::json(["msj"=>"¡Cierre guardado exitosamente!","estado"=>true]);

        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getCode()." ".$e->getMessage(),"estado"=>false]);
            
        }
    }
    
    public function verCierre(Request $req)
    {   
        $fechareq = $req->fecha;
        $type = $req->type;
        $usuario = isset($req->usuario)? $req->usuario: null;
        
        $usuarioLogin = $usuario? $usuario: session("id_usuario");

        $sucursal = sucursal::all()->first();

        $totalizarcierre = filter_var($req->totalizarcierre, FILTER_VALIDATE_BOOLEAN);
        if ($totalizarcierre) {
            $id_vendedor =  pedidos::select('id_vendedor')->distinct()->get()->map(function($e){
                return $e->id_vendedor;
            });
            
        }else{
            $id_vendedor = [$usuarioLogin];
        } 

        $cierre = cierres::with("usuario")->where("fecha",$fechareq)->where('id_usuario',$usuarioLogin)->first();
        if (!$cierre) {
            return "No hay cierre guardado para esta fecha";
        }
        
        $pagos_referencias = pagos_referencias::where("created_at","LIKE",$fechareq."%")
        ->whereIn('id_pedido',function($q) use ($id_vendedor){
            $q->from('pedidos')->whereIn('id_vendedor',$id_vendedor)->select('id');
        })
        ->orderBy("tipo","desc")->get();

        $movimientos = movimientos::with(["usuario"=>function($q){
            $q->select(["id","nombre","usuario"]);
        },"items"=>function($q){
            $q->with("producto");
        }])->where("created_at","LIKE",$fechareq."%")->get();

        $movimientosInventario = movimientosInventario::with(["usuario"=>function($q){
            $q->select(["id","nombre","usuario"]);
        }])->where("created_at","LIKE",$fechareq."%")
        ->get()
        ->map(function($q){
            if ($q->antes) {
                $q->antes = json_decode($q->antes);
            }
            if ($q->despues) {
                $q->despues = json_decode($q->despues);
            }
            return $q;
        });

        $facturado = $this->cerrarFun($fechareq,0,0,0,[],false,$totalizarcierre, true, $usuario? $usuario: null);
        if (is_array($facturado)) {
            $total_inventario = $facturado["total_inventario"];
            $total_inventario_base = $facturado["total_inventario_base"];
            $cred_total = $facturado["cred_total"];
            $vueltos_totales = $facturado["vueltos_totales"];
            $pedidos_abonos = $facturado["pedidos_abonos"];
            $abonosdeldia = $facturado["abonosdeldia"];
        }else{
            return $facturado;
        }

        
        if (is_object($facturado)) {
            return $facturado;
        }
        $arr_send = [
            "referencias"=>$pagos_referencias,
            "cierre" => $cierre,
            "cierre_tot" => moneda($cierre->debito+$cierre->efectivo+$cierre->transferencia+$cierre->caja_biopago),
           
            "total_inventario" =>($total_inventario),
            "total_inventario_format" =>moneda($total_inventario),
            "total_inventario_base" =>($total_inventario_base),
            "total_inventario_base_format" =>moneda($total_inventario_base),
           
            "vueltos_totales" =>moneda($vueltos_totales),
            "vueltos_des" => $facturado["vueltos_des"],

            "precio"=> moneda($facturado["precio"]),
            "precio_base"=> moneda($facturado["precio_base"]),
            "ganancia"=> moneda(round($facturado["ganancia"],2)),
            "porcentaje"=> moneda($facturado["porcentaje"]),
            "desc_total"=> moneda(round($facturado["desc_total"],2)),
            "facturado" => $facturado,
            "facturado_tot" => moneda($facturado[2]+$facturado[3]+$facturado[1]+$facturado[5]),
            "sucursal"=>$sucursal,
            "movimientos"=>$movimientos,
            "movimientosInventario"=>$movimientosInventario,
        ];

        
        $arr_send["cierre"]["debito"] = moneda($arr_send["cierre"]["debito"]);
        $arr_send["cierre"]["efectivo"] = moneda($arr_send["cierre"]["efectivo"]);
        $arr_send["cierre"]["transferencia"] = moneda($arr_send["cierre"]["transferencia"]);
        $arr_send["cierre"]["caja_biopago"] = moneda($arr_send["cierre"]["caja_biopago"]);

        $arr_send["cierre"]["dejar_dolar"] = moneda($arr_send["cierre"]["dejar_dolar"]);
        $arr_send["cierre"]["dejar_peso"] = moneda($arr_send["cierre"]["dejar_peso"]);
        $arr_send["cierre"]["dejar_bss"] = moneda($arr_send["cierre"]["dejar_bss"]);
        $arr_send["cierre"]["tasa"] = moneda($arr_send["cierre"]["tasa"]);
        $arr_send["cierre"]["efectivo_guardado"] = moneda($arr_send["cierre"]["efectivo_guardado"]);
        $arr_send["cierre"]["efectivo_guardado_cop"] = moneda($arr_send["cierre"]["efectivo_guardado_cop"]);
        $arr_send["cierre"]["efectivo_guardado_bs"] = moneda($arr_send["cierre"]["efectivo_guardado_bs"]);
        $arr_send["facturado"]["total"] = moneda($arr_send["facturado"]["total"]);
        $arr_send["facturado"]["caja_inicial"] = moneda($arr_send["facturado"]["caja_inicial"]);
        
        $arr_send["facturado"]["entregadomenospend"] = moneda($arr_send["facturado"]["entregadomenospend"]);
        $arr_send["facturado"]["entregado"] = moneda($arr_send["facturado"]["entregado"]);
        $arr_send["facturado"]["pendiente"] = moneda($arr_send["facturado"]["pendiente"]);
        $arr_send["facturado"]["total_caja"] = moneda($arr_send["facturado"]["total_caja"]);
        $arr_send["facturado"]["total_punto"] = moneda($arr_send["facturado"]["total_punto"]);
        $arr_send["facturado"]["total_biopago"] = moneda($arr_send["facturado"]["total_biopago"]);

        $arr_send["facturado"]["1"] = moneda($arr_send["facturado"]["1"]);
        $arr_send["facturado"]["2"] = moneda($arr_send["facturado"]["2"]);
        $arr_send["facturado"]["3"] = moneda($arr_send["facturado"]["3"]);
        $arr_send["facturado"]["4"] = moneda($arr_send["facturado"]["4"]);
        $arr_send["facturado"]["5"] = moneda($arr_send["facturado"]["5"]);
        $arr_send["facturado"]["6"] = moneda($arr_send["facturado"]["6"]);
        
        $arr_send["total_inventario_format"] = toLetras($arr_send["total_inventario_format"]); 
        $arr_send["total_inventario_base_format"] = toLetras($arr_send["total_inventario_base_format"]); 
        $arr_send["vueltos_totales"] = toLetras($arr_send["vueltos_totales"]); 
        $arr_send["precio"] = toLetras($arr_send["precio"]); 
        $arr_send["precio_base"] = toLetras($arr_send["precio_base"]); 
        $arr_send["ganancia"] = toLetras($arr_send["ganancia"]); 
        $arr_send["porcentaje"] = toLetras($arr_send["porcentaje"]); 
        $arr_send["desc_total"] = toLetras($arr_send["desc_total"]); 
        $arr_send["cierre_tot"] = toLetras($arr_send["cierre_tot"]); 
        $arr_send["facturado_tot"] = toLetras($arr_send["facturado_tot"]); 

        $arr_send["cierre"]["debito"] = toLetras($arr_send["cierre"]["debito"]); 
        $arr_send["cierre"]["efectivo"] = toLetras($arr_send["cierre"]["efectivo"]); 
        $arr_send["cierre"]["transferencia"] = toLetras($arr_send["cierre"]["transferencia"]); 
        $arr_send["cierre"]["caja_biopago"] = toLetras($arr_send["cierre"]["caja_biopago"]); 
        
        $arr_send["cierre"]["dejar_dolar"] = toLetras($arr_send["cierre"]["dejar_dolar"]);
        $arr_send["cierre"]["dejar_peso"] = toLetras($arr_send["cierre"]["dejar_peso"]);
        $arr_send["cierre"]["dejar_bss"] = toLetras($arr_send["cierre"]["dejar_bss"]);
        $arr_send["cierre"]["tasa"] = toLetras($arr_send["cierre"]["tasa"]);

        $arr_send["cierre"]["efectivo_guardado"] = toLetras($arr_send["cierre"]["efectivo_guardado"]);
        $arr_send["cierre"]["efectivo_guardado_cop"] = toLetras($arr_send["cierre"]["efectivo_guardado_cop"]);
        $arr_send["cierre"]["efectivo_guardado_bs"] = toLetras($arr_send["cierre"]["efectivo_guardado_bs"]);

        $arr_send["facturado"]["numventas"] = toLetras($arr_send["facturado"]["numventas"]);
        $arr_send["facturado"]["total"] = toLetras($arr_send["facturado"]["total"]);
        $arr_send["facturado"]["caja_inicial"] = toLetras($arr_send["facturado"]["caja_inicial"]);
        $arr_send["facturado"]["entregadomenospend"] = toLetras($arr_send["facturado"]["entregadomenospend"]);
        $arr_send["facturado"]["entregado"] = toLetras($arr_send["facturado"]["entregado"]);
        $arr_send["facturado"]["pendiente"] = toLetras($arr_send["facturado"]["pendiente"]);
        $arr_send["facturado"]["total_caja"] = toLetras($arr_send["facturado"]["total_caja"]);
        $arr_send["facturado"]["total_punto"] = toLetras($arr_send["facturado"]["total_punto"]);
        $arr_send["facturado"]["total_biopago"] = toLetras($arr_send["facturado"]["total_biopago"]);
        
        $arr_send["facturado"]["1"] = toLetras($arr_send["facturado"]["1"]);
        $arr_send["facturado"]["2"] = toLetras($arr_send["facturado"]["2"]);
        $arr_send["facturado"]["3"] = toLetras($arr_send["facturado"]["3"]);
        $arr_send["facturado"]["4"] = toLetras($arr_send["facturado"]["4"]);
        $arr_send["facturado"]["5"] = toLetras($arr_send["facturado"]["5"]);
        $arr_send["facturado"]["6"] = toLetras($arr_send["facturado"]["6"]);
        
        $arr_send["cred_total"] = $cred_total;
        $arr_send["pedidos_abonos"] = $pedidos_abonos;
        $arr_send["abonosdeldia"] = $abonosdeldia;

        if ($type=="ver") {
            return view("reportes.cierre",$arr_send);
        }else{
            //Enviar Central

            // (new sendCentral)->setGastos();
            // (new sendCentral)->setCentralData();
            // (new sendCentral)->setVentas();

            //Enviar Cierre

            $from1 = $sucursal->correo;
            $from = $sucursal->sucursal;
            $subject = $sucursal->sucursal." | CIERRE DIARIO | ".$fechareq;
            $mensajes = "";
            try {
                \Artisan::call('database:backup'); //Hacer respaldo Local
                \Artisan::call('backup:run'); //Enviar Respaldo al correo
                
                $sendGastos = (new sendCentral)->sendGastos();
                //$mensajes  = "[ Envio de Gastos: $sendGastos ], ";
                
                //$sendGarantias = (new sendCentral)->sendGarantias();
                // $mensajes .= "[ Envio de Garantias: $sendGarantias ], ";
                
                // //$sendFallas = (new sendCentral)->sendFallas(); 
                // $mensajes .= "[ Envio de Fallas: $sendFallas ], "; 
                
                // //$sendInventario = (new sendCentral)->sendInventario();
                // $mensajes .= "[ Envio de Inventario: $sendInventario ], ";
                
                // //$sendCierreCentral = (new sendCentral)->sendCierres($cierre->id);
                // $mensajes .= "[ Cierre a Central: $sendCierreCentral ], ";
                
                $enviarcierrecorreo = Mail::to($this->sends())->send(new enviarCierre($arr_send,$from1,$from,$subject));    
                $mensajes .= "Cierre al correo: $enviarcierrecorreo "; 
               
                




                return Response::json(["msj" => $mensajes ,"estado" => true]);
            
            } catch (\Exception $e) {

                return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
                
            }

        }
    }

    public function sendCuentasporCobrar()
    {   
        $today = $this->today();
        $sucursal = sucursal::all()->first();

        $from1 = $sucursal->correo;
        $from = $sucursal->sucursal;
        $subject = $sucursal->sucursal." | CUENTAS POR COBRAR | ".$today;
        $data = (new PagoPedidosController)->getDeudoresFun("","saldo","asc",$today);
        try {
            
            Mail::to($this->sends())->send(new enviarCuentaspagar([
                "data" => $data,
                "sucursal" => $sucursal,
                "today"=>$today
            ],$from1,$from,$subject));    
            
            return Response::json(["msj"=>"Cuentas enviadas con Éxito","estado"=>true]);
        
        } catch (\Exception $e) {

            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
            
        }
    }

    

    
    
}
