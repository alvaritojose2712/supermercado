<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('images/icon.ico') }}">
    <title>sinapsis</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/table/table.css') }}" rel="stylesheet">
   
</head>
<body>
   
    <div class="container">
        <div class="row">
            <div class="col d-flex justify-content-center">
                <img src="{{asset('images/logo.png')}}" alt="" class="logo">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <form action="{{route('setSucursal')}}" method="">
  <div class="form-group">
    <label for="sucursal">Empresa</label>
    <input type="text" required name="sucursal" value="{{$sucursal?$sucursal->sucursal:null}}" class="form-control" id="sucursal" placeholder="Nombre de la Empresa">
    <small id="emailHelp" class="form-text text-muted">Ejemplo: Mantecal</small>
  </div>
  <div class="form-group">
    <label for="codsucursal">Código</label>
    <input type="text" required name="codigo" value="{{$sucursal?$sucursal->codigo:null}}" class="form-control" placeholder="Código">
    <small id="textHelp" class="form-text text-muted">Ejemplo: CODE</small>
  </div>

  <div class="form-group">
    <label for="codsucursal">Nombre de registro</label>
    <input type="text" required name="nombre_registro" value="{{$sucursal?$sucursal->nombre_registro:null}}" class="form-control" placeholder="Nombre de registro">
    <small id="textHelp" class="form-text text-muted"></small>
  </div>

  <div class="form-group">
    <label for="codsucursal">RIF</label>
    <input type="text" required name="rif" value="{{$sucursal?$sucursal->rif:null}}" class="form-control" placeholder="Identificación">
    <small id="textHelp" class="form-text text-muted"></small>
  </div>

  <div class="form-group">
    <label for="sucursal">Dirección de Registro</label>
    <input type="text" required name="direccion_registro" value="{{$sucursal?$sucursal->direccion_registro:null}}" class="form-control">
    <small id="textHelp" class="form-text text-muted"></small>
  </div>
  <div class="form-group">
    <label for="sucursal">Dirección</label>
    <input type="text" required name="direccion_sucursal" value="{{$sucursal?$sucursal->direccion_sucursal:null}}" class="form-control">
    <small id="textHelp" class="form-text text-muted"></small>
  </div>
  <div class="form-group">
    <label for="sucursal">Teléfono 1</label>
    <input type="text" required name="telefono1" value="{{$sucursal?$sucursal->telefono1:null}}" class="form-control">
    <small id="emailHelp" class="form-text text-muted"></small>
  </div>
  <div class="form-group">
    <label for="sucursal">Teléfono 2</label>
    <input type="text" required name="telefono2" value="{{$sucursal?$sucursal->telefono2:null}}" class="form-control">
    <small id="emailHelp" class="form-text text-muted"></small>
  </div>

  <div class="form-group">
    <label for="codsucursal">Correo</label>
    <input type="text" required name="correo" value="{{$sucursal?$sucursal->correo:null}}" class="form-control" placeholder="Correo">
    <small id="textHelp" class="form-text text-muted"></small>
  </div>


  <div class="form-group">
    <label for="sucursal">Tickera</label>
    <input type="text" required name="tickera" value="{{$sucursal?$sucursal->tickera:null}}" class="form-control">
    <small id="emailHelp" class="form-text text-muted"></small>
  </div>

<div class="form-group">
    <label for="sucursal">Máquina Fiscal</label>
    <input type="text" required name="fiscal" value="{{$sucursal?$sucursal->fiscal:null}}" class="form-control">
    <small id="emailHelp" class="form-text text-muted"></small>
  </div>

  

  
  <button type="submit" class="btn btn-sinapsis">Enviar</button>
</form>

            </div>
        </div>
    </div>
    
    
</body>
</html>
