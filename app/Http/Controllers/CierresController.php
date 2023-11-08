<?php

namespace App\Http\Controllers;

use App\Models\cierres;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CierresController extends Controller
{
    function getLastCierre() {
        $seconds = 3600;
        
        if (Cache::has('lastcierres')) {
            return Cache::get('lastcierres');
        }else{
            return Cache::remember('lastcierres', $seconds, function () {
                return cierres::orderBy("fecha","desc")->first();
            });
        }
    }
    public function getStatusCierre(Request $req)
    {
        $today = (new PedidosController)->today();

        $tipo_accion = cierres::where("fecha",$today)->where("id_usuario",session("id_usuario"))->first();
        if ($tipo_accion) {
            $tipo_accion = "editar"; 
        }else{
            $tipo_accion = "guardar"; 

        }

        return ["tipo_accionCierre"=>$tipo_accion];
    }
    public function getTotalizarCierre(Request $req)
    {   
        $bs = (new PedidosController)->get_moneda()["bs"];

        $today = (new PedidosController)->today();
        $c = cierres::where("tipo_cierre",0)->where("fecha",$today)->get();

        return [
            "caja_usd" => $c->sum("efectivo_actual"),
            "caja_cop" => $c->sum("efectivo_actual_cop"),
            "caja_bs" => $c->sum("efectivo_actual_bs"),
            "caja_punto" => $c->sum("puntodeventa_actual_bs"),
            "dejar_dolar" => $c->sum("dejar_dolar"),
            "dejar_peso" => $c->sum("dejar_peso"),
            "dejar_bss" => $c->sum("dejar_bss"),
            "caja_biopago" => $c->sum("caja_biopago")*$bs,
        ];
    }
}
