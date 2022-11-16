<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\MonedaController;
use App\Http\Controllers\ItemsPedidosController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\MovimientosCajaController;

use App\Http\Controllers\PagoPedidosController;
use App\Http\Controllers\MovimientosController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\CategoriasController;


use App\Http\Controllers\MarcasController;
use App\Http\Controllers\DepositosController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ItemsFacturaController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\tickera;
use App\Http\Controllers\sendCentral;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\LotesController;
use App\Http\Controllers\PagoFacturasController;
use App\Http\Controllers\PagosReferenciasController;
use App\Http\Controllers\GastosController;


Route::get('getip', [sendCentral::class,"getip"]);
Route::get('/backup', function () {

    \Illuminate\Support\Facades\Artisan::call('backup:run');

    return 'Copia de seguridad completada!';

});

Route::get('', [HomeController::class,"index"]);
Route::get('setCarrito', [InventarioController::class,"setCarrito"]);

Route::post('getinventario', [InventarioController::class,"index"]);
Route::post('printPrecios', [tickera::class,"precio"]);


Route::get('error', function (){
	return view("layouts.error");
})->name("error");

Route::post('login', [HomeController::class,"login"]);
Route::get('logout', [HomeController::class,"logout"]);
Route::post('verificarLogin', [HomeController::class,"verificarLogin"]);

Route::get('sucursal', [SucursalController::class,"index"]);
Route::get('setSucursal', [SucursalController::class,"setSucursal"])->name("setSucursal");
Route::get('getSucursal', [SucursalController::class,"getSucursal"]);

