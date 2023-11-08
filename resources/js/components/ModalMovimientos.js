import { moneda } from "./assets";

export default function ModalMovimientos({
  agregarProductoDevolucionTemporal,
  devolucionSalidaEntrada,
  setdevolucionSalidaEntrada,
  devolucionTipo,
  setdevolucionTipo,
  devolucionCt,
  setdevolucionCt,
  devolucionMotivo,
  setdevolucionMotivo,
  prodTempoDevolucion,
  setprodTempoDevolucion,
  getMovimientos,
  setShowModalMovimientos,
  showModalMovimientos,

  setBuscarDevolucion,
  buscarDevolucion,

  setTipoMovMovimientos,
  tipoMovMovimientos,
  
  setTipoCatMovimientos,
  tipoCatMovimientos,

  productosDevolucionSelect,

  idMovSelect,
  setIdMovSelect,

  movimientos,

  delMov,
  movimientosList,
  setFechaMovimientos,
  fechaMovimientos,


  setToggleAddPersona,
  getPersona,
  personas,
  setPersonas,
  setPersonaFastDevolucion,
  clienteInpidentificacion,
  setclienteInpidentificacion,
  clienteInpnombre,
  setclienteInpnombre,
  clienteInptelefono,
  setclienteInptelefono,
  clienteInpdireccion,
  setclienteInpdireccion,


  createDevolucion,
  setpagoDevolucion,
  setDevolucion,
  devolucionselect,
  menuselectdevoluciones,
  setmenuselectdevoluciones,

  clienteselectdevolucion,
  setclienteselectdevolucion,
  productosselectdevolucion,
  setproductosselectdevolucion,
  pagosselectdevolucion,
  setpagosselectdevolucion,

  pagosselectdevolucionmonto,
  setpagosselectdevolucionmonto,
  pagosselectdevoluciontipo,
  setpagosselectdevoluciontipo,
  sethandlepagosselectdevolucion,

  handleproductosselectdevolucion,
  sethandleproductosselectdevolucion,
  number,

  delpagodevolucion,
  delproductodevolucion,

  devolucionsumentrada,
  devolucionsumsalida,
  devolucionsumdiferencia,

  setbuscarDevolucionhistorico,
  buscarDevolucionhistorico,
  productosDevolucionSelecthistorico,

}) {


  const retCat = cat => {
    switch(cat){
      case 1:
        return "Garantía"
      break;

      case 2:
        return "Cambio"
      break;
    }
  }

  const retPago = cat => {
    switch(cat){
      case '3':
        return "Efectivo"
      break;
      case '1':
        return "Transferencia"
      break;
      case '2':
        return "Débito"
      break;
      case '5':
        return "Biopago"
      break;
    }
  }
  
  
  return (
    <div>
      <h2 className="text-center">Devoluciones / Garantías   {devolucionselect?"#".devolucionselect:null}</h2>

      <div className="container-fluid">
        <div className="row">
          <div className="col shadow">
            <div className="btn-group mb-1">
              <button className={(menuselectdevoluciones=="cliente"?"btn-":"btn-outline-")+("sinapsis btn")} onClick={()=>setmenuselectdevoluciones("cliente")}>Agregar Cliente {clienteselectdevolucion?" #"+clienteselectdevolucion:null}</button>
              <button className={(menuselectdevoluciones=="inventario"?"btn-":"btn-outline-")+("sinapsis btn")} onClick={()=>setmenuselectdevoluciones("inventario")}>Agregar Productos</button>
              <button className={(menuselectdevoluciones=="buscar"?"btn-":"btn-outline-")+("sinapsis btn")} onClick={()=>setmenuselectdevoluciones("buscar")}>Histórico</button>
            </div>
            {menuselectdevoluciones=="cliente"?
              <>
                <div>
                  <input type="text" className="form-control" placeholder="Buscar..." onChange={(val)=>getPersona(val.target.value)}/>
                </div>

                <table className="table">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>CÉDULA / RIF</th>
                      <th>NOMBRE Y APELLIDO</th>
                    </tr>
                  </thead>
                  <tbody>
                    {personas.length?personas.map((e,i)=>
                      <tr className={(clienteselectdevolucion==e.id?"":null)+(' tr-producto pointer')} key={e.id} onClick={()=>{setclienteselectdevolucion(e.id);setmenuselectdevoluciones("inventario")}} data-index={e.id}>
                        <td>{e.id}</td>
                        <td>{e.identificacion}</td>
                        <td data-index={i}>{e.nombre}</td>
                      </tr>
                      ):null}

                    {!personas.length?<tr>
                      <td colSpan="3">
                        <form onSubmit={setPersonaFastDevolucion} className="m-3">
                          <div className="form-group">
                              <label htmlFor="">
                                C.I./RIF
                              </label> 
                                <input type="text" 
                                value={clienteInpidentificacion} 
                                onChange={e=>setclienteInpidentificacion(e.target.value)} 
                                className="form-control"/>
                            </div>

                            <div className="form-group">
                              <label htmlFor="">
                                Nombres y Apellidos
                              </label> 
                                <input type="text" 
                                value={clienteInpnombre} 
                                onChange={e=>setclienteInpnombre(e.target.value)} 
                                className="form-control"/>
                            </div>
                            <div className="form-group">
                              <label htmlFor="">
                                Teléfono
                              </label> 
                                <input type="text" 
                                value={clienteInptelefono} 
                                onChange={e=>setclienteInptelefono(e.target.value)} 
                                className="form-control"/>
                            </div>
                            <div className="form-group">
                              <label htmlFor="">
                                Dirección
                              </label> 
                                <input type="text" 
                                value={clienteInpdireccion} 
                                onChange={e=>setclienteInpdireccion(e.target.value)} 
                                className="form-control"/>
                            </div>
                            <div className="form-group mt-2">
                              <button className="btn btn-outline-success btn-block" type="submit">Guardar</button>
                            </div>
                        </form>
                      </td>
                    </tr>:null}
                  </tbody>
                </table>  
              </>
            :null}
            {menuselectdevoluciones=="inventario"?
              <>  
                <input type="text" className="form-control" placeholder="Buscar..." onChange={e=>setBuscarDevolucion(e.target.value)} value={buscarDevolucion}/>
                
                {Object.keys(prodTempoDevolucion).length !== 0?
                <div className="p-1">
                  <div className="mb-4 mt-4">
                    <h4>{prodTempoDevolucion.descripcion} (<b>{prodTempoDevolucion.codigo_barras}</b>) <i className="fa fa-times text-danger" onClick={()=>setprodTempoDevolucion({})}></i></h4>
                    <h5>{prodTempoDevolucion.codigo_proveedor}</h5>
                  </div>

                  <div className="form-group">
                    <div className="text-center mb-5">
                      <button className={("m-3 btn btn-lg btn-")+(devolucionSalidaEntrada==1?"success":"secondary")} onClick={()=>setdevolucionSalidaEntrada(1)}>Entrada <i className="fa fa-arrow-down"></i></button>
                      <button className={("btn btn-lg btn-")+(devolucionSalidaEntrada==0?"danger":"secondary")} onClick={()=>setdevolucionSalidaEntrada(0)}>Salida <i className="fa fa-arrow-up"></i></button>
                      
                    </div>
                    <div className="mb-5">
                      <div className="input-group">
                        <div className="input-group-prepend">
                          <span className="input-group-text">Cantidad</span>
                        </div>
                        <input type="text" value={devolucionCt} onChange={e=>setdevolucionCt(number(e.target.value))} className="form-control" placeholder="Cantidad" />
                      </div>
                    </div>
                    <div className="mb-5">
                      <h5><b>Tipo de movimiento</b></h5>
                      <div className="text-center mb-2">
                        <button className={("btn btn-lg btn-")+(devolucionTipo==2?"warning":"secondary")} onClick={()=>setdevolucionTipo(2)}>Cambio</button>
                        <button className={("m-3 btn btn-lg btn-")+(devolucionTipo==1?"warning":"secondary")} onClick={()=>setdevolucionTipo(1)}>Garantía</button>
                      </div>
                    </div>

                    {devolucionSalidaEntrada==1?
                      <div className="">
                        <h5><b>Motivo / Estado del Producto</b></h5>
                        <textarea className="form-control" value={devolucionMotivo} onChange={e=>setdevolucionMotivo(e.target.value)} placeholder="Motivo, razón o circunstancia detallada del movimiento. Falla o problema del producto"> 

                        </textarea>
                      </div>
                    :null}
                  </div>

                  {devolucionTipo&&devolucionSalidaEntrada!==null&&devolucionCt?<div className="mt-4 mb-3 text-center">
                    {devolucionSalidaEntrada==1&&!devolucionMotivo?null:
                      <button className="btn btn-success" onClick={agregarProductoDevolucionTemporal}>Agregar Producto</button>
                    }
                  </div>:null}
                </div>
                :
                  <table className="table">
                    <tbody>
                      {productosDevolucionSelect.length?productosDevolucionSelect.map(e=>
                        <tr key={e.id} data-id={e.id} className="pointer" onClick={sethandleproductosselectdevolucion}>
                          <td>{e.codigo_barras}</td>
                          <td>{e.codigo_proveedor}</td>
                          <td>{e.descripcion}</td>
                          <td>Ct. {e.cantidad}</td>
                          <td>P/U. {e.precio}</td>
                        </tr>
                      ):null}
                    </tbody>
                  </table>
                }
              </>
            :null}
            {menuselectdevoluciones=="buscar"?
              <>  
                <h5>Histórico</h5>
                <input type="text" className="form-control" placeholder="Buscar..." onChange={e=>setbuscarDevolucionhistorico(e.target.value)} value={buscarDevolucionhistorico}/>
                
                <table className="table">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>CLIENTE</th>
                      <td>Producto</td>
                    </tr>
                  </thead>
                  <tbody>
                    {productosDevolucionSelecthistorico.length?productosDevolucionSelecthistorico.map(e=>
                      <tr key={e.id} data-id={e.id} className="pointer">
                        <td>{e.id}</td>
                        <td>{e.cliente?e.cliente.identificacion:null}</td>
                        <td>
                          <table className="table">
                            <thead>
                              <tr>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Categoría</th>
                                <th>Tipo</th>
                              </tr>
                            </thead>
                            <tbody>
                              {e.items?e.items.map(ee=>
                                <tr className="" key={ee.id}>
                                  <td>{ee.producto.descripcion}</td>
                                  <td>{ee.producto.precio}</td>
                                  <td>{ee.cantidad}</td>
                                  <td>{retCat(ee.categoria)}</td>
                                  <td>
                                    {(ee.tipo==1?
                                    <button className="btn btn-circle text-white btn-success btn-sm me-1" title="Entrada" ><i className="fa fa-arrow-down"></i></button>
                                      :
                                    <button className="btn btn-circle text-white btn-danger btn-sm me-1" title="Salida"><i className="fa fa-arrow-up"></i></button>
                                    )}
                        
                                  </td>
                                </tr>  
                              ):null}
                            </tbody>
                          </table>
                        </td>
                        
                      </tr>
                    ):null}
                  </tbody>
                </table>
              </>
            :null}
          </div>
          <div className="col-6">
            <div className="container-fluid">
              <div className="row">
                <div className="col shadow">
                  <h5>Productos seleccionados / Cliente ID {clienteselectdevolucion}</h5>
                  
                  <table className="table">
                    <tbody>
                      <tr>
                        <td></td>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Destino de producto</th>
                        <td></td>

                      </tr>
                        {productosselectdevolucion.filter(e=>e.tipo==1).map(e=>
                          <tr key={e.idproducto}>
                            <td><button className="btn btn-circle text-white btn-success btn-sm" title="Entrada" ><i className="fa fa-arrow-down"></i></button></td>
                            <td>{e.descripcion} ({e.codigo})</td>
                            <td>{e.cantidad}</td>
                            <td>{e.precio}</td>
                            <td>{retCat(e.categoria)}</td>
                            <td><i className="fa fa-times text-danger" onClick={()=>delproductodevolucion(e.idproducto)}></i></td>
                            
                          </tr>
                        )}
                        {productosselectdevolucion.filter(e=>e.tipo==0).map(e=>
                          <tr key={e.idproducto}>
                            <td><button className="btn btn-circle text-white btn-danger btn-sm" title="Salida" ><i className="fa fa-arrow-up"></i></button></td>
                            <td>{e.descripcion}  ({e.codigo})</td>
                            <td>{e.cantidad}</td>
                            <td>{e.precio}</td>
                            <td>{retCat(e.categoria)}</td>
                            <td><i className="fa fa-times text-danger" onClick={()=>delproductodevolucion(e.idproducto)}></i></td>

                          </tr>
                        )}
                    </tbody>
                  </table>

                  <table className="table">
                    <thead>
                      <tr>
                        <th>Total Entrada <i className="fa fa-arrow-down fa-2x text-success"></i></th>
                        <th>Total Salida <i className="fa fa-arrow-up fa-2x text-danger"></i></th>
                        <th>Diferencia</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td className="fs-3">{moneda(devolucionsumentrada())}</td>
                        <td className="fs-3">{moneda(devolucionsumsalida())}</td>
                        <td className="fs-2">
                          $ {moneda(devolucionsumdiferencia().dolar)}
                          <br />
                          Bs {moneda(devolucionsumdiferencia().bs)}
                        </td>
                      </tr>
                      <tr>
                        <td></td>
                        <td></td>
                        <td>
                          <small className="text-muted fst-italic">
                            {devolucionsumdiferencia().dolar>0?"Cliente debe pagar diferencia":null}
                            {devolucionsumdiferencia().dolar<0?"Se debe devolver diferencia al cliente":null}
                            {devolucionsumdiferencia().dolar==0?"Sin diferencia":null}
                          </small>
                        </td>
                      </tr>
                    </tbody>
                  </table>


                </div>
              </div>
              <div className="row">
                  {devolucionsumdiferencia().dolar>0?
                    <div className="col mt-4 shadow">
                      <h5>Pagar diferencia</h5>
                      <div>
                        <div className="form-group">
                          <label htmlFor="">Tipo de pago</label>
                          <select value={pagosselectdevoluciontipo} onChange={e=>setpagosselectdevoluciontipo(e.target.value)} className="form-control">
                            <option value="">--Seleccione--</option>            
                            <option value="3">Efectivo</option>            
                            <option value="1">Transferencia</option>            
                            <option value="2">Débito</option>            
                            <option value="5">Biopago</option>            
                          </select>
                        </div>
                        <div className="form-group">
                          <label htmlFor="">Monto Pago</label>
                          <input value={pagosselectdevolucionmonto} placeholder="Monto en $ a pagar" onChange={e=>setpagosselectdevolucionmonto(number(e.target.value))} className="form-control"/>
                        </div>
                        <button className="btn btn-success mt-1" onClick={sethandlepagosselectdevolucion}>Agregar pago</button>
                      </div>

                      <table className="table">
                        <tbody>
                          {pagosselectdevolucion.map((e,i)=>
                            <tr key={i}>
                              <td>{retPago(e.tipo)}</td>
                              <td>{e.monto}</td>
                              <td><i className="fa fa-times text-danger" onClick={()=>delpagodevolucion(e.tipo)}></i></td>
                              
                            </tr>  
                          )}
                        </tbody>
                      </table>
                    </div>
                  :null}
                  {devolucionsumdiferencia().dolar<0?
                    <div className="col mt-4 shadow">
                      <h5 className="text-right">Devolver diferencia</h5>
                      <div>
                        <div className="form-group">
                          <label htmlFor="">Tipo de pago</label>
                          <select value={pagosselectdevoluciontipo} onChange={e=>setpagosselectdevoluciontipo(e.target.value)} className="form-control">
                            <option value="">--Seleccione--</option>            
                            <option value="3">Efectivo</option>            
                            <option value="1">Transferencia</option>            
                            <option value="2">Débito</option>            
                            <option value="5">Biopago</option>            
                          </select>
                        </div>
                        <div className="form-group">
                          <label htmlFor="">Monto Pago</label>
                          <input value={pagosselectdevolucionmonto} placeholder="Monto en $ a pagar" onChange={e=>setpagosselectdevolucionmonto(number(e.target.value))} className="form-control"/>
                        </div>
                        <button className="btn btn-success mt-1" onClick={sethandlepagosselectdevolucion}>Agregar pago</button>
                      </div>

                      <table className="table">
                        <tbody>
                          {pagosselectdevolucion.map((e,i)=>
                            <tr key={i}>
                              <td>{retPago(e.tipo)}</td>
                              <td>{e.monto}</td>
                              <td><i className="fa fa-times text-danger" onClick={()=>delpagodevolucion(e.tipo)}></i></td>
                              
                            </tr>  
                          )}
                        </tbody>
                      </table>
                    </div>
                  :null}
              </div>
              
            </div>
          </div>
          
        </div>
      </div>
      <div className="mt-4 text-center">
        <button className="btn btn-sinapsis btn-lg" onClick={setDevolucion} disabled={(clienteselectdevolucion&&productosselectdevolucion.length)?false:true}>Guardar{clienteselectdevolucion?(productosselectdevolucion.length?true:(" (Debe agregar un producto)")):" (Debe agregar un cliente)"}</button>
      </div>
    </div>

    
  )
}


