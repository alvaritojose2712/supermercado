<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Response;

class admin
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

        if (session('tipo_usuario') == 1) {
            return $next($request);
        }else{
            return Response::json(["msj"=>"Error: Sin permisos","estado"=>false]);
        }
    }
}
