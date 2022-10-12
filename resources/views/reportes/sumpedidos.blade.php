<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.ico') }}">
    <title>Reporte Unir Pedidos</title>

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
						<th colspan="2" class=""><h5>SUCURSAL</h5> {{env("SUCURSAL_DIRECCION")}}</th>
						<th colspan="4" class="text-right"><h5>Fecha y hora de emisión</h5> {{$created_at}}</th>
					</tr>
					<tr>
						<th colspan="2" class="">NOTA ENTREGA</th>
						<th colspan="4" class="text-right text-danger"><h5>N° {{sprintf("%08d", $id)}}</h5></th>
					</tr>
					<tr>
						<th colspan="" class="text-right">Nombre y Apellido o Razón Social del Comprador</th>
						<td colspan="" class="text-left">{{ $cliente->nombre }}</td>
						<th colspan="" class="text-right">N° de RIF./CED N° o Pasaporte N°</th>
						<td colspan="3" class="text-left">{{ $cliente->identificacion }}</td>
					</tr>
					<tr>
						<th colspan="" class="text-right">Domicilio Fiscal</th>
						<td colspan="" class="text-left">{{ $cliente->direccion }}</td>
						<th colspan="" class="text-right">Método de Pago</th>
						<td colspan="3" class="text-left"></td>
					</tr>

					<tr class="tr-secondary">
						<th class="cell2">
							Código
						</th>
						<th class="cell3">
							Descripción
						</th>
						<th>
							Cantidad
						</th>
						<th>
							P/U
						</th>
						<th class="text-right">
							Monto
						</th>
					</tr>
					@foreach ($pedido as $val)
						<tr class="tr-secondary">
							<td class="cell2">
								{{$val->producto->codigo_proveedor}}
							</td>
							<td class="cell3">
								{{$val->producto->descripcion}}
							</td>
							<td>
								{{number_format($val->ct,2)}}
							</td>
							<td>
								{{number_format($val->producto->precio,2)}}
							</td>
							
							<td class="text-right">
								{{number_format($val->ct*$val->producto->precio,2)}}
							</td>
						</tr>
					@endforeach


					<tr>
						<td class="text-right" colspan="4">Sub-total:</td>
						<th class="text-right" colspan="">{{number_format($subtotal)}}</th>
					</tr>

					<tr>
						<td class="text-right" colspan="4">Descuento:</td>
						<th class="text-right" colspan="">{{number_format($total_des)}}</th>
					</tr>
					
					
					<tr>
						<td class="text-right" colspan="4">Total:</td>
						<th class="text-right" colspan="">{{number_format($total)}}</th>
					</tr>
				</tbody>

			</table>
        
    </section> 
    
    
</body>
</html>
