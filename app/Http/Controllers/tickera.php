<?php

namespace App\Http\Controllers;

use App\Models\sucursal;


use Illuminate\Http\Request;
use Mike42\Escpos;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintBuffers\ImagePrintBuffer;
use Mike42\Escpos\CapabilityProfiles\DefaultCapabilityProfile;
use Mike42\Escpos\CapabilityProfiles\SimpleCapabilityProfile;
use Response;

class tickera extends Controller
{
    public function imprimir(Request $req)
    {

        //return gethostname();
        function addSpaces($string = '', $valid_string_length = 0) {
            if (strlen($string) < $valid_string_length) {
                $spaces = $valid_string_length - strlen($string);
                for ($index1 = 1; $index1 <= $spaces; $index1++) {
                    $string = $string . ' ';
                }
            }

            return $string;
        }
        
        $get_moneda = (new PedidosController)->get_moneda();
        $moneda_req = $req->moneda;
        //$
        //bs
        //cop
        if ($moneda_req=="$") {
          $dolar = 1;
        }else if($moneda_req=="bs"){
          $dolar = $get_moneda["bs"];
        }else if($moneda_req=="cop"){
          $dolar = $get_moneda["cop"];
        }else{
          $dolar = $get_moneda["bs"];
        }

        $pedido = (new PedidosController)->getPedido($req,floatval($dolar));
        $sucursal = sucursal::all()->first();
        $fecha_emision = date("Y-m-d H:i:s");

        try {
            $arr_printers = explode(";", $sucursal->tickera);
            $printer = 1;

            if ($req->printer) {
                $printer = $req->printer-1;
            }
            
            $connector = new WindowsPrintConnector($arr_printers[$printer]);//smb://computer/printer
            $printer = new Printer($connector);
            $printer->setEmphasis(true);

                $nombres = $pedido["cliente"]["nombre"];
                $identificacion = $pedido["cliente"]["identificacion"];
            

            if ($nombres=="precio" && $identificacion=="precio") {
                if($pedido->items){

                    foreach ($pedido->items as $val) {

                        if (!$val->producto) {
                            $items[] = [
                                'descripcion' => $val->abono,
                                'codigo_barras' => 0,
                                'pu' => $val->monto,
                                'cantidad' => $val->cantidad,
                                'totalprecio' => $val->total,
                               
                            ];
                        }else{

                            $items[] = [
                                'descripcion' => $val->producto->descripcion,
                                'codigo_barras' => $val->producto->codigo_barras,
                                'pu' => $val->producto->precio,
                                'cantidad' => $val->cantidad,
                                'totalprecio' => $val->total,
                               
                            ];
                        }
                    }
                }
                $printer->setJustification(Printer::JUSTIFY_CENTER);
               
                foreach ($items as $item) {

                    //Current item ROW 1

                    $printer->setEmphasis(true);
                    $printer->text("\n");
                    $printer->text($item['codigo_barras']);
                    $printer->setEmphasis(false);
                   $printer->text("\n");
                   $printer->text($item['descripcion']);
                   $printer->text("\n");

                    $printer->setEmphasis(true);

                   $printer->text($item['pu']);
                   $printer->setEmphasis(false);
                   
                   $printer->text("\n");

                    $printer->feed();
                }
            }else{

                
               $printer->setJustification(Printer::JUSTIFY_CENTER);

                // $tux = EscposImage::load(resource_path() . "/images/logo-small.jpg", false);
                // $printer -> bitImage($tux);
                // $printer->setEmphasis(true);

                // $printer->text("\n");
                $printer->setJustification(Printer::JUSTIFY_CENTER);

                $printer -> text("\n");
                $printer -> text($sucursal->nombre_registro);
                $printer -> text("\n");
                $printer -> text($sucursal->rif);
                $printer -> text("\n");
                $printer -> text($sucursal->telefono1." | ORDEN #".$pedido->id);
                $printer -> text("\n");

                $printer -> setTextSize(1,1);



                $printer -> text("\n");

                if ($nombres!="") {
                    $printer->setJustification(Printer::JUSTIFY_LEFT);
                    $printer -> text("Nombres: ".$nombres);
                    $printer -> text("\n");
                    $printer -> text("ID: ".$identificacion);
                    $printer -> text("\n");
                    $printer->setJustification(Printer::JUSTIFY_LEFT);

                    // $printer -> text("Teléfono: ".$tel);
                    // $printer -> text("\n");
                    // $printer->setJustification(Printer::JUSTIFY_LEFT);

                    // $printer -> text("Dirección: ".$dir);
                    // $printer -> text("\n");
                    // $printer->setJustification(Printer::JUSTIFY_LEFT);


                }



                
                $printer->feed();
                $printer->setPrintLeftMargin(0);
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->setEmphasis(true);
                $printer->setEmphasis(false);
                $items = [];
                $monto_total = 0;

                if($pedido->items){

                    foreach ($pedido->items as $val) {

                        if (!$val->producto) {
                            $items[] = [
                                'descripcion' => $val->abono,
                                'codigo_barras' => 0,
                                'pu' => $val->monto,
                                'cantidad' => $val->cantidad,
                                'totalprecio' => $val->total,
                               
                            ];
                        }else{

                            $items[] = [
                                'descripcion' => $val->producto->descripcion,
                                'codigo_barras' => $val->producto->codigo_barras,
                                'pu' => ($val->descuento<0)?number_format($val->producto->precio-$val->des_unitario,3):$val->producto->precio,
                                'cantidad' => $val->cantidad,
                                'totalprecio' => $val->total,
                            ];
                        }
                    }
                }
               
                foreach ($items as $item) {

                    //Current item ROW 1
                   $printer->text($item['descripcion']);
                   $printer->text("\n");
                   $printer->text($item['codigo_barras']);
                   $printer->text("\n");

                   $printer->setEmphasis(true);
                   $printer->text(addSpaces("Ct ".$item['cantidad'],9));
                   $printer->setEmphasis(false);

                    if ($req->printprecio) {
                       $printer->text(addSpaces("PU ".$item['pu'],11));
                       $printer->text(addSpaces("To ".$item['totalprecio'],12));
                    }else{

                    }

                   $printer->text("\n");



                    $printer->feed();
                }
                $printer->setEmphasis(true);


                if ($req->printprecio) {
                    $printer->text("Desc: ".$pedido->total_des);
                    $printer->text("\n");
                    $printer->text("Sub-Total: ". number_format($pedido->clean_total/1.16,2) );
                    $printer->text("\n");
                    $printer->text("Monto IVA 16%: ".number_format($pedido->clean_total*.16,2));
                    $printer->text("\n");
                    $printer->text("Total: ".$pedido->total);
                    $printer->text("\n");
                    $printer->text("\n");
                }else{
                    
                }
                $printer->setJustification(Printer::JUSTIFY_CENTER);

                $printer->text("Creado: ".$pedido->created_at);
                
                $printer->text("\n");
                $printer->text("\n");

               

                


            }


            

            $printer->cut();
            $printer->pulse();
            $printer->close();

          return Response::json(["msj"=>"Imprimiendo...","estado",true]);

        } catch (Exception $e) {
          return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado",false]);
            
        }
    }
}
