<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\inventario;

use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class tickeprecioController extends Controller
{   
    
    public function tickedPrecio(Request $req)
    {
        $id = $req->id;
        $inventario = inventario::where("id",$id)->get()->first();

        $descripcion = $inventario->descripcion;
        $codigo_barras = $inventario->codigo_barras;
        $pu = number_format($inventario->precio,2,".",","); 

        return view("reportes.tickedprecio",[
            "descripcion" => $descripcion,
            "codigo_barras" => $codigo_barras,
            "pu" => $pu,
        ]);

    }
}
