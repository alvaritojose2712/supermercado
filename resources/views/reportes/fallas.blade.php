<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.ico') }}">
    <title>Solicitud de Cotización</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
   
</head>
<body>
    <section class="container-fluid">
			<table class="table">
				<thead>
					<tr class="text-center">
						<td colspan="6">
							<img src="{{ asset('images/logo.png') }}" width="200px">
						</td>
					</tr>
				</thead>
				<tbody>
					<tr class="text-center">
						<td colspan="2">
							<h5>{{$sucursal->nombre_registro}}</h5>
						</td>
						<td colspan="4">
							<b>RIF. {{$sucursal->rif}}</b>
							<hr>
							<span>
								Domicilio Fiscal: {{$sucursal->direccion_registro}}
							</span>
						</td>
					</tr>
					<tr>
						<th colspan="2" class=""><h5>SUCURSAL</h5> {{$sucursal->sucursal}}</th>
						<th colspan="4" class="text-right"><h5>Fecha y hora de emisión</h5> {{date("Y-m-d h:i:s")}}</th>
					</tr>
					<tr>
						<th colspan="2" class="text-center"><h1>Solicitud de Cotización</h1></th>
						<th colspan="4" class="text-right text-danger"><h5>N° {{sprintf("%08d", $proveedor->id)}}</h5></th>
					</tr>
					<tr>
						<th colspan="" class="text-right">Razón Social</th>
						<td colspan="" class="text-left">{{ $proveedor->descripcion }}</td>
						<th colspan="" class="text-right">RIF</th>
						<td colspan="3" class="text-left">{{ $proveedor->rif }}</td>
					</tr>

					<tr class="tr-secondary">
						<th>
							Código Alterno
						</th>
                        <th>
							Código Barras
						</th>
						<th>
							Descripción
						</th>
						<th>
							Cantidad
						</th>
					</tr>
					@foreach ($fallas as $val)
						<tr class="tr-secondary hover" title="Eliminar" onClick="this.remove()">
							<td>
								{{$val->producto->codigo_proveedor}}
							</td>
                            <td>
								{{$val->producto->codigo_barras}}
							</td>

							<td>
								{{$val->producto->descripcion}}
							</td>
							<td>
								{{$val->producto->cantidad}}
							</td>
							
						</tr>
					@endforeach
				</tbody>

			</table>
        
    </section> 
    
    
</body>
</html>
