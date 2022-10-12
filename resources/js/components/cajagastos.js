function Cajagastos({
	setMovimientoCaja,
	movCajadescripcion,
	setMovCajadescripcion,
	movCajamonto,
	setMovCajamonto,
	number,
	setMovCajacategoria,
	movCajacategoria,
	setMovCajatipo,
	movimientosCaja,
	delMovCaja,
	movCajatipo,

	movCajaFecha,
	setMovCajaFecha,

  viewCaja,
  setViewCaja,
}) {
	return (
    <>
  		<div className="modal-custom">
        <div className="text-danger" onClick={()=>setViewCaja(false)}><span className="closeModal">&#10006;</span></div>

        <div className="modal-content">
          <h4>Movimientos de caja</h4>
          <form onSubmit={setMovimientoCaja} >
            <table className="table table-sm table-hoverable">
              <thead> 
                {movCajatipo!==null?
                <>
                	<tr>
                		<td>
                			<input type="date" value={movCajaFecha} onChange={e=>setMovCajaFecha(e.target.value)}/>
                		</td>
                	</tr>
                  <tr>
                    <td>Descripción</td>
                    <td>Monto</td>
                    <td colSpan="2">Categoría</td>
                    <td>Tipo</td>
                    <td></td>
                  </tr>
    	            <tr>
    	              <td><input type="text" required={true} placeholder="Desc." value={movCajadescripcion} onChange={e=>setMovCajadescripcion(e.target.value)} className="form-control"/></td>
    	              <td><input type="text" required={true} placeholder="Monto." value={movCajamonto} onChange={e=>setMovCajamonto(number(e.target.value))} className="form-control"/></td>
    	              <td colSpan="2">
    	                <select onChange={e=>setMovCajacategoria(e.target.value)} value={movCajacategoria} className="form-control">
    	                  <option value="3">Funcionamiento</option>
    	                  <option value="2">Nómina</option>
    	                  <option value="5">Otros</option>
    	                  <option value="6">Devolución</option>
    	                  
    	                </select>
    	              </td>
                    <td>
                      <select className="form-control" value={movCajatipo} onChange={e=>setMovCajatipo(e.target.value)}>
                        <option value="1">Entregado</option>
                        <option value="0">Pendiente</option>
                      </select>
                    </td>
    	              <td>
    	                <button className="btn"><i className="fa fa-send"></i></button>
    	              </td>
    	              
    	            </tr>
                </>
                :<tr></tr>}
              </thead>
              <tbody>
                <tr>
                  <td colSpan="2">
                    <table className="table table-sm table-hoverable">
                      <tbody>
                        {movimientosCaja.length?movimientosCaja.filter(e=>e.tipo==1).map(e=>
                          <tr key={e.id} data-id={e.id} onClick={delMovCaja}>
                            <td>
                              {e.descripcion}
                              <b>
                                {e.categoria==1?" Vueltos":null}
                                {e.categoria==2?" Nómina":null}
                                {e.categoria==3?" Funcionamiento":null}
                                {e.categoria==4?" Pago a proveedores":null}
                                {e.categoria==5?" Otros":null}
                                {e.categoria==6?" Devolución":null}
                              </b>
                              <br/>
                              <span className="text-muted">{e.created_at}</span>
                            </td>
                            <td>{e.monto}</td>
                          </tr>
                        ):null}  
                      </tbody>
                    </table>
                  </td>
                  <td colSpan="3">
                    <table className="table table-sm table-hoverable">
                      <tbody>
                        {movimientosCaja.length?movimientosCaja.filter(e=>e.tipo==0).map(e=>
                          <tr key={e.id} data-id={e.id} onClick={delMovCaja}>
                            <td>
                              {e.descripcion}
                              <b>
                                {e.categoria==1?" Vueltos":null}
                                {e.categoria==2?" Nómina":null}
                                {e.categoria==3?" Funcionamiento":null}
                                {e.categoria==4?" Pago a proveedores":null}
                                {e.categoria==5?" Otros":null}
                                {e.categoria==6?" Devolución":null}
                              </b>
                              <br/>
                              <span className="text-muted">{e.created_at}</span>
                            </td>
                            <td>{e.monto}</td>
                          </tr>
                        ):null}  
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>  
            </table>
          </form>
        </div>
      </div>
      <div className="overlay"></div>
    </>

	)
}

export default Cajagastos