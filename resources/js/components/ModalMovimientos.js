export default function ModalMovimientos({
  getMovimientos,
  setShowModalMovimientos,
  showModalMovimientos,

  setBuscarDevolucion,
  buscarDevolucion,

  setTipoMovMovimientos,
  tipoMovMovimientos,
  
  setTipoCatMovimientos,
  tipoCatMovimientos,

  productosDevulucionSelect,
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
        {productosDevulucionSelect?.length?productosDevulucionSelect.map(e=>
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
  const retCat = cat => {
    switch(cat){
      case '1':
        return "Garantía"
      break;

      case '2':
        return "Cambio"
      break;
    }
  }
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
          {/*<input type="date" value={fechaMovimientos} onChange={e=>setFechaMovimientos(e.target.value)} />
          <table className="table">
            <thead>
              <tr>
                <td colSpan="3">
                  <div className="form-group d-flex justify-content-between">
                    <input type="text" className="form-control" placeholder="Buscar..." 
                    onChange={e=>setBuscarDevolucion(e.target.value)}
                    value={buscarDevolucion}
                    />
                    <select 
                    onChange={e=>setTipoMovMovimientos(e.target.value)}
                    className="form-control" 
                    value={tipoMovMovimientos}>
                      <option value="1">Entrada</option>
                      <option value="0">Salida</option>
                    </select>

                    <select 
                    onChange={e=>setTipoCatMovimientos(e.target.value)}
                    className="form-control" 
                    value={tipoCatMovimientos}>
                      <option value="1">Garantía</option>
                      <option value="2">Cambio</option>
                    </select>


                   

                  </div>
                </td>
              </tr>
              <tr>
                <th>Num. Mov.</th>
                <th>Entrada</th>
                <th>Salida</th>
              </tr>
            </thead>
            <tbody>

              {buscarDevolucion==""?
                retTipoMov()
              :<tr>
                <td>
                  <table className="table">
                    
                  </table>
                </td>
              </tr>}
            </tbody>
          </table>*/}

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
                        <h1 onClick={()=>setTipoMovMovimientos(1)}><span className="pointer">Entrada</span> {tipoMovMovimientos==1?<input type="text" className="form-control" placeholder="Buscar..." 
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
