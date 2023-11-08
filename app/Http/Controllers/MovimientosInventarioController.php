<?php

namespace App\Http\Controllers;

use App\Models\movimientosInventario;
use App\Http\Requests\StoremovimientosInventarioRequest;
use App\Http\Requests\UpdatemovimientosInventarioRequest;
use Illuminate\Http\Request;


class MovimientosInventarioController extends Controller
{
    public function getHistoricoInventario(Request $req)
    {
        $fecha1histoinven = $req->fecha1histoinven?$req->fecha1histoinven:"";
        $fecha2histoinven = $req->fecha2histoinven?$req->fecha2histoinven:"";
        $usuarioHistoInven = $req->usuarioHistoInven?$req->usuarioHistoInven:"";
        $orderByHistoInven = $req->orderByHistoInven?$req->orderByHistoInven:"";
        $qhistoinven = $req->qhistoinven?$req->qhistoinven:"";

        $mov = movimientosInventario::with(["usuario"=>function($q){
            $q->select(["id","nombre","usuario"]);
        }])->whereBetween("created_at",["$fecha1histoinven 00:00:01","$fecha2histoinven 23:59:59"])
        ->when(!empty($usuarioHistoInven),function($q) use ($usuarioHistoInven){
            $q->where("id_usuario", $usuarioHistoInven);
        })
        ->when(!empty($qhistoinven),function($q) use ($qhistoinven){

            $q->whereIn("id_producto",function($qq) use ($qhistoinven){

                $qq->from("inventarios")->where(function($qqq) use ($qhistoinven){
                    $qqq->orWhere("descripcion","LIKE","%$qhistoinven%")
                    ->orWhere("codigo_proveedor","LIKE","%$qhistoinven%")
                    ->orWhere("codigo_barras","LIKE","%$qhistoinven%");
                })->select("id");
            });

        })
        ->orderBy("created_at",$orderByHistoInven)
        ->get()
        ->map(function($q){
            $q->despues = json_decode($q->despues);
            $q->antes = $q->antes?json_decode($q->antes):$q->antes;
            return $q;
        });

        return $mov;
    }

    public function newMovimientosInventario($arr)
    {
        $movimientoinventario = new movimientosInventario;
        $movimientoinventario->antes = $arr["antes"]; 
        $movimientoinventario->despues = $arr["despues"];
        $movimientoinventario->id_usuario = $arr["id_usuario"]; 
        $movimientoinventario->id_producto = $arr["id_producto"];
        $movimientoinventario->origen = $arr["origen"];                 
        $movimientoinventario->save();
    }
    
}
