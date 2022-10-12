<?php

namespace App\Http\Controllers;

use App\Models\lotes;
use Illuminate\Http\Request;
use Response;

class LotesController extends Controller
{
    public function removeLote(Request $req)
    {
          try {
            if ($req->id) {
                $item = lotes::find($req->id)->delete();
            }
            return Response::json(["msj"=>"¡Éxito al eliminar lote!","estado"=>true]);
            
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
        }
    }
}
