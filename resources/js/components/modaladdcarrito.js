export default function ModalAddCarrito({
  dolar,
  moneda,
  number,
  inputCantidadCarritoref,
  producto,
  pedidoList,
  setSelectItem,
  addCarritoRequest,
  cantidad,
  numero_factura,
  setCantidad,
  setNumero_factura,
  setFalla,
  setPresupuesto
}) {
  const setbultocarrito = bulto => {
    let insert = window.prompt("Cantidad por bulto")
    if (insert) {
      let num = number(insert*bulto)
      if (typeof(num)=="number") {
        setCantidad(num)
      }
    }
  }
  return (
    <>
      <section className="modal-custom"> 
        <div className="text-danger" onClick={setSelectItem}><span className="closeModal">&#10006;</span></div>
        <div className="modal-content-sm modal-cantidad">
          <div className="d-flex justify-content-between p-3">
            <span className="text-success fs-2">
              {moneda(producto.precio)}<br/>
              {producto.bulto?<span className="pointer" onClick={()=>setbultocarrito(producto.bulto)}>x{producto.bulto}</span>:null}
            </span>
            <div className="text-right">
              <h5>{producto.codigo_proveedor}</h5>
              <h4>{producto.descripcion}</h4>
            </div>
          </div>
          <form onSubmit={e=>e.preventDefault()} className="d-flex justify-content-center flex-column p-3">
            <div className="input-group mb-3">
              <input type="text" ref={inputCantidadCarritoref} className="form-control fs-2" placeholder="Cantidad" onChange={(e)=>setCantidad(number(e.target.value))} value={cantidad?cantidad:""}/>

              <div className="input-group-append text-right">
                <span className="input-group-text h-100 fs-3 text-right">
                  $. {cantidad*producto.precio?moneda(cantidad*producto.precio):null}<br/>
                  {/*Mayor. {cantidad*producto.precio1?<>{moneda(cantidad*producto.precio1)}</>:null}*/}
                  Bs. {cantidad*producto.precio*dolar?<>{moneda(cantidad*producto.precio*dolar)}</>:null}

                </span>
              </div>
              <div className="input-group-append text-right">

                <span className="input-group-text h-100 fs-3 text-right">
                Ct. {producto.cantidad}
                </span>
              </div>
            </div>
            <div className="input-group mb-3">
              <div className="input-group-prepend">
                <span className="input-group-text">
                  Pedido #
                  <small className="text-muted">(space)</small>
                </span>
              </div>
              <select className="form-control" onChange={(e)=>setNumero_factura(e.target.value)} value={numero_factura}>
                {pedidoList.map((e,i)=>
                  <option value={e.id} key={e.id}>{e.id}</option>
                )}
                  <option value='nuevo'>Nuevo Pedido</option>
              </select>

            </div>
            <div className="btn-group">
              <button className="btn btn-sinapsis agregar_carrito" type="button" onClick={addCarritoRequest} data-type="agregar">Agregar(enter)</button>
              <button className="btn btn-outline-success" type="button" onClick={addCarritoRequest} data-type="agregar_procesar">Procesar(TAB)</button>
              <button className="btn btn-outline-secondary btn-sm" type="button" onClick={setPresupuesto} data-id={producto.id}>Presupuesto</button>
              
            </div>
          </form>
        </div>
      </section>
      <div className="overlay"></div>
    </>

    
  )
}
