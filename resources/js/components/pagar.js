import {useEffect,useState} from 'react';

import Modaladdproductocarrito from '../components/Modaladdproductocarrito';
import ModaladdPersona from '../components/ModaladdPersona';
import Modalconfigcredito from '../components/Modalconfigcredito';



export default function Pagar({
changeEntregado,
setPagoPedido,
viewconfigcredito,
setviewconfigcredito,
fechainiciocredito,
setfechainiciocredito,
fechavencecredito,
setfechavencecredito,
formatopagocredito,
setformatopagocredito,
datadeudacredito,
setdatadeudacredito,
setconfigcredito,

setPrecioAlternoCarrito,
setCtxBultoCarrito,

addRefPago,
delRefPago,
refPago,
setrefPago,

pedidosFast,
pedidoData,
getPedido,
debito,
setDebito,
efectivo,
setEfectivo,
transferencia,
setTransferencia,
credito,
setCredito,

vuelto,
setVuelto,

number,
delItemPedido,
setDescuento,
setDescuentoUnitario,
setDescuentoTotal,
setCantidadCarrito,

toggleAddPersona,
setToggleAddPersona,

getPersona,
personas,
setPersonas,

ModaladdproductocarritoToggle,
setModaladdproductocarritoToggle,
toggleModalProductos,

setProductoCarritoInterno,

toggleImprimirTicket,
onchangeinputmain,
del_pedido,

productos,
getProductos,
facturar_pedido,
inputmodaladdpersonacarritoref,
inputaddcarritointernoref,

tbodyproducInterref,
tbodypersoInterref,

countListInter,
countListPersoInter,

clickSetOrderColumn,
orderColumn,
orderBy,
entregarVuelto,

setPersonaFast,
clienteInpidentificacion,
setclienteInpidentificacion,
clienteInpnombre,
setclienteInpnombre,
clienteInptelefono,
setclienteInptelefono,
clienteInpdireccion,
setclienteInpdireccion,
inputaddCarritoFast,
setinputaddCarritoFast,
refinputaddcarritofast,

viewReportPedido,
autoCorrector,
setautoCorrector,

getDebito,
getCredito,
getTransferencia,
getEfectivo,
onClickEditPedido,

facturar_e_imprimir,

moneda,

dolar,
peso,

showinputaddCarritoFast,
setshowinputaddCarritoFast,
qProductosMain,
tipoTransferenciaPagar,
settipoTransferenciaPagar
}) {

  const [vuelto_penddolar,setvuelto_penddolar] = useState(0)
  const [vuelto_pendbs,setvuelto_pendbs] = useState(0)
  const [vuelto_pendcop,setvuelto_pendcop] = useState(0)
  const debitoBs = (met) =>{
    try{
      if (met=="debito") {
        if (debito=="") {
          return ""
        }
       return "Bs."+moneda(dolar*debito)

      }

      if (met=="transferencia") {
        if (transferencia=="") {
          return ""
        }
       return "Bs."+moneda(dolar*transferencia)
        
      }

    }catch(err){
      return ""
      console.log()
    }
  }

  const showTittlePrice = (pu,total) => {
    try{
      return "P/U. Bs."+moneda(number(pu)*dolar)+"\n"+"Total Bs."+moneda(number(total)*dolar)

    }catch(err){
      return ""
    }
  }
  
  const syncPago = (val,type)=>{
    val = number(val)
    if (type=="Debito") {

      setDebito(val)
    }
    else if (type=="Efectivo") {
      setEfectivo(val) 
    }
    else if (type=="Transferencia") {
      setTransferencia(val)
    }
    else if (type=="Credito") {
      setCredito(val)
    }


    let divisor=0;

    let inputs = [
      {key:"Debito", val:debito, set:(val)=>setDebito(val)},
      {key:"Efectivo", val:efectivo, set:(val)=>setEfectivo(val)},
      {key:"Transferencia", val:transferencia, set:(val)=>setTransferencia(val)},
      {key:"Credito", val:credito, set:(val)=>setCredito(val)},
    ]

    inputs.map(e => {
      if (e.key!=type) {
        if (e.val) {divisor++}
      }
    })

    if (autoCorrector) {
      inputs.map(e => {
        if (e.key!=type) {
          if (e.val) {
            e.set(((pedidoData.clean_total-val)/divisor).toFixed(2))
          }
        }
      })
    }
  }

  
  useEffect(()=>{
    if (refinputaddcarritofast.current) {
      refinputaddcarritofast.current.value = ""

    }
    // refinputaddcarritofast.current.focus()
  },[])
  try{
    const {
      id,
      created_at,
      cliente,
      items,
      total_des,
      subtotal,
      total,

      clean_total,
      cop_clean,
      bs_clean,
      
      total_porciento,
      cop,
      bs,
      editable,
      vuelto_entregado,
      estado,

      exento,
      gravable,
      ivas,
      monto_iva,
    } = pedidoData

    const setvueltopend = e => {
    let type = e.currentTarget.attributes["data-type"].value
    let billete = window.prompt("Billete")

    if (billete) {
      if (parseFloat(billete)) {
        let valorbillete = 0
        switch(type){
          case "dolar":
              valorbillete = moneda((billete-total))
              setvuelto_penddolar(valorbillete)
                        
          break;

          case "bs":
              valorbillete = moneda((billete-bs_clean))
              setvuelto_pendbs(valorbillete)
            
          break;

          case "cop":

            valorbillete = moneda((billete-cop_clean))
            setvuelto_pendcop(valorbillete)
            
          
          break;
        }
      }
    }


    return;
  }
    return (
      <>
        {viewconfigcredito?
          <Modalconfigcredito
            pedidoData={pedidoData}
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
          />
        :null}
        <div className="container-fluid">
          <div className="row">
            <div className="col-md-auto p-0">
                
                {pedidosFast?pedidosFast.map(e=>
                  e?
                    <div className="card-pedidos d-flex justify-content-center flex-column" key={e.id} data-id={e.id} onClick={onClickEditPedido}>
                      <h3>
                        <span className={(e.id==id?"btn":"btn-outline")+(!e.estado?"-sinapsis":"-success")+(" fs-4 btn btn-xl btn-circle f")}>
                          {e.id}
                        </span>
                      </h3>
                      <span className="text-muted text-center">
                          <b className={("h5 ")+(!e.estado?" text-sinapsis":" text-success")}></b>

                      </span>
                    </div>
                  :null
                ):null} 
            </div>
            <div className="col">
              
              {ModaladdproductocarritoToggle&&<Modaladdproductocarrito 
                qProductosMain={qProductosMain}
                showinputaddCarritoFast={showinputaddCarritoFast}
                setshowinputaddCarritoFast={setshowinputaddCarritoFast}

                toggleModalProductos={toggleModalProductos}
                productos={productos}
                setProductoCarritoInterno={setProductoCarritoInterno}
                getProductos={getProductos}
                inputaddcarritointernoref={inputaddcarritointernoref}

                tbodyproducInterref={tbodyproducInterref}

                countListInter={countListInter}
                onchangeinputmain={onchangeinputmain}

                clickSetOrderColumn={clickSetOrderColumn}
                orderColumn={orderColumn}
                orderBy={orderBy}


              />}

              {toggleAddPersona&&<ModaladdPersona 
                setToggleAddPersona={setToggleAddPersona}
                getPersona={getPersona}
                personas={personas}
                setPersonas={setPersonas}
                inputmodaladdpersonacarritoref={inputmodaladdpersonacarritoref}
                tbodypersoInterref={tbodypersoInterref}
                countListPersoInter={countListPersoInter}

                setPersonaFast={setPersonaFast}
                clienteInpidentificacion={clienteInpidentificacion}
                setclienteInpidentificacion={setclienteInpidentificacion}
                clienteInpnombre={clienteInpnombre}
                setclienteInpnombre={setclienteInpnombre}
                clienteInptelefono={clienteInptelefono}
                setclienteInptelefono={setclienteInptelefono}
                clienteInpdireccion={clienteInpdireccion}
                setclienteInpdireccion={setclienteInpdireccion}
              />}
              <table className="table table-striped text-center">
                <thead>
                  <tr>
                    <th className="text-sinapsis cell2">Código</th>
                    <th className="text-sinapsis cell3">Producto</th>
                    <th className="text-sinapsis cell1">Cant.</th>
                    <th className="text-sinapsis cell1">Precio</th>
                    <th className="text-sinapsis">Desc.%</th>
                    {/*
                    <th className="text-sinapsis">Sub-total</th>
                    <th className="text-sinapsis">Tot.Desc.</th>
                    */}
                    <th className="text-sinapsis cell2">Total</th>
                    {editable?
                    <th className='cell1'><button className="btn btn-circle text-white btn-sinapsis btn-sm" onClick={toggleModalProductos}>F1 <i className="fa fa-plus"></i></button></th>
                    :null}
                  </tr>
                </thead>
                <tbody>
                  {items?items.map((e,i)=>
                    e.abono&&!e.producto?
                    <tr key={e.id}>
                      <td>PAGO</td>
                      <td>{e.abono}</td>
                      <td>{e.cantidad} </td>
                      <td>{e.monto}</td>
                      <td onClick={setDescuentoUnitario} data-index={e.id} className="align-middle pointer clickme">{e.descuento}</td>
                      {/*<td>{e.subtotal}</td>
                      {/*<td>{e.total_des}</td>*/}

                      <th className="font-weight-bold">{e.total}</th>
                      <td> </td>
                    </tr>
                    :<tr key={e.id} title={showTittlePrice(e.producto.precio,e.total)}>
                      <td className="align-middle">{e.producto.codigo_barras}</td>
                      <td className="align-middle">
                        <span className="pointer" onClick={changeEntregado} data-id={e.id}>{e.producto.descripcion}</span> {e.entregado?<span className="btn btn-outline-secondary btn-sm-sm">Entregado</span>:null}
                        <div className='fst-italic fs-6 text-success'>
                            {e.lotedata?<>
                              Lote. {e.lotedata ? e.lotedata.lote : null} - Exp. {e.lotedata ? e.lotedata.vence : null}
                            </>:null} 
                        </div>
                      </td>
                      <td className="pointer clickme align-middle" onClick={setCantidadCarrito} data-index={e.id}>
                        {e.cantidad.replace(".00","")} 
                      </td>
                      {e.producto.precio1?
                      <td className="align-middle text-success pointer" data-iditem={e.id} onClick={setPrecioAlternoCarrito} >{e.producto.precio}</td>
                        :
                      <td className="align-middle pointer">{e.producto.precio}</td>
                      }
                       <td onClick={setDescuentoUnitario} data-index={e.id} className="align-middle pointer">{e.descuento}</td>
                      {/*<td onClick={setDescuentoUnitario} data-index={e.id} className="align-middle pointer clickme">{e.descuento}</td>*/}
                      


                      <th className="font-weight-bold align-middle">{e.total}</th>
                      {editable?
                      <td className="align-middle"> <i onClick={delItemPedido} data-index={e.id} className="fa fa-times text-danger"></i> </td>
                      :null}
                    </tr>
                  ):null}
                  <tr>
                    <td><button className="btn btn-outline-success fs-5">{items?items.length:null}</button></td>
                    <th colSpan="6" className="p-2 align-middle">{cliente?cliente.nombre:null} <b>{cliente?cliente.identificacion:null}</b></th>
                  </tr>
                </tbody>
              </table>
            </div>
          
            
            <div className="col-5">
              <div className={(estado?"bg-success":"bg-sinapsis")+(" text-center p-1")}>
                <h3>Pedido #{id}</h3>
                <h6>{created_at}</h6>
              </div>
              <div className="mt-2 mb-2 container-fluid">
                
                <div className="row">
                  {editable?
                    <>
                      <div className="col p-0">
                        
                        <div className={(debito!=""?"bg-success-light card-sinapsis addref":"t-5")+(" card")}>
                          <div className="card-body">
                            <div className="card-title pointer" onClick={getDebito}>Déb.</div>
                            <div className="card-text pago-numero"><input type="text" value={debito} onChange={(e)=>syncPago(e.target.value,"Debito")} placeholder="D"/></div>
                            <small className="text-muted fs-4">{debitoBs("debito")}</small>
                                                      
                          </div>
                        </div>
                      </div>
                      <div className="col p-0">
                        
                        <div className={(efectivo!=""?"bg-success-light card-sinapsis addref":"t-5")+(" card")}>
                          <div className="card-body">
                            <div className="card-title pointer" onClick={getEfectivo}>Efec.</div>
                            <div className="card-text pago-numero"><input type="text" value={efectivo} onChange={(e)=>syncPago(e.target.value,"Efectivo")} placeholder="E"/></div>
                            
                          </div>
                        </div>
                      </div>

                      <div className="col p-0">
                        
                        <div className={(transferencia!=""?"bg-success-light card-sinapsis addref":"t-5")+(" card")}>
                          <div className="card-body">
                            <div className="card-title pointer" onClick={getTransferencia}>Tran.</div>
                            <div className="card-text pago-numero"><input type="text" value={transferencia} onChange={(e)=>syncPago(e.target.value,"Transferencia")} placeholder="T"/></div>
                            <small className="text-muted fs-4">{debitoBs("transferencia")}</small>
                            <select value={tipoTransferenciaPagar} onChange={e=>addRefPago(e.target.value)} id="" className="form-control form-control-sm">
                              <option value="">-BANCO-</option>
                              <option value="BIOPAGO">BIOPAGO</option>
                              <option value="0134 Banesco">0134 Banesco</option>
                              <option value="0102 Banco de Venezuela">0102 Banco de Venezuela</option>
                              <option value="0105 Banco Mercantil">0105 Banco Mercantil</option>
                              <option value="0108 BBVA Provincial">0108 BBVA Provincial</option>
                              <option value="0114 Bancaribe">0114 Bancaribe</option>
                              <option value="0172 Bancamiga">0172 Bancamiga</option>
                              <option value="0163 Banco Del Tesoro">0163 Banco Del Tesoro</option>
                              <option value="0104 Banco Venezolano de Crédito">0104 Banco Venezolano de Crédito</option>
                              <option value="0115 Banco Exterior">0115 Banco Exterior</option>
                              <option value="0128 Banco Caroní">0128 Banco Caroní</option>
                              <option value="0137 Banco Sofitasa">0137 Banco Sofitasa</option>
                              <option value="0138 Banco Plaza">0138 Banco Plaza</option>
                              <option value="0146 Bangente">0146 Bangente</option>
                              <option value="0151 BFC Banco Fondo Común">0151 BFC Banco Fondo Común</option>
                              <option value="0156 100% Banco">0156 100% Banco</option>
                              <option value="0157 DELSUR Banco Universal">0157 DELSUR Banco Universal</option>
                              <option value="0166 Banco Agrícola de Venezuela">0166 Banco Agrícola de Venezuela</option>
                              <option value="0168 Bancrecer">0168 Bancrecer</option>
                              <option value="0169 Mi Banco">0169 Mi Banco</option>
                              <option value="0171 Banco Activo">0171 Banco Activo</option>
                              <option value="0173 Banco Internacional de Desarrollo">0173 Banco Internacional de Desarrollo</option>
                              <option value="0174 Banplus">0174 Banplus</option>
                              <option value="0175 Banco Bicentenario del Pueblo">0175 Banco Bicentenario del Pueblo</option>
                              <option value="0177 Banfanb">0177 Banfanb</option>
                              <option value="0190 Citibank">0190 Citibank</option>
                              <option value="0191 Banco Nacional de Credito">0191 Banco Nacional de Credito</option>
                            </select>
                            
                          </div>
                        </div>
                      </div>

                      <div className="col p-0">
                        
                        <div className={(credito!=""?"bg-success-light card-sinapsis":"t-5")+(" card")}>
                          <div className="card-body">
                            <div className="card-title pointer" onClick={getCredito}>Créd.</div>
                            <div className="card-text pago-numero"><input type="text" value={credito} onChange={(e)=>syncPago(e.target.value,"Credito")} placeholder="C"/></div>
                            
                          </div>
                        </div>
                      </div>
                    </>:<>
                      
                      <div className="col p-0">
                        
                        <div className={(debito!=""?"bg-success-light card-sinapsis":"t-5")+(" card")}>
                          <div className="card-body">
                            <div className="card-title pointer">Déb.</div>
                            <div className="card-text pago-numero">{debito}</div>
                            
                          </div>
                        </div>
                      </div>
                      <div className="col p-0">
                        
                        <div className={(efectivo!=""?"bg-success-light card-sinapsis":"t-5")+(" card")}>
                          <div className="card-body">
                            <div className="card-title pointer">Efec.</div>
                            <div className="card-text pago-numero">{efectivo}</div>
                            
                          </div>
                        </div>
                      </div>

                      <div className="col p-0">
                        
                        <div className={(transferencia!=""?"bg-success-light card-sinapsis":"t-5")+(" card")}>
                          <div className="card-body">
                            <div className="card-title pointer">Tran.</div>
                            <div className="card-text pago-numero">{transferencia}</div>
                            
                          </div>
                        </div>
                      </div>

                      <div className="col p-0">
                        
                        <div className={(credito!=""?"bg-success-light card-sinapsis":"t-5")+(" card")}>
                          <div className="card-body">
                            <div className="card-title pointer">Créd.</div>
                            <div className="card-text pago-numero">{credito}</div>
                            
                          </div>
                        </div>
                      </div>
                    </>
                  }

                  <div className="col p-0">
                    
                    <div className={(vuelto!=""?"card-danger-pago":"t-5")+(" card pointer")}>
                      <div className="card-body">
                        <div className="card-title">Vuel.</div>
                        {
                          editable?
                          <div className="card-text pago-numero">
                            <input type="text" value={vuelto} onChange={(e)=>setVuelto(number(e.target.value))} placeholder="V"/>
                          </div>
                          :
                          <div onClick={entregarVuelto}>
                            <div className="card-text pago-numero">                
                              {vuelto}
                            </div>
                              <small className="text-success fst-italic pointer">Entregar</small><br/>
                            {vuelto_entregado?vuelto_entregado.map(e=><div title={e.created_at} key={e.id}>
                              Entregado = <b>{e.monto}</b>
                              
                            </div>):null}
                          </div>
                        }
                      </div>
                    </div>
                  </div>
                </div>

              </div>
              {editable?
                <div className="container p-0 m-0">
                  <div className="row">
                    <div className="col">
                      <ul className="list-group">
                        {refPago ? refPago.length ? refPago.map(e=>
                          <li key={e.id} className='list-group-item d-flex justify-content-between align-items-start'>
                            <span className='cell45'>Ref.{e.descripcion}</span>
                            {e.tipo==1&&e.monto!=0?<span className="cell45 btn-sm btn-info btn">Trans. {moneda(e.monto)} ({e.banco}) </span>:null}
	                          {e.tipo==2&&e.monto!=0?<span className="cell45 btn-sm btn-secondary btn">Deb. {moneda(e.monto)} </span>:null}
                            <span className="cell1 text-danger text-right" data-id={e.id} onClick={delRefPago}>
                              <i className="fa fa-times"></i>
                            </span>
                          </li>
                        )
                        :null:null}
                      </ul>

                    </div>
                    <div className="col text-right">
                      {autoCorrector?
                        <button className="btn btn-outline-success btn-sm" onClick={()=>setautoCorrector(false)}>Auto On</button>:
                        <button className="btn btn-outline-danger btn-sm" onClick={()=>setautoCorrector(true)}>Off Auto</button>
                      }
                      
                    </div>

                  </div>
                  


                </div>:null
              }

              <div className="mt-1 mb-1">
                <table className="table table-sm">
                  <tbody>
                    <tr className='hover'>
                      <th className="">Sub-Total</th>
                      <td colSpan="2" className="text-right">{subtotal}</td>
                    </tr>
                    <tr className='hover'>
                      <th data-index={id} onClick={setDescuentoTotal} className="pointer clickme">Desc. {total_porciento}%
                      </th>
                      <td colSpan="2" className="text-right">{total_des}</td>
                    </tr>
                    <tr className='hover'>
                      <th className="">Monto Exento</th>
                      <td colSpan="2" className="text-right">{exento}</td>
                    </tr>
                    <tr className='hover'>
                      <th className="">Monto Gravable</th>
                      <td colSpan="2" className="text-right">{gravable}</td>
                    </tr>
                    <tr className='hover'>
                      <th className="">IVA <span>({ivas})</span></th>
                      <td colSpan="2" className="text-right">{monto_iva}</td>
                    </tr>
                    <tr className="hover h4">
                      <th className="">Total</th>
                      <td className="text-left text-muted align-bottom">
                        {vuelto_penddolar?<>
                          <span>Vuelto: {vuelto_penddolar}</span>
                        </>:null}
                      </td>
                      <td className="text-right text-success fw-bold fs-11">
                        <span onClick={setvueltopend} data-type="dolar" className="pointer">{total}</span>
                      </td>
                    </tr>

                    <tr className="text-muted">
                      <td></td>
                      <td className="text-left text-muted">
                        {vuelto_pendbs?<>
                          <span className="fs-2">Vuelto: {vuelto_pendbs}</span><br/>
                        </>:null}
                        {vuelto_pendcop?<>
                          <span className="fs-5">Vuelto: {vuelto_pendcop}</span>
                        </>:null}
                      </td>
                      <th className="text-right" colSpan="2">
                        <span onClick={setvueltopend} data-type="bs" className='fs-2 pointer'> Bs {bs}</span><br/>
                        <span onClick={setvueltopend} data-type="cop" className='fs-5 pointer'>COP {cop}</span>
                      </th>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div className="d-flex justify-content-center">
              </div>
              <div className="d-flex justify-content-center p-2">
                
                <div className="">
                  {editable?
                    <>
                    <button className="btn btn-circle text-white btn-success btn-xl me-1" onClick={facturar_pedido}>ENTER <i className="fa fa-paper-plane"></i></button>

                    <button className="btn btn-circle btn-primary text-white btn-xl me-5" onClick={facturar_e_imprimir}> 
                      CL+ETR<i className="fa fa-paper-plane"></i>
                      <i className="fa fa-print"></i>
                    </button>
                    </>
                  :null}
                  {editable?
                  <button className="btn btn-circle text-white btn-sinapsis btn-xl me-1" onClick={()=>setToggleAddPersona(true)}>F2 <i className="fa fa-user"></i></button>
                  :null}
                  <button className="btn btn-circle text-white btn-sinapsis btn-xl me-4" onClick={toggleImprimirTicket}>F3 <i className="fa fa-print"></i></button>
                  <button className="btn btn-circle text-white btn-sinapsis btn-xl me-4" onClick={viewReportPedido}>F4 <i className="fa fa-eye"></i></button>
                  {editable?
                  <button className="btn btn-circle text-white btn-danger btn-sm" onClick={del_pedido}>F5 <i className="fa fa-times"></i></button>
                  :null}
                </div>
              </div>
            </div>
            
          </div>
        </div>
      </>

      
    )
  }catch(err){
    console.log(err)
    return ""

  }
}