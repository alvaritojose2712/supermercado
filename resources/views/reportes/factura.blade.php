<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.ico') }}">
    <title>Reporte de Factura </title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
   
</head>
<body>
    <section class="container-fluid">
			<table class="table">
				<thead>
					<tr class="text-center">
						<td colspan="7">
							<img src="{{ asset('images/logo.png') }}" width="200px">
						</td>
					</tr>
				</thead>
				<tbody>
					<tr class="text-center">
						<td colspan="3">
							<h5>{{$sucursal->nombre_registro}}</h5>
							<b>RIF. {{$sucursal->rif}}</b>
							<br>
							<span>
								Domicilio Fiscal: {{$sucursal->direccion_registro}}
							</span>
						</td>
						<td colspan="3">
							<b>Código {{$sucursal->codigo}}</b>
							<hr>
							<span>
								<span class="h3">{{$sucursal->sucursal}}</span>
							</span>
						</td>
					</tr>
					<tr>
						<th colspan="6" class="text-right"><h5>Fecha y hora de emisión</h5> {{$factura->created_at}}</th>
					</tr>
					<tr>
						<th colspan="" class="">Factura Importada</th>
						<th colspan="2" class="text-right text-danger"><h5>ID° {{sprintf("%08d", $factura->id)}}</h5></th>
						<th colspan="3" class="text-right text-danger"><h5>N° {{sprintf("%08d", $factura->numfact)}}</h5></th>
					</tr>
					<tr>
						<th colspan="" class="text-right">Proveedor</th>
						<td colspan="" class="text-left">{{ $factura->proveedor->descripcion }}</td>
						<th colspan="" class="text-right">N° RIF/C.I./Pasaporte</th>
						<td colspan="4" class="text-left">{{ $factura->proveedor->rif }}</td>
					</tr>
					<tr>
						<th colspan="" class="text-right">Domicilio Fiscal</th>
						<td colspan="" class="text-left">{{ $factura->proveedor->direccion }}</td>
						<th colspan="" class="text-right">Num. Items</th>
						<td colspan="3" class="text-left">{{ count($factura->items) }}</td>
					</tr>

					<tr class="tr-secondary">
						<th>
							<small class="text-muted">Tipo de Movimiento</small>		
						</th>
						<th>
							Código
						</th>
						<th>
							Descripción
						</th>
						<th>
							Cantidad
						</th>
						<th >
							P/U
						</th>
						<th class="text-right">
							Sub-Total
						</th>

					</tr>
					@foreach ($factura->items as $val)
						<tr class="tr-secondary">
							<td class="align-middle">{{$val->tipo}}</td>
							<td class="align-middle">
								<small class="text-muted">{{$val->producto->codigo_proveedor}}</small><br/>
								<small class="text-muted">{{$val->producto->codigo_barras}}</small>
							</td>
							<td class="align-middle">
								{{$val->producto->descripcion}}
							</td>
							<td class="align-middle">
								{{$val->cantidad}}
							</td>
							<td class="align-middle">
								{{$val->producto->precio}}
							</td>
							<td class="align-middle text-right">
								{{$val->subtotal}}
							</td>
						</tr>
					@endforeach

					<tr>
						<td class="text-right" colspan="5">Total:</td>
						<th class="text-right" colspan="">{{$factura->monto}}</th>
					</tr>
				</tbody>

			</table>
        
    </section> 
    
    
</body>
</html>
