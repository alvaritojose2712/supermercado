<?php

namespace App\Http\Controllers;

use App\Models\home;
use App\Models\usuarios;
use App\Models\sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Response;
use Session;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $su = sucursal::all()->first();
        if ($su) {
            return view("facturar.index");
        }else{

            return view("sucursal.crear",["sucursal"=>$su]);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    public function selectRedirect()
    {
      $selectRedirect = "/";
        switch(session("tipo_usuario")){
            case 1:
                $selectRedirect = '/admin';
                break;
            case 2:
                $selectRedirect = '/cajero';
                break;
            default:
                $selectRedirect = '/login';
        }
      return $selectRedirect;
         
        // return $next($request);
    } 
    public function verificarLogin(Request $req)
    {
        if (session()->has("id_usuario")) {
            return Response::json( ["estado"=>true] );
        }else{
            return Response::json( ["estado"=>false] );
        }
    }
    public function logout(Request $request)
    {
        $request->session()->flush();

    }
    public function role($tipo)
    {
        switch ($tipo) {
            case '1':
                return "Administrador";
                break;
            case '2':
                return "Cajero";
                break;
            case '3':
                return "Vendedor";
                break;
            case '4':
                return "Cajero Vendedor";
                break;
            
            default:
                # code...
                break;
        }
    }
    public function nivel($tipo)
    {
        if ($tipo == 1) {
            return 1;
            //Admin
        }
        
        if ($tipo == 1 || 
        $tipo == 2 ||
        $tipo == 4) {
            //Caja
            return 2;
        }
        
        if ($tipo == 1 || 
        $tipo == 3 ||
        $tipo == 4) {
            //Vendedor
            return 3;
        }
    }
    public function login(Request $req)
    {
        try {

            $d = usuarios::where(function($query) use ($req){
                $query->orWhere('usuario', $req->usuario);
            })
            ->first();
        

            $sucursal = sucursal::all()->first();
            
            if ($d&&Hash::check($req->clave, $d->clave)) {
                $arr_session =  [
                    "id_usuario" => $d->id,
                    "tipo_usuario" => $d->tipo_usuario,
                    "nivel" => $this->nivel($d->tipo_usuario),
                    "role" => $this->role($d->tipo_usuario),
                    "usuario" => $d->usuario,
                    "nombre" => $d->nombre,
                    "sucursal" => $sucursal->codigo,
                    "iscentral" => $sucursal->iscentral,
                ];
                session($arr_session);
                $estado = $this->selectRedirect();
            }else{
                throw new \Exception("¡Datos Incorrectos!", 1);
                
            } 
            
            return Response::json( ["user"=>$arr_session,"estado"=>true,"msj"=>"¡Inicio exitoso! Bienvenido/a, ".$d->nombre] );
        } catch (\Exception $e) {
            return Response::json(["msj"=>"Error: ".$e->getMessage(),"estado"=>false]);
        }
       
    }

    public function closeAllSession(Request $request)
    {
        //Session::flush();
    }
}