Route::group(['middleware' => ['login']], function () {
	


	Route::group(['middleware' => ['caja']], function () {
		Route::post('getPedidos', [PedidosController::class,"getPedidos"]);
		
		Route::get('notaentregapedido', [PedidosController::class,"notaentregapedido"]);
		
		Route::post('setDescuentoUnitario', [ItemsPedidosController::class,"setDescuentoUnitario"]);
		Route::post('setDescuentoTotal', [ItemsPedidosController::class,"setDescuentoTotal"]);
	
		Route::post('getpersona', [ClientesController::class,"getpersona"]);
		
		Route::post('setPagoPedido', [PagoPedidosController::class,"setPagoPedido"]);
		Route::post('setconfigcredito', [PagoPedidosController::class,"setconfigcredito"]);
		

		Route::post('addRefPago', [PagosReferenciasController::class,"addRefPago"]);
		Route::post('delRefPago', [PagosReferenciasController::class,"delRefPago"]);
		
		Route::post('setMoneda', [MonedaController::class,"setMoneda"]);
		
		Route::post('setPagoCredito', [PagoPedidosController::class,"setPagoCredito"]);
		
		Route::post('getDeudores', [PagoPedidosController::class,"getDeudores"]);
		Route::post('getDeudor', [PagoPedidosController::class,"getDeudor"]);
		Route::post('checkDeuda', [PagoPedidosController::class,"checkDeuda"]);
		
		
		Route::post('entregarVuelto', [PagoPedidosController::class,"entregarVuelto"]);
		
		Route::post('getMovimientosCaja', [MovimientosCajaController::class,"getMovimientosCaja"]);
		Route::post('setMovimientoCaja', [MovimientosCajaController::class,"setMovimientoCaja"]);
		
		Route::post('getMovimientos', [MovimientosController::class,"getMovimientos"]);
		Route::post('getBuscarDevolucion', [InventarioController::class,"index"]);
		Route::post('setDevolucion', [MovimientosController::class,"setDevolucion"]);
		
		Route::post('setClienteCrud', [ClientesController::class,"setClienteCrud"]);
		Route::post('getClienteCrud', [ClientesController::class,"getpersona"]);
		Route::post('delCliente', [ClientesController::class,"delCliente"]);
		Route::get('sumpedidos', [PedidosController::class,"sumpedidos"]);
		
		Route::post('imprimirTicked', [tickera::class,"imprimir"]);
		Route::get('getProductosSerial', [InventarioController::class,"getProductosSerial"]);

		Route::post('guardarCierre', [PedidosController::class,"guardarCierre"]);
		Route::get('verCierre', [PedidosController::class,"verCierre"]);
		Route::post('cerrar', [PedidosController::class,"cerrar"]);
		Route::get('getCierres', [PedidosController::class,"getCierres"]);
		Route::get('sendCuentasporCobrar', [PedidosController::class,"sendCuentasporCobrar"]);

		
	});
	Route::group(['middleware' => ['vendedor']], function () {
		// Route::post('getinventario', [InventarioController::class,"index"]);
		// Route::post('setCarrito', [InventarioController::class,"setCarrito"]);
	});
	
	Route::group(['middleware' => ['admin']], function () {
		
		
		/* GastosController */
		
		Route::post('setProveedor', [ProveedoresController::class,"setProveedor"]);
		Route::post('guardarNuevoProducto', [InventarioController::class,"guardarNuevoProducto"]);
		Route::post('guardarNuevoProductoLote', [InventarioController::class,"guardarNuevoProductoLote"]);

		Route::post('setCtxBulto', [InventarioController::class,"setCtxBulto"]);
		Route::post('setPrecioAlterno', [InventarioController::class,"setPrecioAlterno"]);
		
		Route::post('getProveedores', [ProveedoresController::class,"getProveedores"]);
		Route::get('getCategorias', [CategoriasController::class,"getCategorias"]);
		Route::post('delCategoria', [CategoriasController::class,"delCategoria"]);
		Route::post('setCategorias', [CategoriasController::class,"setCategorias"]);

		Route::post('delGastos', [GastosController::class,"delGastos"]);
		Route::post('getGastos', [GastosController::class,"getGastos"]);
		Route::post('setGasto', [GastosController::class,"setGasto"]);


	
		Route::post('delProveedor', [ProveedoresController::class,"delProveedor"]);
		Route::post('delProducto', [InventarioController::class,"delProducto"]);
	
		Route::post('getDepositos', [DepositosController::class,"getDepositos"]);
		Route::post('getMarcas', [MarcasController::class,"getMarcas"]);
		
		Route::post('getFacturas', [FacturaController::class,"getFacturas"]);
		Route::post('setFactura', [FacturaController::class,"setFactura"]);
		Route::post('delFactura', [FacturaController::class,"delFactura"]);
	
		Route::post('delItemFact', [ItemsFacturaController::class,"delItemFact"]);
		
		Route::post('getFallas', [InventarioController::class,"getFallas"]);
		Route::post('setFalla', [InventarioController::class,"setFalla"]);
		Route::post('delFalla', [InventarioController::class,"delFalla"]);
		Route::get('reporteFalla', [InventarioController::class,"reporteFalla"]);
		
		
		Route::post('removeLote', [LotesController::class,"removeLote"]);
		
		Route::get('verFactura', [FacturaController::class,"verFactura"]);
		Route::post('setUsuario', [UsuariosController::class,"setUsuario"]);
		Route::post('delUsuario', [UsuariosController::class,"delUsuario"]);
		Route::get('getUsuarios', [UsuariosController::class,"getUsuarios"]);
		Route::get('verCreditos', [PagoPedidosController::class,"verCreditos"]);
		Route::get('reporteInventario', [InventarioController::class,"reporteInventario"]);
		Route::post('getEstaInventario', [InventarioController::class,"getEstaInventario"]);

		Route::post('saveMontoFactura', [FacturaController::class,"saveMontoFactura"]);
		Route::post('setPagoProveedor', [PagoFacturasController::class,"setPagoProveedor"]);
		Route::post('getPagoProveedor', [PagoFacturasController::class,"getPagoProveedor"]);
		Route::post('delPagoProveedor', [PagoFacturasController::class,"delPagoProveedor"]);
		
		Route::post('delMovCaja', [MovimientosCajaController::class,"delMovCaja"]);


	});
		Route::post('delMov', [MovimientosController::class,"delMov"]);
	Route::post('getPedidosList', [PedidosController::class,"getPedidosUser"]);

	Route::post('getVentas', [PedidosController::class,"getVentas"]);

	Route::post('getMoneda', [MonedaController::class,"getMoneda"]);
	Route::post('today', [PedidosController::class,"today"]);
	
	Route::post('getPedido', [PedidosController::class,"getPedido"]);
	Route::post('getPedidosFast', [PedidosController::class,"getPedidosFast"]);
	Route::post('delItemPedido', [ItemsPedidosController::class,"delItemPedido"]);
	Route::post('changeEntregado', [ItemsPedidosController::class,"changeEntregado"]);
	
	Route::post('delpedido', [PedidosController::class,"delpedido"]);
	Route::post('setCantidad', [ItemsPedidosController::class,"setCantidad"]);
	Route::post('setpersonacarrito', [PedidosController::class,"setpersonacarrito"]);

	Route::post('setPrecioAlternoCarrito', [ItemsPedidosController::class,"setPrecioAlternoCarrito"]);
	Route::post('setCtxBultoCarrito', [ItemsPedidosController::class,"setCtxBultoCarrito"]);
	
	
	
	
	
	
	
	
	
	
	//Update App
	Route::get('update', [sendCentral::class,"updateApp"]);
	
	
	//Central
		Route::post('checkPedidosCentral', [InventarioController::class,"checkPedidosCentral"]);
		
		Route::get('setVentas', [sendCentral::class,"setVentas"]);
		Route::get('setGastos', [sendCentral::class,"setGastos"]);
		Route::get('setCentralData', [sendCentral::class,"setCentralData"]);
		Route::get('central', [sendCentral::class,"index"]);
		Route::get('getMonedaCentral', [sendCentral::class,"getMonedaCentral"]);
		
		Route::get('setFacturasCentral', [sendCentral::class,"setFacturasCentral"]);
		
		Route::post('getmastermachine', [sendCentral::class,"getmastermachine"]);

		
		
		
		
});
	//req
	Route::post('reqpedidos', [sendCentral::class,"reqpedidos"]);
	Route::post('reqinventario', [sendCentral::class,"reqinventario"]);
	
	//res
	Route::post('resinventario', [sendCentral::class,"resinventario"]);
	Route::post('respedidos', [sendCentral::class,"respedidos"]);

	Route::post('setexportpedido', [PedidosController::class,"setexportpedido"]);


	
	
	










// }