/* export default function ModalMovimientos({
  getMovimientos,
  setShowModalMovimientos,
  showModalMovimientos,

  setBuscarDevolucion,
  buscarDevolucion,

  setTipoMovMovimientos,
  tipoMovMovimientos,
  
  setTipoCatMovimientos,
  tipoCatMovimientos,

  productosDevolucionSelect,
  setDevolucion,

  idMovSelect,
  setIdMovSelect,

  movimientos,

  delMov,
  movimientosList,
  setFechaMovimientos,
  fechaMovimientos,
}) {

  const retTipoMov = (type) => (
    <table className="table">
      
      <tbody>
        {productosDevolucionSelect?.length?productosDevolucionSelect.map(e=>
          <tr key={e.id} onClick={setDevolucion} data-id={e.id} data-type={type} className="hover">
            <td>{e.codigo_proveedor}</td>
            <td>{e.descripcion}</td>
            <td>Ct. {e.cantidad}</td>
            <td>P/U. {e.precio}</td>
          </tr>
        ):null}
      </tbody>
    
    </table>
         
  )
 
  const retTipoSubMov = (items,tipo) =>(
      <>
        <table className="table table-sm">
          <thead>
            <tr>
              <th>Cat.</th>
              <th>Prod.</th>
              <th>Precio</th>
              <th>Ct.</th>
              <th>Total</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {items.filter(e=>e.tipo==tipo).map(ee=>
              <tr key={ee.id}>
                <td>{retCat(ee.categoria)}</td>
                <th>{ee.producto.codigo_proveedor} {ee.producto.descripcion}</th>
                <td>{ee.producto.precio}</td>
                <td>{ee.cantidad}</td>
                <td className="">{ee.total}</td>
                <td><i className="fa fa-times text-danger" data-id={ee.id} onClick={delMov}></i></td>
              </tr>)
            }
          </tbody>
        </table>
          
      </>
  )
  
  return (
    <>
      <section className="modal-custom"> 
        <div className="text-danger" onClick={()=>setShowModalMovimientos(!showModalMovimientos)}><span className="closeModal">&#10006;</span></div>
        <div className="modal-content">
          

          <div className="container-fluid">
            <div className="row">
              <div className="col-2">
                <h4>Devoluciones <button className="btn btn-success" onClick={()=>setIdMovSelect("nuevo")}>Nuevo</button></h4>
                <input type="text" className="form-control mb-1" placeholder="Buscar..." onChange={e=>getMovimientos(e.target.value)}/>
                <div className="list-items">
                  {movimientos.length?movimientos.map(e=>
                    <div className={("card-pedidos pointer ")+(e.id==idMovSelect?"bg-sinapsis-light":null)} key={e.id} onClick={()=>setIdMovSelect(e.id)}>Mov. {e.id}</div>

                  ):null}
                </div>
              </div>
              <div className="col">
                <div className="d-flex justify-content-between">
                  <div className="h1">Seleccionado: Mov. {idMovSelect}</div>

                  {
                    movimientos.length&&movimientos.filter(e=>e.id==idMovSelect).length?
                      movimientos.filter(e=>e.id==idMovSelect).map(e=>
                        <div className="h1" key={e.id}>Diff. {e.diff}</div>
                      )
                    :null
                  }
                  
                </div>

                <div className="container-fluid">
                  <div className="row">
                    <div className="col">
                      <div className="header text-center bg-success-super">
                        <h1 onClick={()=>setTipoMovMovimientos(1)}><span className="pointer">Entrada</span> {tipoMovMovimientos==1?
                          <input type="text" className="form-control" placeholder="Buscar..." 
                        onChange={e=>setBuscarDevolucion(e.target.value)}
                        value={buscarDevolucion}
                        />:null}
                        </h1>
                        {buscarDevolucion==""?
                          movimientos.length&&movimientos.filter(e=>e.id==idMovSelect).length?
                            movimientos.filter(e=>e.id==idMovSelect).map(e=>
                              <div key={e.id}>
                                {retTipoSubMov(e.items,1)}
                                <div className="h3">Tot. {e.tot1}</div>

                              </div>
                            )
                          :null
                        :
                          tipoMovMovimientos==1?retTipoMov(1):null
                        }
                      </div>
                    </div>
                  </div>

                  <div className="row">
                    <div className="col">
                      <div className="header text-center bg-danger-super">
                        <h1 onClick={()=>setTipoMovMovimientos(0)}><span className="pointer">Salida</span> {tipoMovMovimientos==0?<input type="text" className="form-control" placeholder="Buscar..." 
                        onChange={e=>setBuscarDevolucion(e.target.value)}
                        value={buscarDevolucion}
                        />
                        :null}
                        </h1>
                        
                        {buscarDevolucion==""?
                          movimientos.length&&movimientos.filter(e=>e.id==idMovSelect).length?
                            movimientos.filter(e=>e.id==idMovSelect).map(e=>
                              <div key={e.id}>
                                {retTipoSubMov(e.items,0)}
                                <div className="h3">Tot. {e.tot0}</div>
                              </div>
                            )
                          :null
                        :
                          tipoMovMovimientos==0?retTipoMov(0):null
                        }
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <div className="overlay"></div>
    </>

    
  )
}
 */