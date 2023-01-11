import '../../css/modal.css'

import { useHotkeys } from 'react-hotkeys-hook';


import { useState, useEffect, useRef } from 'react';
import {cloneDeep} from 'lodash';
import db from '../database/database';



import { moneda, number } from './assets';
import ProductosList from '../components/productoslist';
import ModalAddCarrito from '../components/modaladdcarrito';
import ModalMovimientos from '../components/ModalMovimientos';
import Configuracion from '../components/configuracion';


import Pagar from '../components/pagar';
import Header from '../components/header';


import Pedidos from '../components/pedidos';

import Credito from '../components/credito';
import Vueltos from '../components/vueltos';
import Clientes from '../components/clientes';

import PedidosCentralComponent from '../components/pedidosCentral';


import Cierres from '../components/cierre';
import Inventario from '../components/inventario';

import Cajagastos from '../components/cajagastos';
import Ventas from '../components/ventas';

import ViewPedidoVendedor from '../components/viewPedidoVendedor';





export default function Facturar({user,notificar,setLoading}) {

  

  const [printprecio, setprintprecio] = useState(true)

  const [tipoTransferenciaPagar, settipoTransferenciaPagar] = useState("")
  const [dropprintprice, setdropprintprice] = useState(false)
  
  const [num, setNum] = useState(50)
  const [showOptionQMain, setshowOptionQMain] = useState(false)
  const [itemCero,setItemCero] = useState(true)
  const [qProductosMain,setQProductosMain] = useState("")

  const [orderColumn, setOrderColumn] = useState("descripcion")
  const [orderBy, setOrderBy] = useState("asc")
  
  const [inputaddCarritoFast, setinputaddCarritoFast] = useState("")


  const [view,setView] = useState("seleccionar")
  const [selectItem,setSelectItem] = useState(null)
  const [pedidoSelect,setPedidoSelect] = useState(null)
  const [pedidoData,setPedidoData] = useState({})

  const [dolar,setDolar] = useState("")
  const [peso,setPeso] = useState("")

  const [cantidad,setCantidad] = useState("")
  const [numero_factura,setNumero_factura] = useState("nuevo")

  const [onlyVueltos,setOnlyVueltos] = useState(0)

  const [showinputaddCarritoFast,setshowinputaddCarritoFast] = useState(false)
  
  const [productos,setProductos] = useState([])

  const [productosInventario,setProductosInventario] = useState([])

  const [qBuscarInventario,setQBuscarInventario] = useState("")
  const [indexSelectInventario,setIndexSelectInventario] = useState(null)


  const [inpInvbarras,setinpInvbarras] = useState("")
  const [inpInvcantidad,setinpInvcantidad] = useState("")
  const [inpInvalterno,setinpInvalterno] = useState("")
  const [inpInvunidad,setinpInvunidad] = useState("UND")
  const [inpInvcategoria,setinpInvcategoria] = useState("24")
  const [inpInvdescripcion,setinpInvdescripcion] = useState("")
  const [inpInvbase,setinpInvbase] = useState("")
  const [inpInvventa,setinpInvventa] = useState("")
  const [inpInviva,setinpInviva] = useState("0")
  const [inpInvporcentaje_ganancia, setinpInvporcentaje_ganancia] = useState("0")
  
  const [inpInvLotes,setinpInvLotes] = useState([])

  const [inpInvid_proveedor,setinpInvid_proveedor] = useState("")
  const [inpInvid_marca,setinpInvid_marca] = useState("")
  const [inpInvid_deposito,setinpInvid_deposito] = useState("")
  
  const [depositosList,setdepositosList] = useState([])
  const [marcasList,setmarcasList] = useState([])

  const [Invnum,setInvnum] = useState(25)
  const [InvorderColumn,setInvorderColumn] = useState("id")
  const [InvorderBy,setInvorderBy] = useState("desc")

  const [proveedordescripcion,setproveedordescripcion] = useState("")
  const [proveedorrif,setproveedorrif] = useState("")
  const [proveedordireccion,setproveedordireccion] = useState("")
  const [proveedortelefono,setproveedortelefono] = useState("")

  const [subViewInventario,setsubViewInventario] = useState("inventario")

  const [indexSelectProveedores,setIndexSelectProveedores] = useState(null)

  const [qBuscarProveedor,setQBuscarProveedor] = useState("")

  const [proveedoresList,setProveedoresList] = useState([])

  const [pedidoList,setPedidoList] = useState([])
  const [showMisPedido,setshowMisPedido] = useState(true)

  const [orderbycolumpedidos,setorderbycolumpedidos] = useState("id")
  const [orderbyorderpedidos,setorderbyorderpedidos] = useState("desc")



  const [debito,setDebito] = useState("")
  const [efectivo,setEfectivo] = useState("")
  const [transferencia,setTransferencia] = useState("")
  const [credito, setCredito] = useState("")

  const [viewconfigcredito,setviewconfigcredito] = useState(false)
  const [fechainiciocredito,setfechainiciocredito] = useState("")
  const [fechavencecredito,setfechavencecredito] = useState("")
  const [formatopagocredito,setformatopagocredito] = useState(1)
  const [datadeudacredito,setdatadeudacredito] = useState({})
  
  const [vuelto,setVuelto] = useState("")
  
  const [descuento,setDescuento] = useState(0)

  const [ModaladdproductocarritoToggle,setModaladdproductocarritoToggle] = useState(false)

  const [toggleAddPersona,setToggleAddPersona] = useState(false)
  const [personas,setPersona] = useState([])
  
  const [pedidos,setPedidos] = useState([])
  
  const [movimientosCaja,setMovimientosCaja] = useState([])
  const [movimientos,setMovimientos] = useState([])
  
  const [tipobusquedapedido,setTipoBusqueda] = useState("fact")

  const [tipoestadopedido,setTipoestadopedido] = useState("todos")

  
  const [busquedaPedido,setBusquedaPedido] = useState("")
  const [fecha1pedido,setFecha1pedido] = useState("")
  const [fecha2pedido,setFecha2pedido] = useState("")

  const [caja_usd,setCaja_usd] = useState("")
  const [caja_cop,setCaja_cop] = useState("")
  const [caja_bs,setCaja_bs] = useState("")
  const [caja_punto,setCaja_punto] = useState("")

  const [dejar_usd,setDejar_usd] = useState("")
  const [dejar_cop,setDejar_cop] = useState("")
  const [dejar_bs,setDejar_bs] = useState("")

  const [cierre,setCierre] = useState({})

  const [today,setToday] = useState("")
  
  const [fechaCierre,setFechaCierre] = useState("")
  
  const [viewCierre,setViewCierre] = useState("cuadre")
  const [toggleDetallesCierre,setToggleDetallesCierre] = useState(0)

  const [filterMetodoPagoToggle,setFilterMetodoPagoToggle] = useState("todos")


  const [notaCierre,setNotaCierre] = useState("")
  
  const [qDeudores,setQDeudores] = useState("")
  const [orderbycolumdeudores,setorderbycolumdeudores] = useState("saldo")
  const [orderbyorderdeudores,setorderbyorderdeudores] = useState("asc")
  const [limitdeudores,setlimitdeudores] = useState(25)



  const [deudoresList,setDeudoresList] = useState([])
  const [cierres,setCierres] = useState({})


  const [selectDeudor,setSelectDeudor] = useState(null)

  const [tipo_pago_deudor,setTipo_pago_deudor] = useState("3")
  const [monto_pago_deudor,setMonto_pago_deudor] = useState("")

  const [detallesDeudor,setDetallesDeudor] = useState([])
  
  
  const [counterListProductos,setCounterListProductos] = useState(0)

  const [countListInter,setCountListInter] = useState(0)
  const [countListPersoInter,setCountListPersoInter] = useState(0)
  
  const [viewCaja,setViewCaja] = useState(false)
  
  const [movCajadescripcion,setMovCajadescripcion] = useState("")
  const [movCajatipo,setMovCajatipo] = useState(1)
  const [movCajacategoria,setMovCajacategoria] = useState(5)
  const [movCajamonto,setMovCajamonto] = useState("")
  const [movCajaFecha,setMovCajaFecha] = useState("")

  const tbodyproductosref = useRef(null)
  const inputBuscarInventario = useRef(null)
  
  
  const tbodyproducInterref = useRef(null)
  const tbodypersoInterref = useRef(null)
  
  const inputCantidadCarritoref = useRef(null)
  const inputbusquedaProductosref = useRef(null)
  const inputmodaladdpersonacarritoref = useRef(null)
  const inputaddcarritointernoref = useRef(null)

  const refinputaddcarritofast = useRef(null)
  const refaddfast = useRef(null)




  const [typingTimeout,setTypingTimeout] = useState(0)

  const [fechaMovimientos,setFechaMovimientos] = useState("")
  
  const [showModalMovimientos,setShowModalMovimientos] = useState(false)
  const [buscarDevolucion,setBuscarDevolucion] = useState("")
  const [tipoMovMovimientos,setTipoMovMovimientos] = useState("1")
  const [tipoCatMovimientos,setTipoCatMovimientos] = useState("2")
  const [productosDevulucionSelect,setProductosDevulucionSelect] = useState([])

  const [idMovSelect,setIdMovSelect] = useState("nuevo")

  const [showModalFacturas,setshowModalFacturas] = useState(false)
  
  const [facturas,setfacturas] = useState([])

  const [factqBuscar,setfactqBuscar] = useState("")
  const [factqBuscarDate,setfactqBuscarDate] = useState("")
  const [factOrderBy,setfactOrderBy] = useState("id")
  const [factOrderDescAsc,setfactOrderDescAsc] = useState("desc")
  const [factsubView,setfactsubView] = useState("buscar")
  const [factSelectIndex,setfactSelectIndex] = useState(null)
  const [factInpid_proveedor,setfactInpid_proveedor] = useState("")
  const [factInpnumfact,setfactInpnumfact] = useState("")
  const [factInpdescripcion,setfactInpdescripcion] = useState("")
  const [factInpmonto,setfactInpmonto] = useState("")
  const [factInpfechavencimiento,setfactInpfechavencimiento] = useState("")

  const [factInpestatus,setfactInpestatus] = useState(0)
  
  
  const [qBuscarCliente,setqBuscarCliente] = useState("")
  const [numclientesCrud,setnumclientesCrud] = useState(25)
  
  
  const [clientesCrud,setclientesCrud] = useState([])
  const [indexSelectCliente,setindexSelectCliente] = useState(null)
  
  const [clienteInpidentificacion,setclienteInpidentificacion] = useState("")
  const [clienteInpnombre,setclienteInpnombre] = useState("")
  const [clienteInpcorreo,setclienteInpcorreo] = useState("")
  const [clienteInpdireccion,setclienteInpdireccion] = useState("")
  const [clienteInptelefono,setclienteInptelefono] = useState("")
  const [clienteInpestado,setclienteInpestado] = useState("")
  const [clienteInpciudad,setclienteInpciudad] = useState("")  

  const [sumPedidosArr,setsumPedidosArr] = useState([])

  const [qFallas,setqFallas] = useState("")
  const [orderCatFallas,setorderCatFallas] = useState("proveedor")
  const [orderSubCatFallas,setorderSubCatFallas] = useState("todos")
  const [ascdescFallas,setascdescFallas] = useState("")
  const [fallas,setfallas] = useState([])

  const [autoCorrector,setautoCorrector] = useState(true)
  
  const [pedidosCentral,setpedidoCentral] = useState([])
  const [indexPedidoCentral, setIndexPedidoCentral] = useState(null)

  const [showaddpedidocentral, setshowaddpedidocentral] = useState(false)
  const [permisoExecuteEnter, setpermisoExecuteEnter] = useState(true)

  const [guardar_usd,setguardar_usd] = useState("")
  const [guardar_cop,setguardar_cop] = useState("")
  const [guardar_bs,setguardar_bs] = useState("")

  const [ventasData,setventasData] = useState([])

  const [fechaventas,setfechaventas] = useState("")

  const [pedidosFast,setpedidosFast] = useState([])
  
  const [billete1,setbillete1] = useState("") 
  const [billete5,setbillete5] = useState("") 
  const [billete10,setbillete10] = useState("") 
  const [billete20,setbillete20] = useState("") 
  const [billete50,setbillete50] = useState("") 
  const [billete100,setbillete100] = useState("")

  const [pathcentral, setpathcentral] = useState("")
  const [mastermachines, setmastermachines] = useState([])

  const [usuariosData, setusuariosData] = useState([])
  const [usuarioNombre, setusuarioNombre] = useState("")
  const [usuarioUsuario, setusuarioUsuario] = useState("")
  const [usuarioRole, setusuarioRole] = useState("")
  const [usuarioClave, setusuarioClave] = useState("")
  
  const [qBuscarUsuario, setQBuscarUsuario] = useState("")
  const [indexSelectUsuarios, setIndexSelectUsuarios] = useState(null)
  
  
  const [toggleClientesBtn, settoggleClientesBtn] = useState(false)
  
  const [modViewInventario, setmodViewInventario] = useState("list")
  
  const [loteIdCarrito, setLoteIdCarrito] = useState(null)
  const refsInpInvList = useRef(null)
  
  
  const [valheaderpedidocentral, setvalheaderpedidocentral] = useState("12340005ARAMCAL")
  const [valbodypedidocentral, setvalbodypedidocentral] = useState("12341238123456123456123451234123712345612345612345123412361234561234561234512341235123456123456123451234123412345612345612345")

  const [fechaGetCierre, setfechaGetCierre] = useState("")
  const [fechaGetCierre2, setfechaGetCierre2] = useState("")
  
  
  
  const [modFact, setmodFact] = useState("factura")
  
  
  
// 1234123812345612345612345
// 1234123712345612345612345
// 1234123612345612345612345
// 1234123512345612345612345
// 1234123412345612345612345
// 12341234ARAMCAL


const [fechaQEstaInve, setfechaQEstaInve] = useState("")
const [fechaFromEstaInve, setfechaFromEstaInve] = useState("")
const [fechaToEstaInve, setfechaToEstaInve] = useState("")
const [orderByEstaInv, setorderByEstaInv] = useState("desc")
const [orderByColumEstaInv, setorderByColumEstaInv] = useState("cantidadtotal")
const [dataEstaInven, setdataEstaInven] = useState([])

const [tipopagoproveedor, settipopagoproveedor] = useState("");
const [montopagoproveedor, setmontopagoproveedor] = useState("");
const [pagosproveedor, setpagosproveedor] = useState([]);

const [busquedaAvanazadaInv, setbusquedaAvanazadaInv] = useState(false);

const [busqAvanzInputs, setbusqAvanzInputs] = useState({
  codigo_barras:"",
  codigo_proveedor:"",
  id_proveedor:"",
  id_categoria:"",
  unidad:"",
  descripcion:"",
  iva:"",
  precio_base:"",
  precio:"",
  cantidad:"",
});

///Configuracion Component
const [subViewConfig, setsubViewConfig] = useState("usuarios")


///End Configuracion Component

  



useHotkeys("tab",()=>{
  if(typeof(selectItem)=="number"&&view=="seleccionar"){
      addCarritoRequest("agregar_procesar")
    }
  },{
    enableOnTags:["INPUT", "SELECT"],
  },[view,selectItem])
  useHotkeys('f1', () => {
    if(view=="pagar"){
      // toggleModalProductos(true,()=>{
      //   inputaddcarritointernoref.current.focus()
      //   setQProductosMain("")
      //   setCountListInter(0)

      // })
      try{
        if (refaddfast) {
          if (refaddfast.current) {
            refaddfast.current.focus()
          }
        }
      }catch(err){}
      
    }else if(selectItem===null && view=="seleccionar"){
      getPedido("ultimo",()=>{
        setView("pagar")
      })
    } else if (view == "inventario" && subViewInventario == "inventario" && modViewInventario == "list"){
      guardarNuevoProductoLote()
    }

  },{
    enableOnTags:["INPUT", "SELECT"],
  },[view,selectItem]);
  useHotkeys('f2', () => {
    if (view=="seleccionar") {
      setView("pedidos")
      getPedidos()
    }else if(view=="pagar"){
      setToggleAddPersonaFun(true,()=>{
        setclienteInpnombre("")
        setclienteInptelefono("")
        setclienteInpdireccion("")

        if (inputmodaladdpersonacarritoref) {
          if (inputmodaladdpersonacarritoref.current) {
            inputmodaladdpersonacarritoref.current.focus()
          }
        }
      })
    } else if (view == "inventario" && subViewInventario == "inventario" && modViewInventario == "list") {
      changeInventario(null, null, null, "add")
    }
  },{
    enableOnTags:["INPUT", "SELECT"],
  },[view]);
  useHotkeys('f5', () => {
    if(view=="pagar"){
      del_pedido()
    }
  },{
    enableOnTags:["INPUT", "SELECT"],
  },[view]);

  useHotkeys('f4', () => {
    if(view=="pagar"){
      viewReportPedido()
    }
  },{
    enableOnTags:["INPUT", "SELECT"],
  },[view]);
  
  useHotkeys('f3', () => {
    if (view=="pagar") {
      toggleImprimirTicket() 
    }
  },{
    enableOnTags:["INPUT", "SELECT"],
    filter:false,
  },[view]);

  useHotkeys('esc', () => {
    try{
      if(view=="pedidos"){
        setView("seleccionar")
      }else if (view=="seleccionar"&&typeof(selectItem)=="number") {
        setSelectItem(null) 
        setViewCaja(false) 
      }else if(view=="seleccionar"&&typeof(selectItem)!="number"){
        inputbusquedaProductosref.current.value = ""
        inputbusquedaProductosref.current.focus()
        if (viewCaja) {
          setViewCaja(false)
        }
        if (showModalMovimientos) {
          setShowModalMovimientos(false)
        }
      }else if(view=="pagar"){
        setToggleAddPersona(false)
        toggleModalProductos(false)
        setViewCaja(false)
        if (!ModaladdproductocarritoToggle&&!toggleAddPersona&&view!="seleccionar") {
          setView("seleccionar")
        }
      }else if(view=="inventario"){
        inputBuscarInventario.current.value = ""
        inputBuscarInventario.current.focus()
        
      }
    }catch(err){
      
    }
  },{
    enableOnTags:["INPUT", "SELECT"],
    filter:false,
  },[view,selectItem,viewCaja,showModalMovimientos,ModaladdproductocarritoToggle,toggleAddPersona]);

  useHotkeys('space', () => {
    if (view=="seleccionar"&&selectItem!==null) {
      setNumero_factura("nuevo")  
    }
  },{
    enableOnTags:["INPUT", "SELECT"],
    filter:false,
  },[view,selectItem]);

  useHotkeys('d', () => {
    if (view=="pagar") {
      getDebito() 
    }
  },{
    enableOnTags:["INPUT", "SELECT"],
    filter:false,
  },[view]);

  useHotkeys('c', () => {
    if (view=="pagar") {
      getCredito()  
    }
  },{
    enableOnTags:["INPUT", "SELECT"],
    filter:false,
  },[view]);

  useHotkeys('t', () => {
    if (view=="pagar") {
      getTransferencia() 
      

    }
  },{
    enableOnTags:["INPUT", "SELECT"],
    filter:false,
  },[view]);
  
  useHotkeys('e', () => {
    if (view=="pagar") {
      getEfectivo() 
    }
  },{
    enableOnTags:["INPUT", "SELECT"],
    filter:false,
  },[view]);
  useHotkeys('down', event => {
    if(view=="seleccionar"){
      try{
        let index = counterListProductos+1
          if (tbodyproductosref.current.rows[index]) {
            setCounterListProductos(index)
            
            tbodyproductosref.current.rows[index].focus()
          }
          
        }catch(err){
          //console.log(err)
        }
      }else if(view=="pagar"){
      if (ModaladdproductocarritoToggle) {
        let index = countListInter+1
        if (tbodyproducInterref.current.rows[index]) {
          setCountListInter(index)
          tbodyproducInterref.current.rows[index].focus()
        }
        
      }else if(toggleAddPersona) {

        let index = countListPersoInter+1
        if (tbodypersoInterref) {
          if (tbodypersoInterref.current) {
            if (tbodypersoInterref.current.rows) {
              if (tbodypersoInterref.current.rows[index]) {
                setCountListPersoInter(index)
                tbodypersoInterref.current.rows[index].focus()
              } 

            }
          }

        }

      }

    } else if (view == "inventario" && subViewInventario == "inventario" && modViewInventario == "list") {
      // focusInputSibli(event.target, 1)
    }
  },{
    enableOnTags:["INPUT", "SELECT"],

  }, [view, counterListProductos, countListInter, countListPersoInter, subViewInventario, modViewInventario]);
  useHotkeys('up', event => {
    if(view=="seleccionar"){
      if (counterListProductos>0) {
        try{

          let index = counterListProductos-1
          if (tbodyproductosref.current.rows[index]) {
            tbodyproductosref.current.rows[index].focus()
            setCounterListProductos(index)
          }
        }catch(err){}
      }
    }else if(view=="pagar"){
      if (ModaladdproductocarritoToggle) {
        
        
        if (countListInter>0) {
          let index = countListInter-1
          if (tbodyproducInterref.current.rows[index]) {
            tbodyproducInterref.current.rows[index].focus()
            setCountListInter(index)
          }
        }
        

      }else if(toggleAddPersona) {

        if (countListPersoInter>0) {
          let index = countListPersoInter-1
          
          if (tbodypersoInterref) {
            if (tbodypersoInterref.current) {
              if (tbodypersoInterref.current.rows) {
                if (tbodypersoInterref.current.rows[index]) {
                  tbodypersoInterref.current.rows[index].focus()
                  setCountListPersoInter(index)
                }
              }
            }
          }
        }
        


      }
    }else if (view == "inventario" && subViewInventario == "inventario" && modViewInventario == "list") {
      // focusInputSibli(event.target, -1)
    }
  },{
    enableOnTags:["INPUT", "SELECT"],

  }, [view, counterListProductos, countListInter, countListPersoInter, subViewInventario, modViewInventario]);
  useHotkeys('enter', event => {
    try{

      if(typeof(selectItem)!="number"&&view=="seleccionar"){
        try{
          if (tbodyproductosref.current) {
            let tr = tbodyproductosref.current.rows[counterListProductos]
            let index = tr.attributes["data-index"].value
            if (permisoExecuteEnter) {
              if (productos[index]) {
                if (!productos[index].lotes.length) {
                  addCarrito(index)
                }
              }
              // console.log("Execute Enter")
            }
            //wait
          }

        }catch(err){}
        
      }else if(typeof(selectItem)=="number"&&view=="seleccionar"&&productos[selectItem]){
        addCarritoRequest("agregar")
      }else if(view=="pagar"){
        if (ModaladdproductocarritoToggle) {
          if (tbodyproducInterref.current.rows[countListInter]) {
            if (permisoExecuteEnter) {
              setProductoCarritoInterno(tbodyproducInterref.current.rows[countListInter].attributes["data-index"].value)
              // console.log("Execute Enter")

            }
            //wait
          }
        }else if(toggleAddPersona){
          if (tbodypersoInterref) {
            if (tbodypersoInterref.current) {
              if (tbodypersoInterref.current.rows[countListPersoInter]) {
                if (tbodypersoInterref.current.rows[countListPersoInter].attributes["data-index"]) {
                  setPersonas(tbodypersoInterref.current.rows[countListPersoInter].attributes["data-index"].value)

                }
              }
            }
          }
        }else{
          if (viewconfigcredito) {
            setPagoPedido()
          } else if(document.activeElement===refaddfast.current){
            addCarritoFast()
          } else {
            facturar_pedido()
          }
        }
      }else if (view == "inventario" && subViewInventario == "inventario" && modViewInventario == "list") {
        focusInputSibli(event.target,1)
      }
    }catch(err){
      console.log(err)
    }

  },{
    filterPreventDefault:false,
    enableOnTags:["INPUT", "SELECT","TEXTAREA"],
  }, [view, counterListProductos, selectItem, subViewInventario, modViewInventario]);

  useHotkeys('ctrl+enter', event => {
    if(view=="pagar"){
      if (ModaladdproductocarritoToggle) {
      }else if(toggleAddPersona){
      }else{
        facturar_e_imprimir()
      }
    } 
  },{
    filterPreventDefault:false,
    enableOnTags:["INPUT", "SELECT","TEXTAREA"],
  }, [view, counterListProductos, selectItem, subViewInventario, modViewInventario]);
  
  

  useEffect(()=>{
    if (showinputaddCarritoFast) {
      if (inputaddcarritointernoref) {
        if (inputaddcarritointernoref.current) {
          inputaddcarritointernoref.current.focus()
        }
      }
      
      if (inputbusquedaProductosref) {
        if (inputbusquedaProductosref.current) {
          inputbusquedaProductosref.current.focus()
        }
      }
    }
    
  }, [showinputaddCarritoFast])

  

  const [refPago, setrefPago] = useState([])
  const addRefPago = e => {
    
    let tipo = 1
    if (e.currentTarget) {
      tipo = e.currentTarget.attributes["data-type"].value
    }else{
      
      settipoTransferenciaPagar(e)
    }
    let descripcion = window.prompt("Referencia")
    let monto = transferencia
    let banco = e
    if (pedidoData.id&&descripcion&&monto){
      db.addRefPago({
        tipo,
        descripcion,
        monto,
        banco,
        id_pedido: pedidoData.id,
      }).then(res=>{
        getPedido(null,null,false)
        notificar(res)
        settipoTransferenciaPagar("")
      })
    }



  }
  const changeEntregado = e => {
    let id = e.currentTarget.attributes["data-id"].value
    if(confirm("Confirme Entrega de producto")){
      db.changeEntregado({id}).then(res=>{
        getPedido()
        notificar(res)
      })

    }
  }
  const delRefPago = e => {
    let id = e.currentTarget.attributes["data-id"].value
    if(confirm("Confirme eliminación de referencia")){
      db.delRefPago({id}).then(res=>{
        getPedido()
        notificar(res)
      })

    }

  }
  //Gastos component
  
  const [qgastosfecha1,setqgastosfecha1] = useState("")
  const [qgastosfecha2,setqgastosfecha2] = useState("")
  const [qgastos,setqgastos] = useState("")
  const [qcatgastos,setqcatgastos] = useState("")
  const [gastosdescripcion,setgastosdescripcion] = useState("")
  const [gastoscategoria,setgastoscategoria] = useState("3")
  const [gastosmonto,setgastosmonto] = useState("")
  const [gastosData,setgastosData] = useState({})
  
  const delGastos = e => {
    let id = e.currentTarget.attributes["data-id"].value
    if (id && confirm("Confirme eliminación de gasto")) {
      db.delGastos({id}).then(res=>{
        notificar(res)
        getGastos()
      })
      
    }
  }
  const getGastos = () => {

    db.getGastos({
      qgastosfecha1,
      qgastosfecha2,
      qgastos,
      qcatgastos,
    }).then(res=>{
      if (res.data) {
        if (res.data.gastos) {
          setgastosData(res.data)
        }else {
          setgastosData({})
        }
      }
    })
  }
  const setGasto = e => {
    e.preventDefault()

    db.setGasto({
      gastosdescripcion,
      gastoscategoria,
      gastosmonto
    }).then(res=>{
      notificar(res)
      getGastos()
    })
  }
  //End Gastos Component
  
  const [qBuscarCategorias,setQBuscarCategorias] = useState("")
  const [categorias,setcategorias] = useState([])

  const [categoriasDescripcion,setcategoriasDescripcion] = useState("")
  const [indexSelectCategorias,setIndexSelectCategorias] = useState(null)

  const delCategorias = () => {
    setLoading(true)
    let id = null
    if (indexSelectCategorias) {
      if (categorias[indexSelectCategorias]) {
        id = categorias[indexSelectCategorias].id
      }
    }

    db.delCategoria({id}).then(res=>{
      setLoading(false)
      getCategorias()
      notificar(res)
      setIndexSelectCategorias(null)
    })
  }

  const addNewCategorias = e => {
    e.preventDefault()

    let id = null
    if (indexSelectCategorias) {
      if (categorias[indexSelectCategorias]) {
        id = categorias[indexSelectCategorias].id
      }
    }

    if (categoriasDescripcion) {
      setLoading(true)
      db.setCategorias({id,categoriasDescripcion})
      .then(res=>{
        notificar(res)
        setLoading(false)
        getCategorias()
      })
    }
  }
  const getCategorias = () => {
    db.getCategorias({
      q:qBuscarCategorias
    }).then(res=>{
      if (res.data) {
        if (res.data.length) {
          setcategorias(res.data)
        }else{
          setcategorias([])
        }
      }
    })
  }
  const setInputsCats = () => {
    if (indexSelectCategorias) {
      let obj = categorias[indexSelectCategorias]
      if (obj) {
        setcategoriasDescripcion(obj.descripcion)
      }
      
    }
  }
  useEffect(()=>{
    setInputsCats()
  }, [indexSelectCategorias])
  useEffect(()=>{
    getCategorias()
  }, [qBuscarCategorias])



  useEffect(()=>{
    getUsuarios()
    
  }, [qBuscarUsuario])


  useEffect(() => {
    setInputsUsuarios()
  }, [indexSelectUsuarios])
  

  useEffect(()=>{
    // let isMounted = true;  
    getMoneda() // ya invoca getProductos()
    getPedidosList()
    getToday()

    // return () => { isMounted = false }
  },[])

  useEffect(()=>{
    getFallas()
  },[
    qFallas,
    orderCatFallas,
    orderSubCatFallas,
    ascdescFallas
  ])
  useEffect(()=>{
    addCarritoFast()
  },[inputaddCarritoFast])
  useEffect(()=>{
    getClienteCrud()
  },[qBuscarCliente])
  useEffect(()=>{
    focusCtMain()
  },[selectItem])
  useEffect(()=>{
    getFacturas(false)
  },[
  factqBuscar,
  factqBuscarDate,
  factOrderBy,
  factOrderDescAsc
  ])
  useEffect(() => {
    if (view=="pedidos") {
      getPedidos()
    }
  }, [fecha1pedido,fecha2pedido,tipobusquedapedido,tipoestadopedido,filterMetodoPagoToggle,orderbycolumpedidos,orderbyorderpedidos]);
  useEffect(()=>{
    if (selectDeudor==null) {
      getDeudores()
    }else{
      getDeudor()
    }
  },[selectDeudor])
  useEffect(()=>{
    getBuscarDevolucion()
  },[buscarDevolucion])
  useEffect(()=>{
    buscarInventario()
  },[
    Invnum,
    InvorderColumn,
    InvorderBy,
    qBuscarInventario,
  ]);

  useEffect(()=>{
    if (viewCaja) {
      getMovimientosCaja()
    }
  },[viewCaja,movCajaFecha])
  useEffect(()=>{
    if (showModalMovimientos) {
      getMovimientos()

    }
  },[showModalMovimientos,fechaMovimientos])
  

  useEffect(()=>{
    getProveedores()
  },[qBuscarProveedor])
  useEffect(()=>{
    if (view=="inventario") {
      if (subViewInventario=="fallas") {
        getFallas()
      }else if (subViewInventario=="inventario") {
        getProductos()
      }else if (subViewInventario=="proveedores") {
        getProveedores()
      }
    } else if (view =="pedidosCentral"){
      getmastermachine()
    }

    if (view=="seleccionar") {
      if (inputbusquedaProductosref) {
        if (inputbusquedaProductosref.current) {
          inputbusquedaProductosref.current.value = ""
          inputbusquedaProductosref.current.focus()
        }
      }
    }
  },[view,subViewInventario])  

  useEffect(() => {
    if (view == "credito" || view =="vueltos") {
      getDeudores()
      getDeudor()
    }
  }, [view,qDeudores,orderbycolumdeudores,orderbyorderdeudores]);

  useEffect(()=>{
    getProductos()
  },[
    num,
    itemCero,
    //qProductosMain,
    orderColumn,
    orderBy,
  ])

  useEffect(()=>{
    setInputsInventario()
  },[indexSelectInventario])

  useEffect(()=>{
    getVentas()
  },[fechaventas])

  useEffect(()=>{
    if (subViewInventario =="proveedores") {
      setInputsProveedores()
      
    } else if (subViewInventario == "facturas"){
      getPagoProveedor()
    }

  }, [subViewInventario,indexSelectProveedores])

  useEffect(()=>{
    setBilletes()
  },[
    billete1,
    billete5,
    billete10,
    billete20,
    billete50,
    billete100,
  ])
  useEffect(()=>{
    getEstaInventario()
  },[
fechaQEstaInve,
fechaFromEstaInve,
fechaToEstaInve,
orderByEstaInv,
orderByColumEstaInv])

  useEffect(()=>{
    getPedidos()
  },[showMisPedido])

  


  let total_caja_calc = ( parseFloat(caja_usd?caja_usd:0) + (parseFloat(caja_cop?caja_cop:0)/parseFloat(peso)) + (parseFloat(caja_bs?caja_bs:0)/parseFloat(dolar)) ).toFixed(2)
  let total_caja_neto = !total_caja_calc||total_caja_calc=="NaN"?0:total_caja_calc

  let total_dejar_caja_calc = ( parseFloat(dejar_usd?dejar_usd:0) + (parseFloat(dejar_cop?dejar_cop:0)/parseFloat(peso)) + (parseFloat(dejar_bs?dejar_bs:0)/parseFloat(dolar)) ).toFixed(2)
  let total_dejar_caja_neto = !total_dejar_caja_calc||total_dejar_caja_calc=="NaN"?0:total_dejar_caja_calc

  let total_punto = dolar&&caja_punto?(caja_punto/dolar).toFixed(2):0

const openReporteFalla = (id) => {
  if (id) {
    db.openReporteFalla(id)
    
  }
} 
const getEstaInventario = () => {

  if (time!=0) {
    clearTimeout(typingTimeout)
  }

  let time = window.setTimeout(()=>{
    setLoading(true)
    db.getEstaInventario({
      fechaQEstaInve,
      fechaFromEstaInve,
      fechaToEstaInve,
      orderByEstaInv,
      orderByColumEstaInv})
    .then(e=>{
      setdataEstaInven(e.data)
      setLoading(false)
    })
  },150)
  setTypingTimeout(time)

}

const setporcenganancia = (tipo,base=0,fun=null) => {
  let insert = window.prompt("Porcentaje")
  if (insert) {
    if (number(insert)) {
      if (tipo=="unique") {
        let re = (parseFloat(inpInvbase) + (parseFloat(inpInvbase)*(parseFloat(insert)/100))).toFixed(2)
        if (re) {
          setinpInvventa(re)

        }
      }else if("list"){
        let re = (parseFloat(base) + (parseFloat(base)*(parseFloat(insert)/100))).toFixed(2)
        if (re) {
          fun(re)

        }
      }
    }

  }

}

const focusInputSibli = (tar, mov) => {
  let inputs = [].slice.call(refsInpInvList.current.elements)
  let index;
  if (tar.tagName == "INPUT") {

    if (mov == "down") {
      mov = 11
    } else if (mov == "up") {
      mov = -11
    }
  }
  for (let i in inputs) {
    if (tar == inputs[i]) {
      index = parseInt(i) + mov
      if (refsInpInvList.current[index]) {
        refsInpInvList.current[index].focus()
      }
      break
    }
  }
  if (typeof (index) === "undefined") {
    if (refsInpInvList.current[0]) {
      refsInpInvList.current[0].focus()
    }
  }
}
const sendCuentasporCobrar = () => {
  db.sendCuentasporCobrar({}).then(res=>{
    notificar(res)
  })
}
const setBackup = () => {
  db.backup({})
}
const getCierres = () => {
  db.getCierres({fechaGetCierre,fechaGetCierre2}).then(res=>{
    if (res.data) {
      if (res.data.cierres) {
        setCierres(res.data)
      } else {
        setCierres({})
      }
    }
  })
}
const cerrar_dia = (e) => {
  e.preventDefault()
  setLoading(true)
  db.cerrar({
  fechaCierre,
  total_caja_neto,
  total_punto,
  dejar_usd,
  dejar_cop,
  dejar_bs,}).then(res=>{

    let cierreData = res.data
    if (res.data) {
      setguardar_usd(cierreData["efectivo_guardado"])
      setguardar_cop("")
      setguardar_bs("")

      // if (cierreData["match_cierre"]) {


      //   setDejar_usd(cierreData["match_cierre"]["dejar_dolar"])
      //   setDejar_cop(cierreData["match_cierre"]["dejar_peso"])
      //   setDejar_bs(cierreData["match_cierre"]["dejar_bss"])
      //   setNotaCierre(cierreData["match_cierre"]["nota"])


      //   setguardar_usd(cierreData["match_cierre"]["efectivo_guardado"])
      //   setguardar_cop(cierreData["match_cierre"]["efectivo_guardado_cop"])
      //   setguardar_bs(cierreData["match_cierre"]["efectivo_guardado_bs"])
        
      // }
    }
    setCierre(cierreData)

    setLoading(false)
  })
}
const focusCtMain = () => {
  if (inputCantidadCarritoref.current) {
    inputCantidadCarritoref.current.focus()

  }
}
function getBuscarDevolucion() {
  setLoading(true)

  if (time!=0) {
    clearTimeout(typingTimeout)
  }

  let time = window.setTimeout(()=>{
    db.getBuscarDevolucion({
      qProductosMain:buscarDevolucion,
      num:10,
      itemCero:true,
      orderColumn:"descripcion",
      orderBy:"asc"
    }).then(res=>{
      setProductosDevulucionSelect(res.data)
      setLoading(false)
    })
  },150)
  setTypingTimeout(time)
}
const setToggleAddPersonaFun = (prop,callback=null)=> {
  setToggleAddPersona(prop)
  if (callback) {callback()}
}
const getMovimientos = (val="") =>{
  setLoading(true)
  db.getMovimientos({val,fechaMovimientos}).then(res=>{
    setMovimientos(res.data)

    // if (!res.data.length) {
      // setIdMovSelect("nuevo")
    // }else{
    //   if (res.data[0]) {
    //     setIdMovSelect(res.data[0].id)
    //   }
    // }
    setLoading(false)
  })
}
const getDeudor = () => {
  try{
    let id_p = deudoresList.filter(e=>e.id==selectDeudor)[0] 
    if (id_p) {
        setLoading(true)
      db.getDeudor({onlyVueltos,id:id_p.id}).then(res=>{
        // detallesDeudor
        setDetallesDeudor(res.data)
        setLoading(false)
      })
    }
  }catch(err){}
}
const entregarVuelto = () => {
    let monto = window.prompt("Monto a entregar")
    if(monto){

      if (pedidoData.id&&number(monto)) {
          setLoading(true)
  
        db.entregarVuelto({id_pedido:pedidoData.id,monto}).then(res=>{
          notificar(res)
          getPedido()
          getMovimientosCaja()
          setLoading(false)
  
        })
      }
    }
}
const getDebito = () =>{
  setDebito(pedidoData.clean_total)
  setEfectivo("")
  setTransferencia("")
  setCredito("")
}
const getCredito = () =>{
  setCredito(pedidoData.clean_total)
  setEfectivo("")
  setDebito("")
  setTransferencia("")
}
const getTransferencia = () =>{
  setTransferencia(pedidoData.clean_total)
  setEfectivo("")
  setDebito("")
  setCredito("")
}
const getEfectivo = () =>{
  setEfectivo(pedidoData.clean_total)
  setDebito("")
  setTransferencia("")
  setCredito("")
}
const getToday = () =>{
  db.today({}).then(res=>{
    let today = res.data
    setToday(today)
    setFechaCierre(today)
    setFecha1pedido(today)
    setFecha2pedido(today)
    setFechaMovimientos(today)
    setMovCajaFecha(today)
    setfechaventas(today)
    setqgastosfecha1(today)
    setqgastosfecha2(today)
    setfechainiciocredito(today)

  })
}
const getMovimientosCaja = () =>{
  setLoading(true)
  db.getMovimientosCaja({fecha:movCajaFecha}).then(res=>{
    setMovimientosCaja(res.data)
    setLoading(false)
  })
}
const setMovimientoCaja = e =>{
  e.preventDefault()
  setLoading(true)
  db.setMovimientoCaja({
    descripcion:movCajadescripcion,
    tipo:movCajatipo,
    categoria:movCajacategoria,
    monto:movCajamonto,
    fecha:movCajaFecha

  }).then(res=>{
    getMovimientosCaja()
    notificar(res)
    setLoading(false)
    setMovCajadescripcion("")
    setMovCajamonto("")

  })
}
const filterMetodoPago = e => {
  let type = e.currentTarget.attributes["data-type"].value

  setFilterMetodoPagoToggle(type)
}
const onchangecaja = e => {
  let name = e.currentTarget.attributes["name"].value
  let val
  if (name=="notaCierre"||name=="tipo_pago_deudor"||name=="qDeudores") {
    val = e.currentTarget.value
  }else{
    val = number(e.currentTarget.value)
    val = val=="NaN"||!val?"":val
  }

  switch(name){
    case 'caja_usd':
      setCaja_usd(val)
    break;

    case 'caja_cop':
      setCaja_cop(val)
    break;

    case 'caja_bs':
      setCaja_bs(val)
    break;

    case 'dejar_usd':
      setDejar_usd(val)
    break;

    case 'dejar_cop':
      setDejar_cop(val)
    break;

    case 'dejar_bs':
      setDejar_bs(val)
    break;

    case 'caja_punto':
      setCaja_punto(val)
    break;

    case 'notaCierre':
      setNotaCierre(val)
    break;

    case 'tipo_pago_deudor':
      setTipo_pago_deudor(val)
    break;
    case 'monto_pago_deudor':
      setMonto_pago_deudor(val)
    break;
    case 'qDeudores':
      setQDeudores(val)
    break;



    
  }
}
const setMoneda = e => {
  const tipo = e.currentTarget.attributes["data-type"].value
  let valor = window.prompt("Nuevo valor")
  if (valor) {
    db.setMoneda({tipo,valor}).then(res=>{
      getMoneda()
      getProductos()
    })
  }
}
const getMoneda = () => {
  setLoading(true)
  db.getMoneda().then(res=>{
    if (res.data.peso){
      setPeso(res.data.peso)
    }

    if (res.data.dolar) {
      setDolar(res.data.dolar)
    }
    setLoading(false)
  })
}
const toggleModalProductos = (prop,callback=null) => {
  //setModaladdproductocarritoToggle(prop)
//  if (inputaddcarritointernoref) {
//    if (inputaddcarritointernoref.current){
//      inputaddcarritointernoref.current.focus()//

//    }
//    
//  }
//  if (callback) {callback()}
}
const [selectprinter,setselectprinter] = useState(1)
const toggleImprimirTicket = (id_fake=null) => {
  if (pedidoData) {
    let printer = window.prompt("Número de impresora donde desea imprimir (La que seleccione se guardará por ésta sesión). 1 | 2 ",selectprinter)
    let moneda = window.prompt("Moneda: $ | bs | cop","bs")

      // let printer = 1
      // if (selectprinter) {
      //   printer = selectprinter
      // }else{
        setselectprinter(printer)
      //}

        if (printer && moneda) {
          console.log("Imprimiendo...")

          db.imprimirTicked({
            id: id_fake ?id_fake:pedidoData.id,
            moneda,
            printer,
            printprecio,
          }).then(res=>{
            notificar(res)
          })
        }
      

    
  }
}
const onChangePedidos = e =>{
  const type = e.currentTarget.attributes["data-type"].value
  const value = e.currentTarget.value
  switch(type){
    case "busquedaPedido":
      setBusquedaPedido(value)
    break;
    case "fecha1pedido":
      setFecha1pedido(value)
    break;
    case "fecha2pedido":
      setFecha2pedido(value)
    break;
  }
}
const getPedidos = e => {
  if (e) {
    e.preventDefault()
  }
  setLoading(true)
  setPedidos([])

  if (time!=0) {
    clearTimeout(typingTimeout)
  }
  let time = window.setTimeout(()=>{
    db.getPedidos({
      vendedor:showMisPedido?[user.id_usuario]:[],
      busquedaPedido,
      fecha1pedido,
      fecha2pedido,
      tipobusquedapedido,
      tipoestadopedido,
      filterMetodoPagoToggle,
      orderbycolumpedidos,
      orderbyorderpedidos}).then(res=>{
      if (res.data) {
        setPedidos(res.data)
      }else{
        setPedidos([])
      }
      setLoading(false)
    })
  },150)
  setTypingTimeout(time)

}
const getProductos = (valmain=null) => {

  setpermisoExecuteEnter(false)
  setLoading(true)

  if (time!=0) {
    clearTimeout(typingTimeout)
  }

  if(view=="seleccionar"){
    if (inputbusquedaProductosref.current) {
      valmain = inputbusquedaProductosref.current.value
    }
  }

  let time = window.setTimeout(()=>{
    db.getinventario({vendedor:showMisPedido?[user.id_usuario]:[],num,itemCero,qProductosMain:valmain?valmain:qProductosMain,orderColumn,orderBy}).then(res=>{
      if (res.data) {
        let len = res.data.length
        if (len) {
          setProductos(res.data)
        }
        if (!len) {
          setProductos([])
        }
        if (!res.data[counterListProductos]) {
          setCounterListProductos(0)
          setCountListInter(0)
        }

        if (showinputaddCarritoFast) {
          if (len==1) {
            setQProductosMain("")
            let id_pedido_fact = null
            if (ModaladdproductocarritoToggle&&pedidoData.id) {
              id_pedido_fact = pedidoData.id
            }
            addCarritoRequest("agregar",res.data[0].id,id_pedido_fact)

          }
        }
      }
      setLoading(false)
    })
    setpermisoExecuteEnter(true)

  },150)
  setTypingTimeout(time)


}
const getPersona = q => {
  setLoading(true)
  if (time!=0) {
    clearTimeout(typingTimeout)
  }

  let time = window.setTimeout(()=>{
    db.getpersona({q}).then(res=>{
      if (res.data) {
        if(res.statusText=="OK"){
          if (res.data.length) {
            setPersona(res.data)
          }else{
            setPersona([])
          }
        }
        if (!res.data.length) {
          setclienteInpidentificacion(q)
        }
        setLoading(false)

      }
    })
    
  },100)
  setTypingTimeout(time)

}
const setPersonaFast = e => {
  e.preventDefault()
  db.setClienteCrud({
    id:null,
    clienteInpidentificacion,
    clienteInpnombre,
    clienteInpdireccion,
    clienteInptelefono,
  }).then(res=>{
    notificar(res)
    if (res.data) {
      if (res.data.estado) {
        if (res.data.id) {
          setPersonas(res.data.id)
        }

      }
      
    }
    setLoading(false)
  })
}
const printCreditos = () => {
  db.openPrintCreditos("qDeudores="+qDeudores+"&orderbycolumdeudores="+orderbycolumdeudores+"&orderbyorderdeudores="+orderbyorderdeudores+"")
}
const getPedidosList = (callback=null)=>{
  db.getPedidosList({vendedor:user.id_usuario?user.id_usuario:1}).then(res=>{
    if (res.data) {
      setPedidoList(res.data)
    }
    if (res.data[0]) {
      setNumero_factura(res.data[0].id)
    }else{
      setNumero_factura('nuevo')

    }
    if(callback){callback()}
  })
} 
  const [showModalPedidoFast, setshowModalPedidoFast] = useState(false)
  const getPedidoFast = (e) => {
    let id = e.currentTarget.attributes["data-id"].value
    setshowModalPedidoFast(true)
    getPedido(id)
  }
  const getPedido = (id, callback = null, clearPagosPedido=true) => {
  setLoading(true)
  if (!id) {
    id = pedidoSelect
  }else{
    setPedidoSelect(id)
  }
  db.getPedido({id}).then(res=>{
    setLoading(false)
    if (res.data) {
      setPedidoData(res.data)
      setdatadeudacredito({})
      setviewconfigcredito(false)
      
      if (clearPagosPedido) {
        setTransferencia("")
        setDebito("")
        setEfectivo("")
        setCredito("")
        setVuelto("")
      }
      setrefPago([])
      
      getPedidosFast()
      
      if (res.data.referencias) {
        if (res.data.referencias.length) {
          setrefPago(res.data.referencias)
        }
        
      }else{
        setrefPago([])
      }
  
      if (res.data.pagos) {
        let d = res.data.pagos
        if (d.filter(e=>e.tipo==1)[0]) {
          let var_setTransferencia = d.filter(e=>e.tipo==1)[0].monto
          if (var_setTransferencia=="0.00") {
            if(clearPagosPedido){setTransferencia("")}
  
          }else{
            setTransferencia(d.filter(e=>e.tipo==1)[0].monto)
  
          }
  
        }
        if (d.filter(e=>e.tipo==2)[0]) {
          let var_setDebito = d.filter(e=>e.tipo==2)[0].monto
          if (var_setDebito=="0.00") {
            if(clearPagosPedido){setDebito("")}
  
          }else{
            setDebito(d.filter(e=>e.tipo==2)[0].monto)
  
          }
  
        }
        if (d.filter(e=>e.tipo==3)[0]) {
          let var_setEfectivo = d.filter(e=>e.tipo==3)[0].monto
          if (var_setEfectivo=="0.00") {
            if(clearPagosPedido){setEfectivo("")}
  
          }else{
            setEfectivo(d.filter(e=>e.tipo==3)[0].monto)
  
          }
  
        }
        if (d.filter(e=>e.tipo==4)[0]) {
          let var_setCredito = d.filter(e=>e.tipo==4)[0].monto
          if (var_setCredito=="0.00") {
            if(clearPagosPedido){setCredito("")}
  
          }else{
            setCredito(d.filter(e=>e.tipo==4)[0].monto)
  
          }
  
        }
        if (d.filter(e=>e.tipo==6)[0]) {
          let var_setVuelto = d.filter(e=>e.tipo==6)[0].monto
          if (var_setVuelto=="0.00") {
            if(clearPagosPedido){setVuelto("")}
  
          }else{
            setVuelto(d.filter(e=>e.tipo==6)[0].monto)
  
          }
  
        }
      }else{
        alert("Sin pagos registrados")
      }
      if (callback) { callback() }
    }

  })
}
const addCarrito = (e,callback=null) => {
  let index, loteid;
  if (e.currentTarget) {
    let attr = e.currentTarget.attributes 
    index = attr["data-index"].value
    
    if (attr["data-loteid"]) {
      loteid = attr["data-loteid"].value
    }
    
  }else{
    index = e
  }
  setLoteIdCarrito(loteid)

  if (index != counterListProductos && productos[index].lotes.length) {
    setCounterListProductos(index)
  }else{
    if (pedidoList[0]) {
      setNumero_factura(pedidoList[0].id)
    }else{
      setNumero_factura("nuevo")
    }
    setSelectItem(parseInt(index))
    if (callback) {callback()}

  }
  

}
const addCarritoRequest = (e,id_direct=null,id_pedido_direct=null) =>{
  try{
    setLoading(true)
    let type
    if (e.currentTarget) {
      type = e.currentTarget.attributes["data-type"].value
      e.preventDefault()
    }else{
      type = e
    }
    let id = null
    if (productos[selectItem]) {
      id = productos[selectItem].id
    }
    if (id_direct) {
      id = id_direct
    }

    db.setCarrito({ id, type, cantidad, numero_factura:id_pedido_direct?id_pedido_direct:numero_factura, loteIdCarrito}).then(res=>{
      // getProductos()

      if (numero_factura=="nuevo") {
        getPedidosList()
      }
      switch(res.data.type){
        case "agregar":
          setSelectItem(null)
          notificar(res)

          if (showinputaddCarritoFast&&ModaladdproductocarritoToggle) {
            getPedido(res.data.num_pedido)
          }

        break;
        case "agregar_procesar":
          getPedido(res.data.num_pedido,()=>{
            setView("pagar")
            setSelectItem(null)
            
          })
        break;
      }
      setCantidad("")
      if (inputbusquedaProductosref) {
        if (inputbusquedaProductosref.current) {
          inputbusquedaProductosref.current.value = ""
          inputbusquedaProductosref.current.focus()

        }
      }
      
      setLoading(false)
    })

  }catch(err){
    console.log(err)
  }
}
const onClickEditPedido = e =>{
  const id = e.currentTarget.attributes["data-id"].value
  getPedido(id,()=>{
    setView("pagar")
  })
}
const setexportpedido = e => {
  const id = e.currentTarget.attributes["data-id"].value
  db.setexportpedido({id}).then(res=>{
    getPedidos()
  })
}
const onCLickDelPedido = e => {
  if (confirm("¿Seguro de eliminar?")) {
    const current = e.currentTarget.attributes
    const id = current["data-id"].value
    let motivo = window.prompt("¿Cuál es el Motivo de eliminación?")
    if (motivo) {
      db.delpedido({id,motivo}).then(res=>{
        notificar(res)
        
        
        switch(current["data-type"].value){
          case 'getDeudor':
            getDeudor()

          break;

          case 'getPedidos':
            getPedidos()
            getPedidosList()

          break;
        }
      })

    }
  }
}
const delItemPedido = (e) => {
  setLoading(true)
  const index = e.currentTarget.attributes["data-index"].value
  db.delItemPedido({index}).then(res=>{
    getPedido()
    setLoading(false)
    notificar(res)
  })
}
const setPrecioAlternoCarrito = e => {
  let iditem = e.currentTarget.attributes["data-iditem"].value
  let p = window.prompt("p1 | p2","p1")
  if (p=="p1" || p=="p2") {
    db.setPrecioAlternoCarrito({iditem,p}).then(res=>{
      notificar(res)
      getPedido()
    })
  }

} 

const setCtxBultoCarrito = e => {
  let iditem = e.currentTarget.attributes["data-iditem"].value
  let ct = window.prompt("Cantidad por bulto")
  if (ct) {
    db.setCtxBultoCarrito({iditem,ct}).then(res=>{
      notificar(res)
      getPedido()
    })
  }

} 



const setDescuentoTotal = (e) => {
  // setLoading(true)

  let descuento = window.prompt("Descuento Total *0 para eliminar*")
  let index = e.currentTarget.attributes["data-index"].value
  
  if (descuento) {
    if (descuento=="0") {
      db.setDescuentoTotal({index,descuento:0}).then(res=>{
        getPedido()
        setLoading(false)
        notificar(res)

      })
    }else{
      if (typeof parseFloat(descuento) == "number" && pedidoData.clean_subtotal) {

        let total = parseFloat(pedidoData.clean_subtotal)

        descuento = (100-((parseFloat(descuento)*100)/total).toFixed(3))


        db.setDescuentoTotal({index,descuento}).then(res=>{
          getPedido()
          setLoading(false)
          notificar(res)

        })
      }
    }
  }

}
const setDescuentoUnitario = (e) => {
  setLoading(true)
  const descuento = window.prompt("Descuento unitario")
  if (descuento) {
    const index = e.currentTarget.attributes["data-index"].value
    db.setDescuentoUnitario({index,descuento}).then(res=>{
      getPedido()
      setLoading(false)
      notificar(res)
    })
  }
}
const setCantidadCarrito = (e) => {
  const cantidad = window.prompt("Cantidad")
  if (cantidad) {
    const index = e.currentTarget.attributes["data-index"].value
    setLoading(true)
    db.setCantidad({index,cantidad}).then(res=>{
      getPedido()
      setLoading(false)
      notificar(res)
    })
  }
} 
const setProductoCarritoInterno = (e) => {
  let cantidad = window.prompt("Cantidad","1")
  if (cantidad&&pedidoData.id) {
    setLoading(true)
    let id;
    if (e.currentTarget) {
      id = e.currentTarget.attributes["data-index"].value

    }else{
      id = e
    }
    let type = "agregar"
    db.setCarrito({id,type,cantidad,numero_factura:pedidoData.id}).then(res=>{
      getPedido()
      setModaladdproductocarritoToggle(false)
      setLoading(false)

    })
  }
}
const setPersonas = (e) => {
    setLoading(true)
    let id_cliente;

    if (e.currentTarget) {

      id_cliente = e.currentTarget.attributes["data-index"].value
    }else{
      id_cliente = e

    }
    if (pedidoData.id) {
      db.setpersonacarrito({numero_factura:pedidoData.id,id_cliente}).then(res=>{
        getPedido()
        setToggleAddPersona(false)
        setLoading(false)
        notificar(res)
      })

    }
}
const facturar_e_imprimir = () => {
  toggleImprimirTicket();
  facturar_pedido();
}
const setPagoPedido = () => {
  setLoading(true)

  db.setPagoPedido({
    id:pedidoData.id,
    debito,
    efectivo,
    transferencia,
    credito,
    vuelto,
  }).then(res=>{
    notificar(res)
    setLoading(false)
    
    if (res.data.estado) {
      if(showinputaddCarritoFast){
        setshowinputaddCarritoFast(false)
      }
      setView("seleccionar")
      // getPedidos()
      getPedidosList()
      getProductos()

      setSelectItem(null)
      setviewconfigcredito(false)

    }
  })
}



const setconfigcredito = e => {
  e.preventDefault()

  if (pedidoData.id) {
    setLoading(true)
    db.setconfigcredito({
      fechainiciocredito,
      fechavencecredito,
      formatopagocredito,
      id_pedido: pedidoData.id,
    }).then(res=>{
      notificar(res)
      setLoading(false)
    })
  }
}
const facturar_pedido = () => {
  if (refinputaddcarritofast.current !== document.activeElement) {
    if (pedidoData.id) {
      if (credito) {
        
        db.checkDeuda({id_cliente:pedidoData.id_cliente}).then(res=>{
          if (res.data) {
            let p = res.data.pedido_total
            setdatadeudacredito(p)
            setviewconfigcredito(true)
            
          }
        })
      }else{
        setPagoPedido()
      }
      

    }
  }

}
const del_pedido = () =>{
  if (confirm("¿Seguro de eliminar?")) {
    if (pedidoData.id) {
      let motivo = window.prompt("¿Cuál es el Motivo de eliminación?")
      if (motivo) {
        db.delpedido({id:pedidoData.id,motivo}).then(res=>{
          notificar(res)
          getPedidosList()
          setView("seleccionar")
        })
      }

    }else{
      alert("No hay pedido seleccionado")
    }
  }
}
const guardar_cierre = (e,callback=null) => {
  let type = e.currentTarget.attributes["data-type"].value
  setLoading(true)
  db.guardarCierre({
    fechaCierre,
    
    total_caja_neto,

    dejar_usd,
    dejar_cop,
    dejar_bs,

    total_dejar_caja_neto,
    total_punto,

    guardar_usd,
    guardar_cop,
    guardar_bs,
    
    efectivo: cierre["total_caja"],
    transferencia: cierre[1],
    entregadomenospend: cierre["entregadomenospend"],
    caja_inicial:cierre["caja_inicial"],

    precio: cierre["precio"],
    precio_base: cierre["precio_base"],
    ganancia: cierre["ganancia"],
    porcentaje: cierre["porcentaje"],
    desc_total: cierre["desc_total"],
    numventas: cierre["numventas"],
    

    notaCierre,
  }).then(res=>{
    
    setLoading(false)
    notificar(res,false)
    
    if (res.data.estado) {
      if (type=="ver") {
        verCierreReq(fechaCierre,type)
      }else{
        setLoading(true)
        
        db.sendCierre({type,fecha:fechaCierre}).then(res=>{
          notificar(res,false)

          notificar({data:{msj:"Respaldando Base de Datos",estado:true}})
          setLoading(true)
          db.backup({}).then(res=>{
            notificar(res)

            db.sendCuentasporCobrar({}).then(res=>{
              notificar(res)
              setLoading(false)
            })
          })
          

        })
        
      }

    }     

  })
}
const verCierreReq = (fechaCierre,type="ver") => {
  // console.log(fecha)
  // if (window.confirm("Confirme envio")) {
    db.openVerCierre({fechaCierre,type})
  // }
}
const setPagoCredito = e =>{
  e.preventDefault()
  try{
    let id_p = deudoresList.filter(e=>e.id==selectDeudor)[0] 

    if (id_p) {
      let id_cliente = id_p.id
      setLoading(true)
      db.setPagoCredito({
        id_cliente,
        tipo_pago_deudor,
        monto_pago_deudor,
      }).then(res=>{
        notificar(res)
        setLoading(false)
        getDeudor(id_cliente)
      })
    }
  }catch(err){
    
  }
}
const getDeudores = e =>{
  setLoading(true)

  if (time!=0) {
    clearTimeout(typingTimeout)
  }

  let time = window.setTimeout(()=>{
    
    db.getDeudores({
      qDeudores,
      view,
      orderbycolumdeudores,
      orderbyorderdeudores,
      limitdeudores,
    }).then(res=>{
      if (res.data) {
        if (res.data.length) {
          setDeudoresList(res.data)
        }else{
          setDeudoresList([])
        }
      }
      setLoading(false)
    })
  },150)
  setTypingTimeout(time)


}
const clickSetOrderColumn = e => {
  let valor = e.currentTarget.attributes["data-valor"].value

  if (valor==orderColumn) {
    if (orderBy=="desc") {
      setOrderBy("asc")
    }else{
      setOrderBy("desc")

    }
  }else{
    setOrderColumn(valor)
  }

}

const clickSetOrderColumnPedidos = e => {
  let valor = e.currentTarget.attributes["data-valor"].value

  if (valor==orderbycolumpedidos) {
    if (orderbyorderpedidos=="desc") {
      setorderbyorderpedidos("asc")
    }else{
      setorderbyorderpedidos("desc")

    }
  }else{
    setorderbycolumpedidos(valor)
  }




}
const onchangeinputmain = e => {
  let val = e.currentTarget.value
  setQProductosMain(val)

}
const delMovCaja = e =>{
  if (confirm("¿Seguro de eliminar?")) {
    setLoading(true)
    const id = e.currentTarget.attributes["data-id"].value


    db.delMovCaja({id}).then(res=>{
      setLoading(false)
      getMovimientosCaja()
      notificar(res)
    })

  }
}
const delMov = e =>{
  if (confirm("¿Seguro de eliminar?")) {
    setLoading(true)
    const id = e.currentTarget.attributes["data-id"].value


    db.delMov({id}).then(res=>{
      setLoading(false)
      notificar(res)
      getMovimientos()
    })

  }
}
const setDevolucion = e => {
  setLoading(true)
  let id = e.currentTarget.attributes["data-id"].value
  let type = e.currentTarget.attributes["data-type"].value

  let cantidad = window.prompt("Cantidad")

  if (cantidad) {
    db.setDevolucion({
      id,
      idMovSelect,
      cantidad,
      tipoMovMovimientos:type,
      tipoCatMovimientos,
      fechaMovimientos,
    }).then(res=>{
      setLoading(false)
      getMovimientos()
      setBuscarDevolucion("")
      notificar(res)
    })

  }
}
const buscarInventario = e => {

  let checkempty = productosInventario.filter(e => e.type).filter(e=>
    e.codigo_barras == ""||
    e.descripcion == ""||
    e.id_categoria == ""||
    e.unidad == ""||
    e.id_proveedor == ""||
    e.cantidad == ""||
    e.precio == "")

  if (!checkempty.length) {
    setLoading(true)

    if (time!=0) {
      clearTimeout(typingTimeout)
    }

    let time = window.setTimeout(()=>{
      db.getinventario({
        num:Invnum,
        itemCero:true,
        qProductosMain:qBuscarInventario,
        orderColumn:InvorderColumn,
        orderBy:InvorderBy,
        busquedaAvanazadaInv,
        busqAvanzInputs,
        
      }).then(res=>{
        if (res.data) {
          if (res.data.length) {
            setProductosInventario(res.data)
          }else{
            setProductosInventario([])
          }
          setIndexSelectInventario(null)
          if (res.data.length===1) {
            setIndexSelectInventario(0)
          }else if(res.data.length==0){
            setinpInvbarras(qBuscarInventario)
          }
        }
        setLoading(false)
      })
    },120)
    setTypingTimeout(time)

  }else{
    alert("Hay productos pendientes en carga de Inventario List!")
  }



}
const getProveedores = e => {
  if (time != 0) {
    clearTimeout(typingTimeout)
  }

  let time = window.setTimeout(() => {
    setLoading(true)
    db.getProveedores({
      q:qBuscarProveedor
    }).then(res=>{
      if (res.data.length) {
        setProveedoresList(res.data)
      }else{
        setProveedoresList([])
      }
      setLoading(false)
      if (res.data.length===1) {
        setIndexSelectProveedores(0)
      }
    })
  }, 150)
  setTypingTimeout(time)

  if (!categorias.length) {
    getCategorias()
  }
  if (!depositosList.length) {
    db.getDepositos({
      q:qBuscarProveedor
    }).then(res=>{
      setdepositosList(res.data)
    })
  }


}
const setInputsInventario = () =>{
  if (productosInventario[indexSelectInventario]) {
    let obj = productosInventario[indexSelectInventario]
    setinpInvbarras(obj.codigo_barras?obj.codigo_barras:"")
    setinpInvcantidad(obj.cantidad?obj.cantidad:"")
    setinpInvalterno(obj.codigo_proveedor?obj.codigo_proveedor:"")
    setinpInvunidad(obj.unidad?obj.unidad:"")
    setinpInvdescripcion(obj.descripcion?obj.descripcion:"")
    setinpInvbase(obj.precio_base?obj.precio_base:"")
    setinpInvventa(obj.precio?obj.precio:"")
    setinpInviva(obj.iva?obj.iva:"")

    setinpInvcategoria(obj.id_categoria?obj.id_categoria:"")
    setinpInvid_proveedor(obj.id_proveedor?obj.id_proveedor:"")
    setinpInvid_marca(obj.id_marca?obj.id_marca:"")
    setinpInvid_deposito(obj.id_deposito?obj.id_deposito:"")

    setinpInvLotes(obj.lotes ? obj.lotes : [])

  }
}
const setNewProducto = () => {
  setIndexSelectInventario(null)
  setinpInvbarras("")
  setinpInvcantidad("")
  setinpInvalterno("")
  setinpInvunidad("UND")
  setinpInvdescripcion("")
  setinpInvbase("")
  setinpInvventa("")
  setinpInviva("0")

  setinpInvLotes([])

  if (facturas[factSelectIndex]) {
    setinpInvid_proveedor(facturas[factSelectIndex].proveedor.id)
  }
  

  setinpInvid_marca("GENÉRICO")
  setinpInvid_deposito(1)
}
const setInputsProveedores = () =>{
  if (proveedoresList[indexSelectProveedores]) {
    let obj = proveedoresList[indexSelectProveedores]

    setproveedordescripcion(obj.descripcion)
    setproveedorrif(obj.rif)
    setproveedordireccion(obj.direccion)
    setproveedortelefono(obj.telefono)
  

  }
}
const guardarNuevoProducto = e => {
  e.preventDefault()
  setLoading(true)

  let id = null

  if (indexSelectInventario!=null) {
    if (productosInventario[indexSelectInventario]) {
      id = productosInventario[indexSelectInventario].id
    }
  }

  let id_factura = null

  if (factSelectIndex!=null) {
    if (facturas[factSelectIndex]) {
      id_factura = facturas[factSelectIndex].id
    }
  }

  db.guardarNuevoProducto({
    id,
    inpInvbarras,
    inpInvcantidad,
    inpInvalterno,
    inpInvunidad,
    inpInvcategoria,
    inpInvdescripcion,
    inpInvbase,
    inpInvventa,
    inpInviva,
    inpInvid_proveedor,
    inpInvid_marca,
    inpInvid_deposito,
    inpInvporcentaje_ganancia,
    id_factura,

    inpInvLotes,

  }).then(res=>{
    notificar(res)

    setLoading(false)

    if (res.data.estado) {
      buscarInventario()
      getFacturas(false)

      setinpInvbarras("")
      setinpInvcantidad("")
      setinpInvalterno("")
      setinpInvunidad("UND")
      setinpInvcategoria("24")
      setinpInvdescripcion("")
      setinpInvbase("")
      setinpInvventa("")
      setinpInviva("0")
      setinpInvid_marca("")
    }
  })
}
const getPedidosFast = () => {

  db.getPedidosFast({vendedor:showMisPedido?[user.id_usuario]:[],fecha1pedido}).then(res=>{
    setpedidosFast(res.data)
    
  })
}
const setSameGanancia = () => {
  let insert = window.prompt("Porcentaje")
  if (insert) {
    
    let obj = cloneDeep(productosInventario)
    obj.map(e=>{
      if (e.type) {
        let re = (parseFloat(e.precio_base) + (parseFloat(e.precio_base) * (parseFloat(insert) / 100))).toFixed(2)
        if (re) {
          e.precio = re
        }
      }
      return e
    })
    setProductosInventario(obj)    
  }
}
const [sameCatValue, setsameCatValue] = useState("")
const [sameProValue, setsameProValue] = useState("")

const setSameCat = (val) => {
  if (confirm("¿Confirma Generalizar categoría?")) {
    let obj = cloneDeep(productosInventario)
    obj.map(e => {
      if (e.type) {
        e.id_categoria = val
      }
      return e
    })
    setProductosInventario(obj)
    setsameCatValue(val)
  }

}
const setSamePro = (val) => {
  if (confirm("¿Confirma Generalizar proveeedor?")) {
    let obj = cloneDeep(productosInventario)
    obj.map(e => {
      if (e.type) {
        e.id_proveedor = val
      }
      return e
    })
    setProductosInventario(obj)
    setsameProValue(val)
  }
}

const busqAvanzInputsFun = (e,key) => {
  let obj = cloneDeep(busqAvanzInputs)
  obj[key] = e.target.value
  setbusqAvanzInputs(obj)
 
}
const buscarInvAvanz = () => {
  buscarInventario(null)
}
  
  
  
const setProveedor = e =>{
  setLoading(true)
  e.preventDefault()

  let id = null

  if (indexSelectProveedores!=null) {
    if (proveedoresList[indexSelectProveedores]) {
      id = proveedoresList[indexSelectProveedores].id
    }
  }
  db.setProveedor({
    proveedordescripcion,
    proveedorrif,
    proveedordireccion,
    proveedortelefono,
    id
  }).then(res=>{
    notificar(res)
    getProveedores()
    setLoading(false)

  })
} 
const delProveedor = e => {
  let id;
  if (indexSelectProveedores!=null) {
    if (proveedoresList[indexSelectProveedores]) {
      id = proveedoresList[indexSelectProveedores].id
    }
  }
  if (confirm("¿Desea Eliminar?")) {
    setLoading(true)
    db.delProveedor({id}).then(res=>{
      setLoading(false)
      getProveedores()
      notificar(res)

      if (res.data.estado) {
        setIndexSelectProveedores(null)
      }
    })

  }

}
const delProducto = e => {
  let id;
  if (indexSelectInventario!=null) {
    if (productosInventario[indexSelectInventario]) {
      id = productosInventario[indexSelectInventario].id
    }
  }
  if (confirm("¿Desea Eliminar?")) {
    setLoading(true)
    db.delProducto({id}).then(res=>{
      setLoading(false)
      buscarInventario()
      notificar(res)
      if (res.data.estado) {
        setIndexSelectInventario(null)
      }
    })
    
  }
}
const getFacturas = (clean = true) =>{

  if (time!=0) {
      clearTimeout(typingTimeout)
    }

    let time = window.setTimeout(()=>{
      setLoading(true)
      db.getFacturas({
        factqBuscar,
        factqBuscarDate,
        factOrderBy,
        factOrderDescAsc
      }).then(res=>{
        setLoading(false)
        setfacturas(res.data)

        if (res.data.length === 1) {
          setfactSelectIndex(0)
        }

        if (clean) {
          setfactSelectIndex(null)
        }
      })

    },100)
    setTypingTimeout(time)

}
const setFactura = e => {
  e.preventDefault()
  setLoading(true)

  let id = null

  if (factSelectIndex!=null) {
    if (facturas[factSelectIndex]) {
      id = facturas[factSelectIndex].id
    }
  }
  db.setFactura({
    factInpid_proveedor,
    factInpnumfact,
    factInpdescripcion,
    factInpmonto,
    factInpfechavencimiento,
    factInpestatus,
    id
  }).then(res=>{
    notificar(res)
    getFacturas()
    setLoading(false)
    if (res.data.estado) {
      setfactsubView("buscar")
      setfactSelectIndex(null)
    }

  })
}
const delFactura = e => {
  let id = null

  if (factSelectIndex!=null) {
    if (facturas[factSelectIndex]) {
      id = facturas[factSelectIndex].id
    }
  }
  if (confirm("¿Desea Eliminar?")) {
    setLoading(true)
    db.delFactura({id}).then(res=>{
      setLoading(false)
      getFacturas()
      notificar(res)
      if (res.data.estado) {
        setfactsubView("buscar")
        setfactSelectIndex(null)
      }
    })
    
  }
}
const saveFactura = () => {

  if (facturas[factSelectIndex]) {
    let id = facturas[factSelectIndex].id
    let monto = facturas[factSelectIndex].summonto_base_clean
    db.saveMontoFactura({id,monto}).then(e=>{
      getFacturas(false)
    })
  }
}
const delItemFact = e =>{
  let id = e.currentTarget.attributes["data-id"].value

  if (confirm("¿Desea Eliminar?")) {
    setLoading(true)
    db.delItemFact({id}).then(res=>{
      setLoading(false)
      notificar(res)
      if (res.data.estado) {
        getFacturas(false)
        buscarInventario()
      }
    })
  }
}
const setClienteCrud = e => {
  e.preventDefault()
  setLoading(true)
  let id = null

  if (indexSelectCliente!=null) {
    if (clientesCrud[indexSelectCliente]) {
      id = clientesCrud[indexSelectCliente].id
    }
  }

  db.setClienteCrud({
    id,
    clienteInpidentificacion,
    clienteInpnombre,
    clienteInpcorreo,
    clienteInpdireccion,
    clienteInptelefono,
    clienteInpestado,
    clienteInpciudad
  }).then(res=>{
    notificar(res)
    getClienteCrud()
    setLoading(false)
  })
}
const getClienteCrud = () => {
  setLoading(true)
  db.getClienteCrud({q:qBuscarCliente,num:numclientesCrud}).then(res=>{
    setLoading(false)
    setclientesCrud(res.data)
    setindexSelectCliente(null)
  })
}
const delCliente = () => {
  let id = null

  if (indexSelectCliente!=null) {
    if (clientesCrud[indexSelectCliente]) {
      id = clientesCrud[indexSelectCliente].id
    }
  }
  if (confirm("¿Desea Eliminar?")) {
    setLoading(true)
    db.delCliente({id}).then(res=>{
      setLoading(false)
      getClienteCrud()
      notificar(res)
      if (res.data.estado) {
        setindexSelectCliente(null)
      }
    })
    
  }
}
const sumPedidos = e => {
  let tipo = e.currentTarget.attributes["data-tipo"].value
  let id = e.currentTarget.attributes["data-id"].value
  if (tipo=="add") {
    if (sumPedidosArr.indexOf(id)<0) {
      setsumPedidosArr(sumPedidosArr.concat(id))
    }
  }else{
      setsumPedidosArr(sumPedidosArr.filter(e=>e!=id))

  }
}
const addCarritoFast = () => {
  if (pedidoData.id) {
    if (refaddfast) {
      if (refaddfast.current) {
        db.getinventario({exacto:"si",num:1,itemCero:true,qProductosMain:refaddfast.current.value,orderColumn:"id",orderBy:"desc"}).then(res=>{
          if(res.data.length==1){
            let id = res.data[0].id
            db.setCarrito({id,type:null,cantidad:1,numero_factura:pedidoData.id}).then(res=>{
              refaddfast.current.value=""
              getPedido()
            })
          }
        })
      }
    }
  }
} 
const getFallas = () => {
  setLoading(true)
  db.getFallas({qFallas,orderCatFallas,orderSubCatFallas,ascdescFallas}).then(res=>{
    setfallas(res.data)
    setLoading(false)
  })
}
const setFalla = e => {
  let id_producto = e.currentTarget.attributes["data-id"].value 
  db.setFalla({id:null,id_producto}).then(res=>{
    notificar(res)
    setSelectItem(null)

  })
}
const delFalla = e => {
  if (confirm("¿Desea Eliminar?")) {
    let id = e.currentTarget.attributes["data-id"].value 
    db.delFalla({id}).then(res=>{
      notificar(res)
      getFallas()
    })
  }
}
const viewReportPedido = () =>{
  db.openNotaentregapedido({ id: pedidoData.id})
  
}


function updateinventario() {
  setLoading(true)
  db.reqinventario({ path: pathcentral }).then(res=>{
    console.log(res.data)
    setLoading(false)
  })
}
const getip = () => {
  db.getip({}).then(res=>alert(res.data))
}
const getmastermachine = () => {
  setLoading(true)
  setpathcentral("")

  db.getmastermachine({}).then(res=>{
    if (res.data) {
      if (!res.data.length) {
        setmastermachines([])
      }else{
        setmastermachines(res.data)
      }
      setLoading(false)
    }
  })
}
const getPedidosCentral = () => {
  setLoading(true)
  db.reqpedidos({ path: pathcentral }).then(res=>{
    setLoading(false)
    if (res.data) {
      if (res.data.length) {
        if (res.data[0]){
          if (res.data[0].id) {
            setpedidoCentral(res.data)
          }
        }
      }else{
        setpedidoCentral([])
      }

      if (res.data.msj) {
        notificar(res)
      }
    }else{
      setpedidoCentral([])
    }
  })
}
const procesarImportPedidoCentral = () => {
  // console.log(valbodypedidocentral)
  // Id pedido 4
  // Count items pedido 4
  // sucursal code *


  // console.log(valheaderpedidocentral)
  //id_pedido 4 (0)
  //id_producto 4 (0)
  //base 6 (2)
  //venta 6 (2)
  //cantidad 5 (1)

  try{

    // Header...
    let id_pedido_header = valheaderpedidocentral.substring(0,4).replace(/\b0*/g, '')
    let count = valheaderpedidocentral.substring(4,8).replace(/\b0*/g, '')
    let sucursal_code = valheaderpedidocentral.substring(8)

    let import_pedido = {}

    if (id_pedido_header&&count&&sucursal_code) {

      db.getSucursal({}).then(res=>{
        try{
          if (res.data) {
            if (res.data.codigo) {
              if (res.data.codigo!=sucursal_code) {
                throw("Error: Pedido no pertenece a esta sucursal!")
              }else{
                import_pedido.created_at = today
                import_pedido.sucursal = sucursal_code
                import_pedido.id = id_pedido_header
                import_pedido.base = 0
                import_pedido.venta = 0
                import_pedido.items = []

                let body = valbodypedidocentral.toString().replace(/[^0-9]/g,"")
                if (!body) {
                  
                  throw("Error: Cuerpo incorrecto!")
                }else{
                  
                  let ids_productos = body.match(/.{1,25}/g).map((e,i)=>{

                    if (e.length!=25) {
                      throw("Error: Líneas no tienen la longitud!")

                    }
                    let id_pedido = e.substring(0,4).replace(/\b0*/g, '')
                    let id_producto = e.substring(4,8).replace(/\b0*/g, '')

                    let base = e.substring(8,12).replace(/\b0*/g, '')+"."+e.substring(12,14)
                    let venta = e.substring(14,18).replace(/\b0*/g, '')+"."+e.substring(18,20)
                    
                    let cantidad = e.substring(20,24).replace(/\b0*/g, '')+"."+e.substring(24,25)

                    // if (id_pedido_header!=id_pedido) {
                    //   
                    //   throw("Error: Producto #"+(i+1)+" no pertenece a este pedido!")
                    // }


                    
                    return {id_producto,
                      id_pedido,
                      base,
                      venta,
                      cantidad}
                  })
                  db.getProductosSerial({count,ids_productos:ids_productos.map(e=>e.id_producto)})
                  .then(res=>{
                    try{

                      let obj = res.data

                      if (obj.estado) {
                        if (obj.msj) {
                          let pro = obj.msj.map((e,i)=>{
                            let filter = ids_productos.filter(ee=>ee.id_producto==e.id)[0];

                            let cantidad = filter.cantidad
                            let base = filter.base
                            let venta = filter.venta
                            let monto = cantidad*venta

                            import_pedido.items.push({
                              cantidad: cantidad,
                              producto: {
                                precio_base: base,
                                precio: venta,
                                codigo_barras: e.codigo_barras,
                                codigo_proveedor: e.codigo_proveedor,
                                descripcion: e.descripcion,
                                id: e.id,
                              },
                              id:i,
                              monto,
                            })

                            import_pedido.base += parseFloat(cantidad*base)
                            import_pedido.venta += parseFloat(monto)


                          })
                          // console.log("import_pedido",import_pedido)
                          setpedidoCentral(pedidosCentral.concat(import_pedido))
                          setshowaddpedidocentral(false)

                        }
                      }else{
                        alert(obj.msj)
                      } 

                    }catch(err){
                      alert(err)
                    }

                  })
                  
                }

              }
            }
          }
        }catch(err){
          alert(err)
        }
      })

    }else{
      throw("Error: Cabezera incorrecta!")
    }
  }catch(err){
    alert(err)
  }
}
const selectPedidosCentral = e => {

  try{
    let index = e.currentTarget.attributes["data-index"].value
    let tipo = e.currentTarget.attributes["data-tipo"].value

    let pedidosCentral_copy = cloneDeep(pedidosCentral)

    if (tipo=="select") {
      if (pedidosCentral_copy[indexPedidoCentral].items[index].aprobado===true) {
        
        pedidosCentral_copy[indexPedidoCentral].items[index].aprobado = false
        pedidosCentral_copy[indexPedidoCentral].items[index].ct_real = ""

      }else if (pedidosCentral_copy[indexPedidoCentral].items[index].aprobado===false) {

        delete pedidosCentral_copy[indexPedidoCentral].items[index].aprobado
        delete pedidosCentral_copy[indexPedidoCentral].items[index].ct_real
      
      }else if (typeof(pedidosCentral_copy[indexPedidoCentral].items[index].aprobado) === "undefined") {
        pedidosCentral_copy[indexPedidoCentral].items[index].aprobado = true

      }

    }else if(tipo=="changect_real"){
      pedidosCentral_copy[indexPedidoCentral].items[index].ct_real = number(e.currentTarget.value,4)
    }
    
    setpedidoCentral(pedidosCentral_copy)



    // console.log(pedidosCentral_copy)

  }catch(err){
    console.log(err)
  }
}
const checkPedidosCentral = () => {
  if (indexPedidoCentral!==null&&pedidosCentral) {
    if (pedidosCentral[indexPedidoCentral]) {
      setLoading(true)
      db.checkPedidosCentral({pathcentral,pedido:pedidosCentral[indexPedidoCentral]}).then(res=>{
        setLoading(false)
        
        notificar(res)
        if (res.data.estado) {
          getPedidosCentral()
        }
      })
    }
  }
}
const verDetallesFactura = (e=null) => {
  let id = facturas[factSelectIndex]
  if (e) {
    id = e
  } 
  if (id) {
    db.openVerFactura({ id: facturas[factSelectIndex].id})
  }
  
}
const getVentas = () => {
  setLoading(true)
  db.getVentas({fechaventas}).then(res=>{
    setventasData(res.data)
    setLoading(false)
  })
}
const getVentasClick = () => {
  getVentas()
}
const setBilletes = () => {

  let total = 0
  total = (parseInt(!billete1?0:billete1)*1) + (parseInt(!billete5?0:billete5)*5) + (parseInt(!billete10?0:billete10)*10) + (parseInt(!billete20?0:billete20)*20) + (parseInt(!billete50?0:billete50)*50) + (parseInt(!billete100?0:billete100)*100)
  setCaja_usd(total)
}
const addNewUsuario = e => {
  e.preventDefault()

  let id = null
  if (indexSelectUsuarios) {
    id = usuariosData[indexSelectUsuarios].id
  }

  if (usuarioRole&&usuarioNombre&&usuarioUsuario) {
    setLoading(true)
    db.setUsuario({id,role:usuarioRole,nombres:usuarioNombre,usuario:usuarioUsuario,clave:usuarioClave})
    .then(res=>{
      notificar(res)
      setLoading(false)
      getUsuarios()
    })
  }else{
    console.log("Err: addNewUsuario"+usuarioRole+" "+usuarioNombre+" "+usuarioUsuario)
  }
}
const setInputsUsuarios = () => {
  if (indexSelectUsuarios) {
    let obj = usuariosData[indexSelectUsuarios]
    if (obj) {
      setusuarioNombre(obj.nombre)
      setusuarioUsuario(obj.usuario)
      setusuarioRole(obj.tipo_usuario)
      setusuarioClave(obj.clave)
    }
    
  }
}
const getUsuarios = () => {
  setLoading(true)
  db.getUsuarios({q:qBuscarUsuario}).then(res=>{
    setLoading(false)
    setusuariosData(res.data)
  })
}
const delUsuario = () => {
  setLoading(true)
  let id = null
  if (indexSelectUsuarios) {
    id = usuariosData[indexSelectUsuarios].id
  }
  db.delUsuario({id}).then(res=>{
    setLoading(false)
    getUsuarios()
    notificar(res)
  })
}
const selectProductoFast = e => {
  let id = e.currentTarget.attributes["data-id"].value
  let val = e.currentTarget.attributes["data-val"].value

  setQBuscarInventario(val)
  setfactSelectIndex("ninguna")
  setView("inventario")
  setsubViewInventario("inventario")
}
const addNewLote = e => {
  let addObj = {
    lote: "",
    creacion: "",
    vence: "",
    cantidad: "",
    type: "new",
    id: null,
  }
  setinpInvLotes(inpInvLotes.concat(addObj))
}
const changeModLote = (val, i, id, type, name = null) => {
  
    let lote = cloneDeep(inpInvLotes)

    switch (type) {
      case "update":
        if (lote[i].type != "new") {
          lote[i].type = "update"
        }
        break;
      case "delModeUpdateDelete":
        delete lote[i].type
        break;
      case "delNew":
        lote = lote.filter((e, ii) => ii !== i)
        break;
      case "changeInput":
        lote[i][name] = val
        break;

      case "delMode":
        lote[i].type = "delete"
        let id_replace = 0
        lote[i].id_replace = id_replace
        break;
    }
    setinpInvLotes(lote)
}
const reporteInventario = () => {
  db.openReporteInventario()
}

const guardarNuevoProductoLote = e => {
  // e.preventDefault()
  let id_factura = null

  if (factSelectIndex != null) {
    if (facturas[factSelectIndex]) {
      id_factura = facturas[factSelectIndex].id
    }
  }
  let lotesFil = productosInventario.filter(e => e.type)


  let checkempty = lotesFil.filter(e=>
    e.codigo_barras == ""||
    e.descripcion == ""||
    e.id_categoria == ""||
    e.unidad == ""||
    e.id_proveedor == ""||
    e.cantidad == ""||
    e.precio == "")

  if (lotesFil.length && !checkempty.length) {
    
    setLoading(true)
    db.guardarNuevoProductoLote({ lotes: lotesFil, id_factura}).then(res=>{
      notificar(res)
      setLoading(false)
      try{
        if (res.data.estado) {
          getFacturas(null)

          buscarInventario()
          
        }
      }catch(err){}
    })
  }else{
    alert("¡Error con los campos! Algunos pueden estar vacíos "+JSON.stringify(checkempty))
  }

}
const delPagoProveedor = e => {
  let id = e.target.attributes["data-id"].value
  if(confirm("¿Seguro de eliminar?")){
    db.delPagoProveedor({id}).then(res=>{
      getPagoProveedor()
      notificar(res)
    })
  }
}
const getPagoProveedor = () => {
  if (proveedoresList[indexSelectProveedores]) {
    setLoading(true)
    db.getPagoProveedor({
      id_proveedor: proveedoresList[indexSelectProveedores].id,
    }).then(res => {
      setLoading(false)
      setpagosproveedor(res.data)
    })
  }
}
const setPagoProveedor = e => {
  e.preventDefault()
  if (tipopagoproveedor&&montopagoproveedor){
    if (proveedoresList[indexSelectProveedores]) {
      db.setPagoProveedor({
        tipo: tipopagoproveedor,
        monto: montopagoproveedor,
        id_proveedor: proveedoresList[indexSelectProveedores].id,
      }).then(res=>{
        getPagoProveedor()
        notificar(res)
      })
    }
  }

}
const setCtxBulto = e => {
    let id = e.currentTarget.attributes["data-id"].value
    let bulto = window.prompt("Cantidad por bulto")
    if (bulto) {
      db.setCtxBulto({id,bulto}).then(res=>{
        buscarInventario()
        notificar(res)
      })
    }

}
const setPrecioAlterno = e => {
    let id = e.currentTarget.attributes["data-id"].value
    let type = e.currentTarget.attributes["data-type"].value
    let precio = window.prompt("PRECIO "+type)
    if (precio) {
      db.setPrecioAlterno({id,type,precio}).then(res=>{
        buscarInventario()
        notificar(res)
      })
    }

}
const changeInventario = (val, i, id, type, name = null) => {
  let obj = cloneDeep(productosInventario)

  switch (type) {
    case "update":
      if (obj[i].type != "new") {
        obj[i].type = "update"
      }
      break;
    case "delModeUpdateDelete":
      delete obj[i].type
      break;
    case "delNew":
      obj = obj.filter((e, ii) => ii !== i)
      break;
    case "changeInput":
      obj[i][name] = val
      break;
    case "add":
      let pro = ""

      if (facturas[factSelectIndex]) {
        pro = facturas[factSelectIndex].proveedor.id
      }else{
        pro = sameProValue
      }
      
      
      let newObj = [{
        id:null,
        codigo_proveedor: "",
        codigo_barras: "",
        descripcion: "",
        id_categoria: sameCatValue,
        id_marca: "",
        unidad: "UND",
        id_proveedor: pro,
        cantidad: "",
        precio_base: "",
        precio: "",
        iva: "0",
        type: "new",

      }] 

      obj = newObj.concat(obj)
    break;

    case "delMode":
      obj[i].type = "delete"
      let id_replace = 0
      obj[i].id_replace = id_replace
      break;
  }
  setProductosInventario(obj)
}
const printPrecios = type => {
  if (productosInventario.length) {
    db.printPrecios({type,ids:productosInventario.map(e=>e.id)}).then(res=>{
      console.log(res.data)
      setdropprintprice(false)
    })

  }
}
const logout = () => {
  db.logout().then(e=>{
    window.location.href = "/";
  })
}
const auth = permiso => {
  let nivel = user.nivel
  if(permiso==1){
    if (nivel == 1) {
      return true
    }
  }
  if (permiso == 2) {
    //if (nivel == 1 || nivel == 2) {
      return true
    //}
  }
  if (permiso == 3) {
    //if (nivel == 1 || nivel == 3) {
      return true
    //}
  }
  return false
}

  return (
    <>
      
        <Header
        getip={getip}
        auth={auth}
        logout={logout}
        user={user}
        dolar={dolar}
        peso={peso} 
        setMoneda={setMoneda}
        view={view}
        getPedidos={getPedidos}
        setViewCaja={setViewCaja}
        viewCaja={viewCaja}
        setShowModalMovimientos={setShowModalMovimientos}
        showModalMovimientos={showModalMovimientos}
        getVentasClick={getVentasClick}
        toggleClientesBtn={toggleClientesBtn}
        settoggleClientesBtn={settoggleClientesBtn}
        setView={setView}/>
        {
        view=="seleccionar"?
        <div className="container-fluid p-2">
          
            {typeof(selectItem)=="number"?productos[selectItem]?<ModalAddCarrito
              dolar={dolar} 
              producto={productos[selectItem]} 
              setSelectItem={setSelectItem}
              cantidad={cantidad}
              setCantidad={setCantidad}
              numero_factura={numero_factura}
              setNumero_factura={setNumero_factura}
              pedidoList={pedidoList}
              setFalla={setFalla}
              number={number}
              moneda={moneda}

              inputCantidadCarritoref={inputCantidadCarritoref}
              addCarritoRequest={addCarritoRequest}/>:null:null}

            {showModalMovimientos&&<ModalMovimientos 
              getMovimientos={getMovimientos}
              setShowModalMovimientos={setShowModalMovimientos}
              showModalMovimientos={showModalMovimientos}

              setBuscarDevolucion={setBuscarDevolucion}
              buscarDevolucion={buscarDevolucion}
              setTipoMovMovimientos={setTipoMovMovimientos}
              tipoMovMovimientos={tipoMovMovimientos}
              setTipoCatMovimientos={setTipoCatMovimientos}
              tipoCatMovimientos={tipoCatMovimientos}
              productosDevulucionSelect={productosDevulucionSelect}
              setDevolucion={setDevolucion}
              idMovSelect={idMovSelect}
              setIdMovSelect={setIdMovSelect}
              movimientos={movimientos}
              delMov={delMov}
              setFechaMovimientos={setFechaMovimientos}
              fechaMovimientos={fechaMovimientos}


              
            />}
            <div className="input-group mb-3">
                <input type="text" 
                className="form-control form-control-lg" 
                ref={inputbusquedaProductosref}
                placeholder="Buscar... Presiona (ESC)"
                onChange={e=>getProductos(e.target.value)}/>
                {/*<button onClick={()=>setshowinputaddCarritoFast(!showinputaddCarritoFast)} className={("btn btn-outline-")+(showinputaddCarritoFast?"success":"sinapsis")}>Agg. rápido</button>*/}
              
                {showOptionQMain?<>
                <span className="input-group-text pointer" onClick={() => setshowOptionQMain(false)}><i className="fa fa-arrow-right"></i></span>
                  <span className="input-group-text pointer" onClick={()=>{
                    let num = window.prompt("Número de resultados a mostrar")
                    if (num) {setNum(num)}
                  }}>Num.({num})</span>
                  <span className="input-group-text pointer" onClick={()=>setItemCero(!itemCero)}>En cero: {itemCero?"Sí":"No"}</span>
              </> : <span className="input-group-text pointer" onClick={() => setshowOptionQMain(true)}><i className="fa fa-arrow-left"></i></span>}
              
            </div>
            <ProductosList 
              moneda={moneda}
              auth={auth}
              productos={productos} 
              addCarrito={addCarrito}

              clickSetOrderColumn={clickSetOrderColumn}

              orderColumn={orderColumn}
              orderBy={orderBy}

              counterListProductos={counterListProductos}
              setCounterListProductos={setCounterListProductos}

              tbodyproductosref={tbodyproductosref}
              focusCtMain={focusCtMain}

              selectProductoFast={selectProductoFast}


            />
            {productos.length==0?<div className="text-center p-2"><small className="mr-2">Nada para mostrar...</small></div>:null}
            
          {viewCaja?
            <Cajagastos 
              setMovimientoCaja={setMovimientoCaja}
              movCajadescripcion={movCajadescripcion}
              setMovCajadescripcion={setMovCajadescripcion}
              movCajamonto={movCajamonto}
              setMovCajamonto={setMovCajamonto}
              number={number}
              setMovCajacategoria={setMovCajacategoria}
              movCajacategoria={movCajacategoria}
              setMovCajatipo={setMovCajatipo}
              movimientosCaja={movimientosCaja}
              delMovCaja={delMovCaja}
              movCajatipo={movCajatipo}

              movCajaFecha={movCajaFecha}
              viewCaja={viewCaja}
              setViewCaja={setViewCaja}
              setMovCajaFecha={setMovCajaFecha}
            />
          :null}
        </div>
        :null
        }
        {view == "pedidosCentral" ?
        <PedidosCentralComponent
          mastermachines={mastermachines}
          getmastermachine={getmastermachine}
          updateinventario={updateinventario}
          pathcentral={pathcentral}
          setpathcentral={setpathcentral}
          getPedidosCentral={getPedidosCentral}
          selectPedidosCentral={selectPedidosCentral}
          checkPedidosCentral={checkPedidosCentral}

          pedidosCentral={pedidosCentral}
          setIndexPedidoCentral={setIndexPedidoCentral}
          indexPedidoCentral={indexPedidoCentral}
          moneda={moneda}

          showaddpedidocentral={showaddpedidocentral}
          setshowaddpedidocentral={setshowaddpedidocentral}
          valheaderpedidocentral={valheaderpedidocentral}
          setvalheaderpedidocentral={setvalheaderpedidocentral}
          valbodypedidocentral={valbodypedidocentral}
          setvalbodypedidocentral={setvalbodypedidocentral}
          procesarImportPedidoCentral={procesarImportPedidoCentral}
        />
        : null}
        {view=="ventas"?<Ventas
          ventasData={ventasData}
          getVentasClick={getVentasClick}
          setfechaventas={setfechaventas}
          fechaventas={fechaventas}
          moneda={moneda}
          onClickEditPedido={onClickEditPedido}
        />:null}

        {view == "vueltos" ? <Vueltos
          onchangecaja={onchangecaja}
          qDeudores={qDeudores}
          deudoresList={deudoresList}
          selectDeudor={selectDeudor}
          setSelectDeudor={setSelectDeudor}
          tipo_pago_deudor={tipo_pago_deudor}
          monto_pago_deudor={monto_pago_deudor}
          setPagoCredito={setPagoCredito}
          onClickEditPedido={onClickEditPedido}
          onCLickDelPedido={onCLickDelPedido}
          detallesDeudor={detallesDeudor}
          onlyVueltos={onlyVueltos}
          setOnlyVueltos={setOnlyVueltos}
        
          qBuscarCliente={qBuscarCliente}
          setqBuscarCliente={setqBuscarCliente}
          clientesCrud={clientesCrud}
          setindexSelectCliente={setindexSelectCliente}
          indexSelectCliente={indexSelectCliente}
          setClienteCrud={setClienteCrud}
          delCliente={delCliente}
          clienteInpidentificacion={clienteInpidentificacion}
          setclienteInpidentificacion={setclienteInpidentificacion}
          clienteInpnombre={clienteInpnombre}
          setclienteInpnombre={setclienteInpnombre}
          clienteInpcorreo={clienteInpcorreo}
          setclienteInpcorreo={setclienteInpcorreo}
          clienteInpdireccion={clienteInpdireccion}
          setclienteInpdireccion={setclienteInpdireccion}
          clienteInptelefono={clienteInptelefono}
          setclienteInptelefono={setclienteInptelefono}
          clienteInpestado={clienteInpestado}
          setclienteInpestado={setclienteInpestado}
          clienteInpciudad={clienteInpciudad}
          setclienteInpciudad={setclienteInpciudad}
          sumPedidos={sumPedidos}
        />:null}

        {view=="clientes_crud"?
          <Clientes
          qBuscarCliente={qBuscarCliente}
          setqBuscarCliente={setqBuscarCliente}
          clientesCrud={clientesCrud}
          setindexSelectCliente={setindexSelectCliente}
          indexSelectCliente={indexSelectCliente}
          setClienteCrud={setClienteCrud}
          delCliente={delCliente}
          clienteInpidentificacion={clienteInpidentificacion}
          setclienteInpidentificacion={setclienteInpidentificacion}
          clienteInpnombre={clienteInpnombre}
          setclienteInpnombre={setclienteInpnombre}
          clienteInpcorreo={clienteInpcorreo}
          setclienteInpcorreo={setclienteInpcorreo}
          clienteInpdireccion={clienteInpdireccion}
          setclienteInpdireccion={setclienteInpdireccion}
          clienteInptelefono={clienteInptelefono}
          setclienteInptelefono={setclienteInptelefono}
          clienteInpestado={clienteInpestado}
          setclienteInpestado={setclienteInpestado}
          clienteInpciudad={clienteInpciudad}
          setclienteInpciudad={setclienteInpciudad}
        />
        :null}

        {view=="cierres"?<Cierres
          moneda={moneda}
          sendCuentasporCobrar={sendCuentasporCobrar}
          fechaGetCierre2={fechaGetCierre2}
          setfechaGetCierre2={setfechaGetCierre2}
          verCierreReq={verCierreReq}
          fechaGetCierre={fechaGetCierre}
          setfechaGetCierre={setfechaGetCierre}
          getCierres={getCierres}
          cierres={cierres}
          number={number}
          guardar_usd={guardar_usd}
          setguardar_usd={setguardar_usd}
          guardar_cop={guardar_cop}
          setguardar_cop={setguardar_cop}
          guardar_bs={guardar_bs}
          setguardar_bs={setguardar_bs}
          caja_usd={caja_usd}
          caja_cop={caja_cop}
          caja_bs={caja_bs}
          caja_punto={caja_punto}

          dejar_usd={dejar_usd}
          dejar_cop={dejar_cop}
          dejar_bs={dejar_bs}

          setDejar_usd={setDejar_usd}
          setDejar_cop={setDejar_cop}
          setDejar_bs={setDejar_bs}
          
          cierre={cierre}
          cerrar_dia={cerrar_dia}
          total_caja_neto={total_caja_neto}
          total_punto={total_punto}

          total_dejar_caja_neto={total_dejar_caja_neto}

          viewCierre={viewCierre}
          setViewCierre={setViewCierre}
          toggleDetallesCierre={toggleDetallesCierre}
          setToggleDetallesCierre={setToggleDetallesCierre}

          onchangecaja={onchangecaja}
          fechaCierre={fechaCierre}
          setFechaCierre={setFechaCierre}
          guardar_cierre={guardar_cierre}
          notaCierre={notaCierre}

          billete1={billete1}
          setbillete1={setbillete1}
          billete5={billete5}
          setbillete5={setbillete5}
          billete10={billete10}
          setbillete10={setbillete10}
          billete20={billete20}
          setbillete20={setbillete20}
          billete50={billete50}
          setbillete50={setbillete50}
          billete100={billete100}
          setbillete100={setbillete100}

          dolar={dolar}
          peso={peso} 
        />:null}
        {view=="pedidos"?<Pedidos
          setexportpedido={setexportpedido}
          pedidoData={pedidoData}
          showModalPedidoFast={showModalPedidoFast}
          setshowModalPedidoFast={setshowModalPedidoFast}
          getPedidoFast={getPedidoFast}
          clickSetOrderColumnPedidos={clickSetOrderColumnPedidos}
          orderbycolumpedidos={orderbycolumpedidos}
          setorderbycolumpedidos={setorderbycolumpedidos}
          orderbyorderpedidos={orderbyorderpedidos}
          setorderbyorderpedidos={setorderbyorderpedidos}
          moneda={moneda}
          setshowMisPedido={setshowMisPedido}
          showMisPedido={showMisPedido}
          tipobusquedapedido={tipobusquedapedido}
          
          setTipoBusqueda={setTipoBusqueda}
          busquedaPedido={busquedaPedido}
          fecha1pedido={fecha1pedido}
          fecha2pedido={fecha2pedido}
          onChangePedidos={onChangePedidos}

          onClickEditPedido={onClickEditPedido}
          onCLickDelPedido={onCLickDelPedido}

          pedidos={pedidos}
          getPedidos={getPedidos}

          filterMetodoPago={filterMetodoPago}
          filterMetodoPagoToggle={filterMetodoPagoToggle}
          tipoestadopedido={tipoestadopedido}
          setTipoestadopedido={setTipoestadopedido}
        />:null}

        
        {view=="inventario"?<Inventario
          sameCatValue={sameCatValue}
          sameProValue={sameProValue}
          setdropprintprice={setdropprintprice}
          dropprintprice={dropprintprice}
          printPrecios={printPrecios}
          setCtxBulto={setCtxBulto}
          setPrecioAlterno={setPrecioAlterno}
          qgastosfecha1={qgastosfecha1}
          setqgastosfecha1={setqgastosfecha1}
          qgastosfecha2={qgastosfecha2}
          setqgastosfecha2={setqgastosfecha2}
          qgastos={qgastos}
          setqgastos={setqgastos}
          qcatgastos={qcatgastos}
          setqcatgastos={setqcatgastos}
          gastosdescripcion={gastosdescripcion}
          setgastosdescripcion={setgastosdescripcion}
          gastoscategoria={gastoscategoria}
          setgastoscategoria={setgastoscategoria}
          gastosmonto={gastosmonto}
          setgastosmonto={setgastosmonto}
          gastosData={gastosData}
          delGastos={delGastos}
          getGastos={getGastos}
          setGasto={setGasto}

          delPagoProveedor={delPagoProveedor}
          busqAvanzInputsFun={busqAvanzInputsFun}
          busqAvanzInputs={busqAvanzInputs}
          buscarInvAvanz={buscarInvAvanz}

          busquedaAvanazadaInv={busquedaAvanazadaInv}
          setbusquedaAvanazadaInv={setbusquedaAvanazadaInv}

          setSameGanancia={setSameGanancia}
          setSameCat={setSameCat}
          setSamePro={setSamePro}

          openReporteFalla={openReporteFalla}
          getPagoProveedor={getPagoProveedor}
          setPagoProveedor={setPagoProveedor}
          pagosproveedor={pagosproveedor}
          
          tipopagoproveedor={tipopagoproveedor}
          settipopagoproveedor={settipopagoproveedor}
          montopagoproveedor={montopagoproveedor}
          setmontopagoproveedor={setmontopagoproveedor}
          setmodFact={setmodFact}
          modFact={modFact}
          saveFactura={saveFactura}
          categorias={categorias}
          setporcenganancia={setporcenganancia}
          refsInpInvList={refsInpInvList}
          guardarNuevoProductoLote={guardarNuevoProductoLote}
          changeInventario={changeInventario}
          reporteInventario={reporteInventario}
          addNewLote={addNewLote}
          changeModLote={changeModLote}
          
          modViewInventario={modViewInventario}
          setmodViewInventario={setmodViewInventario}
          setNewProducto={setNewProducto}
          verDetallesFactura={verDetallesFactura}
          showaddpedidocentral={showaddpedidocentral}
          setshowaddpedidocentral={setshowaddpedidocentral}
          valheaderpedidocentral={valheaderpedidocentral}
          setvalheaderpedidocentral={setvalheaderpedidocentral}
          valbodypedidocentral={valbodypedidocentral}
          setvalbodypedidocentral={setvalbodypedidocentral}
          procesarImportPedidoCentral={procesarImportPedidoCentral}
          moneda={moneda}
          productosInventario={productosInventario}
          qBuscarInventario={qBuscarInventario}
          setQBuscarInventario={setQBuscarInventario}

          setIndexSelectInventario={setIndexSelectInventario}
          indexSelectInventario={indexSelectInventario}

          inputBuscarInventario={inputBuscarInventario}

          inpInvbarras={inpInvbarras}
          setinpInvbarras={setinpInvbarras}
          inpInvcantidad={inpInvcantidad}
          setinpInvcantidad={setinpInvcantidad}
          inpInvalterno={inpInvalterno}
          setinpInvalterno={setinpInvalterno}
          inpInvunidad={inpInvunidad}
          setinpInvunidad={setinpInvunidad}
          inpInvcategoria={inpInvcategoria}
          setinpInvcategoria={setinpInvcategoria}
          inpInvdescripcion={inpInvdescripcion}
          setinpInvdescripcion={setinpInvdescripcion}
          inpInvbase={inpInvbase}
          setinpInvbase={setinpInvbase}
          inpInvventa={inpInvventa}
          setinpInvventa={setinpInvventa}
          inpInviva={inpInviva}
          setinpInviva={setinpInviva}
          inpInvLotes={inpInvLotes}

          number={number}
          guardarNuevoProducto={guardarNuevoProducto}

          setProveedor={setProveedor}
          proveedordescripcion={proveedordescripcion}
          setproveedordescripcion={setproveedordescripcion}
          proveedorrif={proveedorrif}
          setproveedorrif={setproveedorrif}
          proveedordireccion={proveedordireccion}
          setproveedordireccion={setproveedordireccion}
          proveedortelefono={proveedortelefono}
          setproveedortelefono={setproveedortelefono}

          subViewInventario={subViewInventario}
          setsubViewInventario={setsubViewInventario}

          setIndexSelectProveedores={setIndexSelectProveedores}
          indexSelectProveedores={indexSelectProveedores}
          qBuscarProveedor={qBuscarProveedor}
          setQBuscarProveedor={setQBuscarProveedor}
          proveedoresList={proveedoresList}

          delProveedor={delProveedor}
          delProducto={delProducto}

          inpInvid_proveedor={inpInvid_proveedor}
          setinpInvid_proveedor={setinpInvid_proveedor}
          inpInvid_marca={inpInvid_marca}
          setinpInvid_marca={setinpInvid_marca}
          inpInvid_deposito={inpInvid_deposito}
          setinpInvid_deposito={setinpInvid_deposito}

          depositosList={depositosList}
          marcasList={marcasList}
          
          setshowModalFacturas={setshowModalFacturas}
          showModalFacturas={showModalFacturas}

          facturas={facturas}

          factqBuscar={factqBuscar}
          setfactqBuscar={setfactqBuscar}
          factqBuscarDate={factqBuscarDate}
          setfactqBuscarDate={setfactqBuscarDate}
          factsubView={factsubView}
          setfactsubView={setfactsubView}
          factSelectIndex={factSelectIndex}
          setfactSelectIndex={setfactSelectIndex}
          factOrderBy={factOrderBy}
          setfactOrderBy={setfactOrderBy}
          factOrderDescAsc={factOrderDescAsc}
          setfactOrderDescAsc={setfactOrderDescAsc}
          factInpid_proveedor={factInpid_proveedor}
          setfactInpid_proveedor={setfactInpid_proveedor}
          factInpnumfact={factInpnumfact}
          setfactInpnumfact={setfactInpnumfact}
          factInpdescripcion={factInpdescripcion}
          setfactInpdescripcion={setfactInpdescripcion}
          factInpmonto={factInpmonto}
          setfactInpmonto={setfactInpmonto}
          factInpfechavencimiento={factInpfechavencimiento}
          setfactInpfechavencimiento={setfactInpfechavencimiento}

          factInpestatus={factInpestatus}
          setfactInpestatus={setfactInpestatus}

          setFactura={setFactura}
          delFactura={delFactura}

          Invnum={Invnum}
          setInvnum={setInvnum}
          InvorderColumn={InvorderColumn}
          setInvorderColumn={setInvorderColumn}
          InvorderBy={InvorderBy}
          setInvorderBy={setInvorderBy}
          delItemFact={delItemFact}

          qFallas={qFallas}
          setqFallas={setqFallas}
          orderCatFallas={orderCatFallas}
          setorderCatFallas={setorderCatFallas}
          orderSubCatFallas={orderSubCatFallas}
          setorderSubCatFallas={setorderSubCatFallas}
          ascdescFallas={ascdescFallas}
          setascdescFallas={setascdescFallas}
          fallas={fallas}
          delFalla={delFalla}

          getPedidosCentral={getPedidosCentral}
          selectPedidosCentral={selectPedidosCentral}
          checkPedidosCentral={checkPedidosCentral}
          pedidosCentral={pedidosCentral}
          setIndexPedidoCentral={setIndexPedidoCentral}
          indexPedidoCentral={indexPedidoCentral}

          fechaQEstaInve={fechaQEstaInve}
          setfechaQEstaInve={setfechaQEstaInve}
          fechaFromEstaInve={fechaFromEstaInve}
          setfechaFromEstaInve={setfechaFromEstaInve}
          fechaToEstaInve={fechaToEstaInve}
          setfechaToEstaInve={setfechaToEstaInve}
          orderByEstaInv={orderByEstaInv}
          setorderByEstaInv={setorderByEstaInv}
          orderByColumEstaInv={orderByColumEstaInv}
          setorderByColumEstaInv={setorderByColumEstaInv}

          dataEstaInven={dataEstaInven}

        />:null}
        {view =="ViewPedidoVendedor"?<ViewPedidoVendedor
        
        />:null}
        {view=="pagar"?<Pagar
          printprecio={printprecio}
          setprintprecio={setprintprecio}
          tipoTransferenciaPagar={tipoTransferenciaPagar} 
          settipoTransferenciaPagar={settipoTransferenciaPagar} 
          changeEntregado={changeEntregado}
          setPagoPedido={setPagoPedido}
          viewconfigcredito={viewconfigcredito}
          setviewconfigcredito={setviewconfigcredito}
          fechainiciocredito={fechainiciocredito}
          setfechainiciocredito={setfechainiciocredito}
          fechavencecredito={fechavencecredito}
          setfechavencecredito={setfechavencecredito}
          formatopagocredito={formatopagocredito}
          setformatopagocredito={setformatopagocredito}
          datadeudacredito={datadeudacredito}
          setdatadeudacredito={setdatadeudacredito}
          setconfigcredito={setconfigcredito}

          setCtxBultoCarrito={setCtxBultoCarrito}
          setPrecioAlternoCarrito={setPrecioAlternoCarrito}
          addRefPago={addRefPago}
          delRefPago={delRefPago}
          refPago={refPago}
          setrefPago={setrefPago}

          qProductosMain={qProductosMain}
          showinputaddCarritoFast={showinputaddCarritoFast}
          setshowinputaddCarritoFast={setshowinputaddCarritoFast}

          dolar={dolar}
          peso={peso}
          moneda={moneda}
          facturar_e_imprimir={facturar_e_imprimir}
          pedidosFast={pedidosFast}
          onClickEditPedido={onClickEditPedido}
          tipobusquedapedido={tipobusquedapedido}
          pedidos={pedidos}
          pedidoData={pedidoData} 
          getPedido={getPedido} 
          debito={debito}
          setDebito={setDebito}
          efectivo={efectivo}
          setEfectivo={setEfectivo}
          transferencia={transferencia}
          setTransferencia={setTransferencia}
          vuelto={vuelto}
          setVuelto={setVuelto}
          number={number}
          credito={credito}
          inputmodaladdpersonacarritoref={inputmodaladdpersonacarritoref}
          inputaddcarritointernoref={inputaddcarritointernoref}

          viewReportPedido={viewReportPedido}

          delItemPedido={delItemPedido}
          setDescuento={setDescuento}
          setDescuentoUnitario={setDescuentoUnitario}
          setDescuentoTotal={setDescuentoTotal}
          setCantidadCarrito={setCantidadCarrito}

          ModaladdproductocarritoToggle={ModaladdproductocarritoToggle}
          setModaladdproductocarritoToggle={setModaladdproductocarritoToggle}

          toggleModalProductos={toggleModalProductos}

          toggleAddPersona={toggleAddPersona}
          setToggleAddPersona={setToggleAddPersona}
          personas={personas}
          getPersona={getPersona}
          setPersonas={setPersonas}

          setProductoCarritoInterno={setProductoCarritoInterno}

          del_pedido={del_pedido}

          toggleImprimirTicket={toggleImprimirTicket}

          productos={productos}
          getProductos={getProductos}
          facturar_pedido={facturar_pedido}

          setCredito={setCredito}

          tbodyproducInterref={tbodyproducInterref}
          tbodypersoInterref={tbodypersoInterref}
          
          countListInter={countListInter}
          countListPersoInter={countListPersoInter}

          onchangeinputmain={onchangeinputmain}

          clickSetOrderColumn={clickSetOrderColumn}
          orderColumn={orderColumn}
          orderBy={orderBy}
          entregarVuelto={entregarVuelto}

          setPersonaFast={setPersonaFast}
          clienteInpidentificacion={clienteInpidentificacion}
          setclienteInpidentificacion={setclienteInpidentificacion}
          clienteInpnombre={clienteInpnombre}
          setclienteInpnombre={setclienteInpnombre}
          clienteInptelefono={clienteInptelefono}
          setclienteInptelefono={setclienteInptelefono}
          clienteInpdireccion={clienteInpdireccion}
          setclienteInpdireccion={setclienteInpdireccion}
          
          inputaddCarritoFast={inputaddCarritoFast}
          setinputaddCarritoFast={setinputaddCarritoFast}
          addCarritoFast={addCarritoFast}
          refinputaddcarritofast={refinputaddcarritofast}
          refaddfast={refaddfast}

          autoCorrector={autoCorrector}
          setautoCorrector={setautoCorrector}

          getDebito={getDebito}
          getCredito={getCredito}
          getTransferencia={getTransferencia}
          getEfectivo={getEfectivo}
          />
        :null}
        {view=="credito"?<Credito
          limitdeudores={limitdeudores}
          setlimitdeudores={setlimitdeudores}

          moneda={moneda}
          orderbycolumdeudores={orderbycolumdeudores}
          setorderbycolumdeudores={setorderbycolumdeudores}
          orderbyorderdeudores={orderbyorderdeudores}
          setorderbyorderdeudores={setorderbyorderdeudores}
          printCreditos={printCreditos}
          onchangecaja={onchangecaja}
          qDeudores={qDeudores}
          deudoresList={deudoresList}
          tipo_pago_deudor={tipo_pago_deudor}
          monto_pago_deudor={monto_pago_deudor}

          selectDeudor={selectDeudor}
          setSelectDeudor={setSelectDeudor}
          setPagoCredito={setPagoCredito}
          detallesDeudor={detallesDeudor}
          onClickEditPedido={onClickEditPedido}
          onCLickDelPedido={onCLickDelPedido}
          onlyVueltos={onlyVueltos}
          setOnlyVueltos={setOnlyVueltos}

          qBuscarCliente={qBuscarCliente}
          setqBuscarCliente={setqBuscarCliente}
          clientesCrud={clientesCrud}
          setindexSelectCliente={setindexSelectCliente}
          indexSelectCliente={indexSelectCliente}
          setClienteCrud={setClienteCrud}
          delCliente={delCliente}
          clienteInpidentificacion={clienteInpidentificacion}
          setclienteInpidentificacion={setclienteInpidentificacion}
          clienteInpnombre={clienteInpnombre}
          setclienteInpnombre={setclienteInpnombre}
          clienteInpcorreo={clienteInpcorreo}
          setclienteInpcorreo={setclienteInpcorreo}
          clienteInpdireccion={clienteInpdireccion}
          setclienteInpdireccion={setclienteInpdireccion}
          clienteInptelefono={clienteInptelefono}
          setclienteInptelefono={setclienteInptelefono}
          clienteInpestado={clienteInpestado}
          setclienteInpestado={setclienteInpestado}
          clienteInpciudad={clienteInpciudad}
          setclienteInpciudad={setclienteInpciudad}

          sumPedidos={sumPedidos}
          sumPedidosArr={sumPedidosArr}
          setsumPedidosArr={setsumPedidosArr}
        />
        :null}

        {view=="configuracion"?
          <Configuracion
            subViewConfig={subViewConfig}
            setsubViewConfig={setsubViewConfig}

            categorias={categorias}
            addNewCategorias={addNewCategorias}
            categoriasDescripcion={categoriasDescripcion}
            setcategoriasDescripcion={setcategoriasDescripcion}
            indexSelectCategorias={indexSelectCategorias}
            setIndexSelectCategorias={setIndexSelectCategorias}
            qBuscarCategorias={qBuscarCategorias}
            setQBuscarCategorias={setQBuscarCategorias}
            delCategorias={delCategorias}

            addNewUsuario={addNewUsuario}
            usuarioNombre={usuarioNombre}
            setusuarioNombre={setusuarioNombre}
            usuarioUsuario={usuarioUsuario}
            setusuarioUsuario={setusuarioUsuario}
            usuarioRole={usuarioRole}
            setusuarioRole={setusuarioRole}
            usuarioClave={usuarioClave}
            setusuarioClave={setusuarioClave}
            indexSelectUsuarios={indexSelectUsuarios}
            setIndexSelectUsuarios={setIndexSelectUsuarios}
            qBuscarUsuario={qBuscarUsuario}
            setQBuscarUsuario={setQBuscarUsuario}
            delUsuario={delUsuario}
            usuariosData={usuariosData}
          />
        :null}
      
    </>
  );
}

