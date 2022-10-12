<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\fallas;
use App\Models\movimientos_caja;
use App\Models\sucursal;
use App\Models\moneda;
use App\Models\factura;

use App\Models\inventario;
use App\Models\categorias;
use App\Models\proveedores;
use App\Models\pedidos;


use Http;
use Response;

ini_set('max_execution_time', 300);
class sendCentral extends Controller
{
    
    public function path()
    {
        return "";
    }
    public function index()
    {
        return view("central.index");
    }
    // public function update($new_version)
    // {
    //     $runproduction = "npm run production";        
    //     // $phpArtisan = "php artisan key:generate && php artisan view:cache && php artisan route:cache && php artisan config:cache";

    //     $pull = shell_exec("cd C:\sinapsisfacturacion && git stash && git pull https://github.com/alvaritojose2712/sinapsisfacturacion.git && composer install --optimize-autoloader --no-dev");

    //     if (!str_contains($pull, "Already up to date")) {
    //         echo "Éxito al Pull. Building...";
    //         exec("cd C:\sinapsisfacturacion && ".$runproduction." && ".$phpArtisan,$output, $retval);

    //         if (!$retval) {
    //             echo "Éxito al Build. Actualizado...";

    //             sucursal::update(["app_version",$new_version]);
    //         }
    //     }else{
    //         echo "Pull al día. No requiere actualizar <br>";
    //         echo "<pre>$pull</pre>";

    //     }
    // }


    //req
    public function getip()
    {
        return getHostByName(getHostName());
    }
    public function getmastermachine()
    {
        return ["192.168.0.103:8001","192.168.0.102:8001","127.0.0.1:8001"];
    }
    public function changeExportStatus($pathcentral,$id)
    {
        $response = Http::post("http://".$pathcentral.":9090/setexportpedido",["id"=>$id]);
    }
    public function reqinventario(Request $req)
    {
        
        $response = Http::post("http://".$req->path.":9090/resinventario");
        
        if ($response->ok()) {
            $res = $response->json();
            if (isset($res["categorias"])) {
                foreach ($res["categorias"] as $e) {
                    categorias::updateOrCreate(
                    [
                        "id"=>$e["id"]
                    ],[
                        "descripcion"=>$e["descripcion"],
                    ]);
                }

            }
            if (isset($res["proveedores"])) {
                foreach ($res["proveedores"] as $e) {
                    proveedores::updateOrCreate(
                        [
                            "id" => $e["id"],
                        ],[
                            "descripcion" => $e["descripcion"],
                            "rif" => $e["rif"],
                            "direccion" => $e["direccion"],
                            "telefono" => $e["telefono"],
                        ]
                    );
                }
            }

            if (isset($res["inventario"])) {
                foreach ($res["inventario"] as $e) {
                    $insertOrUpdateInv = inventario::updateOrCreate([
                        "id" => $e["id"]
                    ],[
                        "codigo_barras" => $e["codigo_barras"],
                        "codigo_proveedor" => $e["codigo_proveedor"],
                        "unidad" => $e["unidad"],
                        "id_categoria" => $e["id_categoria"],
                        "descripcion" => $e["descripcion"],
                        "precio_base" => $e["precio_base"],
                        "precio" => $e["precio"],
                        "iva" => $e["iva"],
                        "id_proveedor" => $e["id_proveedor"],
                        "id_marca" => $e["id_marca"],
                        "id_deposito" => $e["id_deposito"],
                        "porcentaje_ganancia" => $e["porcentaje_ganancia"]
                    ]);    
                }
            }
            
            
        }else{
            
            return "Error: ".$response->body();
        }   
    }
    public function reqpedidos(Request $req)
    {   
        try {
            $sucursal = sucursal::all()->first();

            $response = Http::post("http://".$req->path.':9090/respedidos');

            if ($response->ok()) {
                $res = $response->json();
                if ($res["pedido"]) {
                    return $res["pedido"];
                }
            }else{
                return "Error: ".$response->body();

            }
            
        } catch (\Exception $e) {
            return Response::json(["estado"=>false,"msj"=>"Error de sucursal: ".$e->getMessage()]);
        }
    }

