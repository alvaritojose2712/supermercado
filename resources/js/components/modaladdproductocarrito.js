function Modaladdproductocarrito({
  countListInter,
  tbodyproducInterref,
  toggleModalProductos,
  productos,
  getProductos,
  setProductoCarritoInterno,
  inputaddcarritointernoref,

  clickSetOrderColumn,
  orderColumn,
  orderBy,
  onchangeinputmain,

  showinputaddCarritoFast,
  setshowinputaddCarritoFast,
  qProductosMain,
}) {

  return (
    <>
      <section className="modal-custom"> 
        <div className="text-danger" onClick={()=>toggleModalProductos(false)}><span className="closeModal">&#10006;</span></div>
        <div className="modal-content modal-cantidad">
         <div className="d-flex justify-content-between">
          <div>
            <h5>Agregar producto</h5>
          </div>
         </div>
         <div>
         <div className="input-group">
          <span className="">
            {/*<button onClick={()=>setshowinputaddCarritoFast(!showinputaddCarritoFast)} className={("btn btn-outline-")+(showinputaddCarritoFast?"success":"sinapsis")}>Agg. rápido</button>*/}
            
            </span>
            <input type="text" className="form-control" placeholder="Buscar..." ref={inputaddcarritointernoref} onChange={e=>getProductos(e.target.value)}/>
           
         </div>
         </div>
         <table className="table table-bordered tabla_datos">
            <thead>
              <tr>
                <th className="cell2 pointer" 
                data-valor="codigo_proveedor" 
                onClick={clickSetOrderColumn}>Cod. 
                  {orderColumn=="codigo_proveedor"?(<i className={orderBy=="desc"?"fa fa-arrow-up":"fa fa-arrow-down"}></i>):null}
                </th>
                <th className="cell4 pointer" 
                data-valor="descripcion" 
                onClick={clickSetOrderColumn}>Desc. 
                  {orderColumn=="descripcion"?(<i className={orderBy=="desc"?"fa fa-arrow-up":"fa fa-arrow-down"}></i>):null}
                </th>
                <th className="cell1 pointer" 
                data-valor="cantidad" 
                onClick={clickSetOrderColumn}>Disp. 
                  {orderColumn=="cantidad"?(<i className={orderBy=="desc"?"fa fa-arrow-up":"fa fa-arrow-down"}></i>):null}
                </th>
                <th className="cell1 pointer" 
                data-valor="unidad" 
                onClick={clickSetOrderColumn}>Unidad 
                  {orderColumn=="unidad"?(<i className={orderBy=="desc"?"fa fa-arrow-up":"fa fa-arrow-down"}></i>):null}
                </th>
                <th className="cell2 pointer" 
                data-valor="precio" 
                onClick={clickSetOrderColumn}>Precio 
                  {orderColumn=="precio"?(<i className={orderBy=="desc"?"fa fa-arrow-up":"fa fa-arrow-down"}></i>):null}
                </th>
              </tr>
            </thead>
            <tbody ref={tbodyproducInterref}>


              {productos.length?productos.map((e,i)=>
                <tr tabIndex="-1" className={(countListInter==i?"bg-select":null)+(' tr-producto ')} key={e.id} onClick={setProductoCarritoInterno} data-index={e.id}>
                  <td className="cell2">{e.codigo_barras}</td>
                  <td className='text-left pl-5 cell4'>{e.descripcion}</td>
                  <td className="cell1">
                    <a href='#' className='formShowProductos btn btn-sinapsis btn-sm'>{e.cantidad}</a>         
                  </td>
                  <td className="cell1">{e.unidad}</td>
                  <td className="cell2">
                    <div className='btn-group w-75'>
                        <button type="button" className='m-0 btn btn-success text-light fs-4 fw-bold'>{e.precio}</button>
                        <button type="button" className='m-0 btn btn-secondary text-light'>BsS. {e.bs}</button>
                    </div>
                    <div className='btn-group w-75'>
                        <button type="button" className='m-0 btn btn-secondary text-light'>Cop. {e.cop}</button>
                    </div>
                  </td>
                </tr>
                ):null}
            </tbody>
          </table>


   

        </div>
      </section>
      <div className="overlay"></div>
    </>

    
  )
}
export default Modaladdproductocarrito