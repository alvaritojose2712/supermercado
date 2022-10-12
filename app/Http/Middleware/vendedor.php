<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Response;


class vendedor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $se = session('tipo_usuario');

         if ($se == 1 || 
         $se == 3 ||
         $se == 4) {
             return $next($request);
            }else{
            return Response::json(["msj"=>"Error: Sin permisos","estado"=>false]);
        }
    }
}