    //res
    public function respedidos(Request $req)
    {
        $ped = pedidos::with(["items"=>function($q){
            $q->with("producto");
        }])
        ->where("estado",1)
        ->where("export",1)
        ->orderBy("id","desc")
        ->get()
        ->map(function($q){
            $q->base = $q->items->map(function($q){
                return $q->producto->precio_base*$q->cantidad;
            })->sum();
            $q->venta = $q->items->sum("monto");
            return $q;

        });

        if ($ped) {
            return Response::json([
                "msj"=>"Tenemos algo :D",
                "pedido"=>$ped,
                "estado"=>true
            ]);
        }else{
            return Response::json([
                "msj"=>"No hay pedidos pendientes :(",
                "estado"=>false
            ]);
        }
    }
    public function resinventario(Request $req)
    {
        //return "exportinventario";
        return [
            "inventario"=>inventario::all(),
            "categorias" => categorias::all(),
            "proveedores" => proveedores::all(), 
        ];
    }


    

    public function updateApp()
    {   
        try {
            
            $sucursal = sucursal::all()->first();
            $actually_version = $sucursal["app_version"];

            $getVersion = Http::get($this->path."/getVersionRemote");

            if ($getVersion->ok()) {

                $server_version = $getVersion->json();
                if ($actually_version!=$server_version) {
                    $this->update($server_version);
                }else if($actually_version==$server_version){
                    return "Sistema al día :)";
                }else{
                    return "Upps.. :("."V-Actual=".$actually_version." V-Remote".$server_version;

                };
            }
        } catch (\Exception $e) {
            return "Error: ".$e->getMessage();
        }

    }
    

    public function getInventarioCentral()
    {
        try {
            $sucursal = sucursal::all()->first();
            $response = Http::post($this->path.'/getInventario', [
                "sucursal_code"=>$sucursal->codigo,

            ]);
        } catch (\Exception $e) {
            return Response::json(["estado"=>false,"msj"=>"Error de sucursal: ".$e->getMessage()]);
            
        }
        
    }

    public function setGastos()
    {
        try {
            $sucursal = sucursal::all()->first();
            $movimientos_caja = movimientos_caja::all();

            if (!$movimientos_caja->count()) {
                return Response::json(["msj"=>"Nada que enviar","estado"=>false]);
            }


            $response = Http::post($this->path.'/setGastos', [
                "sucursal_code"=>$sucursal->codigo,
                "movimientos_caja"=>$movimientos_caja
            ]);

            //ids_ok => id de movimiento 

            if ($response->ok()) {
                $res = $response->json();
                if ($res["estado"]) {
                    return $res["msj"];
                }
            }else{
                return $response->body();
            }   
        } catch (\Exception $e) {
            return Response::json(["estado"=>false,"msj"=>"Error de sucursal: ".$e->getMessage()]);
            
        }
    }
    public function setFacturasCentral()
    {
        try {
            $sucursal = sucursal::all()->first();
            $facturas = factura::with(["proveedor","items"=>function($q){
                $q->with("producto");
            }])
            ->where("push",0)->get();


            if (!$facturas->count()) {
                return Response::json(["msj"=>"Nada que enviar","estado"=>false]);
            }


            $response = Http::post($this->path.'/setConfirmFacturas', [
                "sucursal_code"=>$sucursal->codigo,
                "facturas"=>$facturas
            ]);

            //ids_ok => id de movimiento 

            if ($response->ok()) {
                $res = $response->json();
                if (isset($res["estado"])) {
                    if ($res["estado"]) {
                        factura::where("push",0)->update(["push"=>1]);
                        return $res["msj"];
                    }

                }else{

                    return $response;
                }
            }else{
                return $response->body();
            }   
        } catch (\Exception $e) {
            return Response::json(["estado"=>false,"msj"=>"Error de sucursal: ".$e->getMessage()]);
            
        }
    }
    public function setCentralData()
    {
        try {
            $sucursal = sucursal::all()->first();
            $fallas = fallas::all();

            if (!$fallas->count()) {
                return Response::json(["msj"=>"Nada que enviar","estado"=>false]);
            }


            $response = Http::post($this->path.'/setFalla', [
                "sucursal_code"=>$sucursal->codigo,
                "fallas"=>$fallas
            ]);

            //ids_ok => id de productos 

            if ($response->ok()) {
                $res = $response->json();
                // code...

                if ($res["estado"]) {

                    return $res["msj"];
                }
            }else{
                
                return $response;
            }            
        } catch (\Exception $e) {
            return Response::json(["estado"=>false,"msj"=>"Error de sucursal: ".$e->getMessage()]);
            
        }


    }

