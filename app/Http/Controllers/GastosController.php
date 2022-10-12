<?php

namespace App\Http\Controllers;

use App\Models\gastos;
use Illuminate\Http\Request;
use Response;

class GastosController extends Controller
{
    public function delGastos(Request $req)
    {
         try {
            $id = $req->id;
            if ($id) {
                gastos::find($id)->delete();
            }
            return Response::json(["msj"=>"Éxito al eliminar","estado"=>true]);
            
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
            
        }
    }
    public function getGastos(Request $req)
    {
        $buscar = $req->qgastos;
        $qgastosfecha1 = $req->qgastosfecha1;
        $qgastosfecha2 = $req->qgastosfecha2;
        $qcatgastos = $req->qcatgastos;
        $gasto = gastos::where(function($q) use ($buscar,$qcatgastos)
        {   
            $q->orwhere("descripcion","LIKE",$buscar."%");
            $q->orwhere("monto","LIKE",$buscar."%");
        })
        ->where(function($q) use ($buscar,$qcatgastos)
        {   
            if ($qcatgastos) {
                $q->where("categoria",$qcatgastos); 
            }
        })
        ->whereBetween("fecha",[$qgastosfecha1,$qgastosfecha2]);
        
        
        return ["gastos"=>$gasto->get(),"num"=>$gasto->count(),"sum"=>$gasto->sum("monto")];
    }
    public function setGasto(Request $req)
    {
         try {
            $obj = new gastos;
            $obj->descripcion = $req->gastosdescripcion;
            $obj->categoria = $req->gastoscategoria;
            $obj->monto = $req->gastosmonto;
            $obj->fecha = (new PedidosController)->today();
            
            $obj->save();
            return Response::json(["msj"=>"¡Éxito!","estado"=>true]);
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
        }
    }
}
