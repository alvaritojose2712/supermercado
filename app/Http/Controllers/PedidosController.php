<?php

namespace App\Http\Controllers;

set_time_limit(300000);
use Illuminate\Support\Facades\DB;
use App\Models\cierres;
use App\Models\pedidos;
use App\Models\moneda;
use App\Models\inventario;
use App\Models\items_pedidos;
use App\Models\pago_pedidos;
use App\Models\clientes;

use App\Models\movimientos_caja;
use App\Models\sucursal;
use App\Models\movimientos;
use App\Models\items_movimiento;
use App\Models\pagos_referencias;


use Illuminate\Support\Facades\Cache;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Mail\enviarCierre;
use App\Mail\enviarCuentaspagar;

use Response;

class PedidosController extends Controller
{   

    protected $sends = [
        "yanethkarismar@gmail.com",           
        "alvaroospino79@gmail.com",           
    ];
    protected  $letras = [
                1=>"L",
                2=>"R",
                3=>"E",
                4=>"A",
                5=>"S",
                6=>"G",
                7=>"F",
                8=>"B",
                9=>"P",
                0=>"X",
            ];

    public function setexportpedido(Request $req)
    {
        $p = pedidos::find($req->id);

        if ($p) {
            if ($p->export) {
                $p->export = 0;
            }else {
                $p->export = 1;
            }
            $p->save();
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

        return $ret->limit(4)
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
        return date("Y-m-d");
    } 
    public function sumpedidos(Request $req)
    {
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

            "subtotal"=> round($subtotal,2),
            "total_porciento"=> $total_porciento,
            "total_des"=> round($total_des,2),
            "total"=> round($total,2)
        ]);

    }
    public function getVentas(Request $req)
    {
        $fechaventas = $req->fechaventas;

        $arr = $this->cerrarFun($fechaventas,0,0,[],true);

        if ($fechaventas) {
            // code...
           

            // foreach ($this->letras as $key => $value) {
                if (isset($arr["total"])) {
                    $arr["total"] = toLetras(number_format($arr["total"],2));
                    // code...
                }
                if (isset($arr["3"])) {
                    // code...
                    $arr["3"] = toLetras(number_format($arr["3"],2));
                }
                if (isset($arr["2"])) {
                    $arr["2"] = toLetras(number_format($arr["2"],2));
                    // code...
                }

                if (isset($arr["1"])) {
                    $arr["1"] = toLetras(number_format($arr["1"],2));
                    // code...
                }
            // }
        }
        

        return $arr;
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
            "subtotal"=>number_format($subtotal,2,".",","), 
            "desctotal"=>$desctotal, 
            "totaltotal"=>number_format($totaltotal,2,".",","),
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

        if ($id===null) {
            $fecha_creada = $tipo;
            $estado = true;
        }else{

            if ($tipo=="pedido") {
                $pedido = pedidos::select(["estado","created_at"])->find($id);
            }else{
                $pedido = pedidos::select(["estado","created_at"])->find(items_pedidos::find($id)->id_pedido);
            }
            $fecha_creada = date("Y-m-d",strtotime($pedido->created_at));
           
            $estado = $pedido->estado;
            $last_cierre = cierres::orderBy("fecha","desc")->first();
        }

       //Si no se ha pagado
       //si la fecha de entrada no existe en los cierres
       //si la fecha del ultimo cierre es igual la fecha de entrada
    
       $today = $this->today();
       if (!$estado || !cierres::where("fecha",$fecha_creada)->get()->count() || $fecha_creada == $today) {
        return true;   
       }else{
        return false;   
       }
    }
    public function checkPedidoAuth($id,$tipo="pedido")
    {   
        if (!$this->pedidoAuth($id,$tipo)) {
            throw new \Exception("No se puede hacer movimientos en esta fecha. ¡Cierre Procesado!", 1);
        }

       
    }
    public function delpedido(Request $req)
    {


        try {
            $id = $req->id;
            $motivo = $req->motivo;
            $this->checkPedidoAuth($id);
            if ($id) {
                $mov = new movimientos;

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
                   if($v->tipo==5){$pagos .= "Otros ";} 
                   if($v->tipo==6){$pagos .= "vuelto ";} 
               }
                
                $mov->tipo = "Eliminación de Pedido"; 
                $mov->motivo = $motivo; 
                $mov->tipo_pago = $pagos; 
                $mov->monto = $monto;
                $mov->save();


                foreach ($items as $key => $value) {
                   (new InventarioController)->hacer_pedido($value->id,null,99,"del");
                   

                   $items_mov = new items_movimiento;
                   $items_mov->id_producto = $value->id;
                   $items_mov->cantidad = $value->cantidad;
                   $items_mov->tipo = 2;
                   $items_mov->categoria = "Eliminación de pedido - Item";
                   $items_mov->id_movimiento = $mov->id;
                   $items_mov->save();

                }
                pedidos::find($id)->delete();
            }
            return Response::json(["msj"=>"Éxito al eliminar. Pedido #".$id,"estado"=>true]);
            
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
            
        }
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
                    
                    $item->producto["precio"] = $item->producto["precio"]*$factor;
                    $subtotal = ($item->producto["precio"]*$item->cantidad);
                    $iva_val = $item->producto["iva"];
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
            $pedido->total_des = number_format($total_des_ped,2,".",",");
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
            // code...
            return $pedido->makeHidden("items");
            // return $pedido;
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

        return view("reportes.notaentrega",["sucursal"=>$sucursal,"pedido"=>$this->getPedido($req)]);
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

    public function entregadoPendi($fecha)
    {
        $entregado = movimientos_caja::where("created_at","LIKE",$fecha."%")->where("tipo",1);
        $entregado_sum = $entregado->sum("monto");

        $pendiente = movimientos_caja::where("created_at","LIKE",$fecha."%")->where("tipo",0);
        $pendiente_sum = $pendiente->sum("monto");

        return [
            "entregado"=>$entregado_sum,
            "pendiente"=>$pendiente_sum,
            "entre_pend_get"=> movimientos_caja::where("created_at","LIKE",$fecha."%")->get(),
        ];
    }
    public function ultimoCierre($fecha)
    {
        return cierres::where("fecha","<",$fecha)->orderBy("fecha","desc")->first();
    }

    public function getEntreGadoCajainicial($fecha)
    {
        return [
            "entregado" =>$this->entregadoPendi($fecha),
            "ultimo_cierre" =>$this->ultimoCierre($fecha)
        ];
    }
    public function cerrarFun($fecha,$total_caja_neto,$total_punto,$dejar=[],$grafica=false)
    {   
        if (!$fecha) {
            return Response::json(["msj"=>"Error: Fecha inválida","estado"=>false]);

        }
        $entregado_fun = $this->entregadoPendi($fecha);
        $ultimo_cierre = $this->ultimoCierre($fecha);


        $cop = $this->get_moneda()["cop"];
        $bs = $this->get_moneda()["bs"];
            
        $caja_inicial = 0;
        if ($ultimo_cierre) {
            $caja_inicial = round($ultimo_cierre->dejar_dolar + ($ultimo_cierre->dejar_peso/$cop) + ($ultimo_cierre->dejar_bss/$bs),3);
        }
        $pedido = pedidos::where("created_at","LIKE",$fecha."%");

        /////Montos de ganancias
            //Var vueltos_des
            //Var precio
            //Var precio_base
            //Var desc_total
            //Var ganancia
            //Var porcentaje
            $vueltos_des = pago_pedidos::where("tipo",6)->where("monto","<>",0)
            ->whereIn("id_pedido",pedidos::where("created_at","LIKE",$fecha."%")->select("id"))
            ->get()
            ->map(function($q){
                $q->cliente = pedidos::with("cliente")->find($q->id_pedido);
                return $q;
            });

            $inv = items_pedidos::with("producto")->whereIn("id_pedido",pedidos::where("created_at","LIKE",$fecha."%")->where("estado",1)->select("id"))
            ->get()
            ->map(function($q){
                if (isset($q->producto)) {
                    $q->base_tot = $q->producto->precio_base*$q->cantidad;
                }else{
                    $q->base_tot = 0;
                }
                return $q;
            });

            $precio = $inv->sum("monto");
            $precio_base = $inv->sum("base_tot");
            
            $desc_total = items_pedidos::whereIn("id_pedido",pedidos::where("created_at","LIKE",$fecha."%")->where("estado",1)->select("id"))
            ->get()
            ->map(function($q){

                // $q->monto_sin = 0;
                // if ($q->descuento!=0) {
                    $q->monto_sin = ($q->monto)-(($q->descuento/100)*$q->monto);
                // }

                return $q;
            })->sum("monto_sin");

            $credi_total = pago_pedidos::whereIn("id_pedido",pedidos::where("created_at","LIKE",$fecha."%")->where("tipo",4)->select("id"))
            ->get()
            ->sum("monto");

            $desc_total -= $credi_total;
            $precio -= $credi_total;
            
            $ganancia = ($desc_total-$precio_base);

            if ($precio_base==0) {
                $porcentaje = 100; 
            }else{
                $porcentaje = round( (($ganancia*100) / $precio_base),2 ); 
            }
        /////End Montos de ganancias


        $arr_pagos = [
            // "match_cierre" => $match_cierre,
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
        if (!$fechaGetCierre&&!$fechaGetCierre2) {
            $cierres = cierres::orderBy("fecha","desc");
        }else{
            $cierres = cierres::whereBetween("fecha",[$fechaGetCierre,$fechaGetCierre2]);
        }
        
        
        return [
            "cierres"=>$cierres->get(),
            "numventas"=>$cierres->sum("numventas"),
            
            "debito" => number_format($cierres->sum("debito"),2),
            "efectivo" => number_format($cierres->sum("efectivo"),2),
            "transferencia" => number_format($cierres->sum("transferencia"),2),

            "precio" => number_format($cierres->sum("precio"),2),
            "precio_base" => number_format($cierres->sum("precio_base"),2),

            "ganancia" => number_format($cierres->sum("ganancia"),2),
            "porcentaje" => number_format($cierres->avg("porcentaje"),2),
            
        ];
        
    }
    public function cerrar(Request $req)
    {
        return $this->cerrarFun($req->fechaCierre,$req->total_caja_neto,$req->total_punto,["dejar_bs"=>$req->dejar_bs, "dejar_usd"=>$req->dejar_usd, "dejar_cop"=>$req->dejar_cop]);
    }

    public function msj_cuadre($total_entregado,$monto_facturado,$clave,&$arr_pagos,$tolerancia=10)
    {
        if ($monto_facturado) {
            $diff = round($total_entregado-$monto_facturado,3);
            if (($diff>=-1) && ($diff<=$tolerancia)) {
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
            
            $cop = $this->get_moneda()["cop"];
            $bs = $this->get_moneda()["bs"];

            $last_cierre = cierres::orderBy("fecha","desc")->first();
            $check = cierres::where("fecha",$req->fechaCierre)->first();
            
            $fecha_ultimo_cierre = "0";
            if ($last_cierre) {
                $fecha_ultimo_cierre = $last_cierre["fecha"];
            }

            /* $last_debito = $last_cierre->debito;
            $last_efectivo = $last_cierre->efectivo;
            $last_transferencia = $last_cierre->transferencia;
            

            $today = $this->today(); */

            if ($check===null || $fecha_ultimo_cierre==$req->fechaCierre) {
                if ($req->total_punto || $req->efectivo || $req->transferencia) {
                    cierres::updateOrCreate(
                        ["fecha"=>$req->fechaCierre],
                        [
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
                            "id_usuario" => session()->has("id_usuario"),
        
                            "precio" => floatval($req->precio),
                            "precio_base" => floatval($req->precio_base),
                            "ganancia" => floatval($req->ganancia),
                            "porcentaje" => floatval($req->porcentaje),
                            "numventas" => intval($req->numventas),
                            "desc_total" => floatval($req->desc_total),
                            
                        ]
        
                    );
                }else{
                    throw new \Exception("Cierre sin montos.", 1);
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
        $type = $req->type;
        $sucursal = sucursal::all()->first();

        $cierre = cierres::where("fecha",$req->fecha)->first();
        if (!$cierre) {
            return "No hay cierre guardado para esta fecha";
        }

        $total_inventario = DB::table("inventarios")
        ->select(DB::raw("sum(precio*cantidad) as suma"))->first()->suma;
        $vueltos = pago_pedidos::where("tipo",6)->where("monto","<>",0);
        $vueltos_totales = $vueltos->sum("monto");
        
        $pagos_referencias = pagos_referencias::where("created_at","LIKE",$req->fecha."%")->orderBy("banco","desc")->get();
        
        $pagos_referencias_totales = pagos_referencias::where("created_at","LIKE",$req->fecha."%")
        ->selectRaw("SUM(monto) as total,banco")
        ->groupBy('banco')
        ->orderBy("banco","desc")
        ->get();


        

        $movimientos = movimientos::with(["items"=>function($q){
            $q->with("producto");
        }])->where("created_at","LIKE",$req->fecha."%")->get();
        // return $vueltos_des;
        $facturado = $this->cerrarFun($req->fecha,0,0);
        

        $arr_send = [
            "bs" => $this->get_moneda()["bs"],
            "referencias"=>$pagos_referencias,
            "pagos_referencias_totales"=>$pagos_referencias_totales,
            "cierre" => $cierre,
            "cierre_tot" => number_format($cierre->debito+$cierre->efectivo+$cierre->transferencia,2,",","."),
            "total_inventario" =>($total_inventario),
            "total_inventario_format" =>number_format($total_inventario,2,",","."),
            "vueltos_totales" =>number_format($vueltos_totales,2,",","."),
            "vueltos_des" =>$facturado["vueltos_des"],

            "precio"=> number_format($facturado["precio"],2,",","."),
            "precio_base"=> number_format($facturado["precio_base"],2,",","."),
            "ganancia"=> number_format(round($facturado["ganancia"],2),2,",","."),
            "porcentaje"=> number_format($facturado["porcentaje"],2,",","."),
            "desc_total"=> number_format(round($facturado["desc_total"],2),2,",","."),
            "facturado" => $facturado,
            "facturado_tot" => number_format($facturado[2]+$facturado[3]+$facturado[1],2,",","."),
            "sucursal"=>$sucursal,
            "movimientos"=>$movimientos,
        ];

        
        $arr_send["cierre"]["debito"] = number_format($arr_send["cierre"]["debito"],2,",",".");
        $arr_send["cierre"]["efectivo"] = number_format($arr_send["cierre"]["efectivo"],2,",",".");
        $arr_send["cierre"]["transferencia"] = number_format($arr_send["cierre"]["transferencia"],2,",",".");
        $arr_send["cierre"]["dejar_dolar"] = number_format($arr_send["cierre"]["dejar_dolar"],2,",",".");
        $arr_send["cierre"]["dejar_peso"] = number_format($arr_send["cierre"]["dejar_peso"],2,",",".");
        $arr_send["cierre"]["dejar_bss"] = number_format($arr_send["cierre"]["dejar_bss"],2,",",".");
        $arr_send["cierre"]["tasa"] = number_format($arr_send["cierre"]["tasa"],2,",",".");
        $arr_send["cierre"]["efectivo_guardado"] = number_format($arr_send["cierre"]["efectivo_guardado"],2,",",".");
        $arr_send["cierre"]["efectivo_guardado_cop"] = number_format($arr_send["cierre"]["efectivo_guardado_cop"],2,",",".");
        $arr_send["cierre"]["efectivo_guardado_bs"] = number_format($arr_send["cierre"]["efectivo_guardado_bs"],2,",",".");
        $arr_send["facturado"]["total"] = number_format($arr_send["facturado"]["total"],2,",",".");
        $arr_send["facturado"]["caja_inicial"] = number_format($arr_send["facturado"]["caja_inicial"],2,",",".");
        //$arr_send["facturado"]["numventas"] = number_format($arr_send["facturado"]["numventas"],2,",",".");
        $arr_send["facturado"]["entregadomenospend"] = number_format($arr_send["facturado"]["entregadomenospend"],2,",",".");
        $arr_send["facturado"]["entregado"] = number_format($arr_send["facturado"]["entregado"],2,",",".");
        $arr_send["facturado"]["pendiente"] = number_format($arr_send["facturado"]["pendiente"],2,",",".");
        $arr_send["facturado"]["total_caja"] = number_format($arr_send["facturado"]["total_caja"],2,",",".");
        $arr_send["facturado"]["total_punto"] = number_format($arr_send["facturado"]["total_punto"],2,",",".");

        $arr_send["facturado"]["1"] = number_format($arr_send["facturado"]["1"],2,",",".");
        $arr_send["facturado"]["2"] = number_format($arr_send["facturado"]["2"],2,",",".");
        $arr_send["facturado"]["3"] = number_format($arr_send["facturado"]["3"],2,",",".");
        $arr_send["facturado"]["4"] = number_format($arr_send["facturado"]["4"],2,",",".");
        $arr_send["facturado"]["5"] = number_format($arr_send["facturado"]["5"],2,",",".");
        $arr_send["facturado"]["6"] = number_format($arr_send["facturado"]["6"],2,",",".");
        //foreach ($this->letras as $key => $value) {
            $arr_send["total_inventario_format"] = toLetras($arr_send["total_inventario_format"]); 
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

            $arr_send["facturado"]["1"] = toLetras($arr_send["facturado"]["1"]);
            $arr_send["facturado"]["2"] = toLetras($arr_send["facturado"]["2"]);
            $arr_send["facturado"]["3"] = toLetras($arr_send["facturado"]["3"]);
            $arr_send["facturado"]["4"] = toLetras($arr_send["facturado"]["4"]);
            $arr_send["facturado"]["5"] = toLetras($arr_send["facturado"]["5"]);
            $arr_send["facturado"]["6"] = toLetras($arr_send["facturado"]["6"]);
        //}



        



        
        

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
            $subject = $sucursal->sucursal." | CIERRE DIARIO | ".$req->fecha;
            try {
                
                Mail::to($this->sends)->send(new enviarCierre($arr_send,$from1,$from,$subject));    
                
                return Response::json(["msj"=>"Cierre enviado con Éxito","estado"=>true]);
            
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
            
            Mail::to($this->sends)->send(new enviarCuentaspagar([
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