    public function setVentas()
    {
        try {
            $PedidosController = new PedidosController; 
            $sucursal = sucursal::all()->first();
            $fecha = $PedidosController->today();
            $bs = $PedidosController->get_moneda()["bs"];

            $cierre_fun = $PedidosController->cerrarFun($fecha,0,0);

                  // 1 Transferencia
                   // 2 Debito 
                   // 3 Efectivo 
                   // 4 Credito  
                   // 5 Otros
                   // 6 vuelto

            $ventas = [
                "debito"=> $cierre_fun[2],
                "efectivo"=>$cierre_fun[3],
                "transferencia"=> $cierre_fun[1],
                "tasa"=>$bs,
                "fecha"=>$cierre_fun["fecha"],
                "num_ventas"=>$cierre_fun["numventas"],
            ];


            $response = Http::post($this->path.'/setVentas', [
                "sucursal_code"=>$sucursal->codigo,
                "ventas"=>$ventas
            ]);

            //ids_ok => id de movimiento 

            if ($response->ok()) {
                $res = $response->json();
                if ($res["estado"]) {
                   return $res["msj"];
                }
            }else{
                return $response->body();
            }            
        } catch (\Exception $e) {
            return Response::json(["estado"=>false,"msj"=>"Error de sucursal: ".$e->getMessage()]);
            
        }

    }
   

    public function getMonedaCentral()
    {   

        try {
            $res = Http::get($this->path."/getMoneda");

            if ($res->ok()) {
                $moneda_get = $res->json();
                
                $cop = moneda::where("tipo",2)->orderBy("id","desc")->first();
                $bs = moneda::where("tipo",1)->orderBy("id","desc")->first();
                
                if ($moneda_get["bs"]) {
                    if ($moneda_get["bs"]!=$bs["valor"]) {
                        moneda::updateOrCreate(["tipo"=>1],["valor"=>$moneda_get["bs"]]);
                        // code...
                    }
                }
                if ($moneda_get["cop"]) {
                    if ($moneda_get["cop"]!=$cop["valor"]) {
                        moneda::updateOrCreate(["tipo"=>2],["valor"=>$moneda_get["cop"]]);
                    }
                }
            }else{

            }
            
        } catch (\Exception $e) {
            return Response::json(["estado"=>false,"msj"=>"Error de sucursal: Conexión rechazada"]);
        }
    }

    public function sendInventario()
    {
        try {
            $inventario = InventarioController::all(); 
            


            $response = Http::post($this->path.'/sendInventario', [
                "sucursal_code"=>$sucursal->codigo,
                "inventario"=>$inventario
            ]);

            //ids_ok => id de movimiento 

            if ($response->ok()) {
                $res = $response->json();
                if ($res["estado"]) {
                   return $res["msj"];
                }
            }else{
                return $response->body();
            }            
        } catch (\Exception $e) {
            return Response::json(["estado"=>false,"msj"=>"Error de sucursal: ".$e->getMessage()]);
            
        }
    }

}
