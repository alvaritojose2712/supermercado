function Clientes({
qBuscarCliente,
setqBuscarCliente,
clientesCrud,

setindexSelectCliente,
indexSelectCliente,

setClienteCrud,
delCliente,

clienteInpidentificacion,
setclienteInpidentificacion,

clienteInpnombre,
setclienteInpnombre,

clienteInpcorreo,
setclienteInpcorreo,

clienteInpdireccion,
setclienteInpdireccion,

clienteInptelefono,
setclienteInptelefono,

clienteInpestado,
setclienteInpestado,

clienteInpciudad,
setclienteInpciudad

}) {
	const setindexSelectClienteFun = e => {
		let index = e.currentTarget.attributes["data-index"].value
    if (index==indexSelectCliente) {
      setindexSelectCliente(null)
    }else{
      setindexSelectCliente(index)

      if (clientesCrud[index]) {
      	let obj = clientesCrud[index]

      	setclienteInpidentificacion(obj.identificacion?obj.identificacion:"")
				setclienteInpnombre(obj.nombre?obj.nombre:"")
				setclienteInpcorreo(obj.correo?obj.correo:"")
				setclienteInpdireccion(obj.direccion?obj.direccion:"")
				setclienteInptelefono(obj.telefono?obj.telefono:"")
				setclienteInpestado(obj.estado?obj.estado:"")
				setclienteInpciudad(obj.ciudad?obj.ciudad:"")
      }
    }
	}
  const setNuevoCliente = () => {
    setclienteInpidentificacion("")
    setclienteInpnombre("")
    setclienteInpcorreo("")
    setclienteInpdireccion("")
    setclienteInptelefono("")
    setclienteInpestado("")
    setclienteInpciudad("")
    setindexSelectCliente(null)
  }
	return (
    <div className="container">
      <div className="row">
        <div className="col">
          <h1>Administrar Clientes <button className="btn btn-sm btn-success" onClick={setNuevoCliente}>Nuevo</button></h1> 
        </div>
      </div> 
      <div className="row">
        <div className="col">
            <div className="input-group ">
              <input type="text" 
              className="form-control" 
              placeholder="Buscar..." 
              value={qBuscarCliente} 
              onChange={e=>setqBuscarCliente(e.target.value)}/>
              <div className="input-group-prepend">
                <button className="btn btn-outline-secondary" type="button"><i className="fa fa-search"></i></button>
              </div>
            </div>
            
            { 
              clientesCrud.length
              ? clientesCrud.map( (e,i) =>
                <div 
                onClick={setindexSelectClienteFun} 
                data-index={i}
                key={e.id}
                className={(indexSelectCliente==i?"bg-sinapsis":"bg-light text-secondary")+" card mt-2 pointer"}>
                  <div className="card-header flex-row row justify-content-between">
                    <div>
                      <small>ID.{e.id}</small>
                    </div>
                    <div className="d-flex justify-content-between">
                      <div><span>{e.telefono}</span></div>
                      <div><span className="h3">{e.identificacion}</span></div>
                    </div>
                  </div>
                  <div className="card-body">
                    <div className="/personal/vermas">
                      <h5 
                      className="card-title"
                      ><b>{e.nombre}</b></h5>
                    </div> 
                    <p className="card-text">
                    </p>
                  </div>
                </div>
              )
              : <div className='h3 text-center text-dark mt-2'><i>¡Sin resultados!</i></div>
            }
        </div>
        <div className="col">        
          <form className="container-fluid" onSubmit={setClienteCrud}>
            <h3>Cliente</h3>

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
                Email
              </label> 
                <input type="text" 
                value={clienteInpcorreo} 
                onChange={e=>setclienteInpcorreo(e.target.value)} 
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


            <div className="form-group">
              <label htmlFor="">
                Ciudad
              </label> 
                <input type="text" 
                value={clienteInpciudad} 
                onChange={e=>setclienteInpciudad(e.target.value)} 
                className="form-control"/>
            </div>
            <div className="form-group">
              <label htmlFor="">
                Estado
              </label> 
                <input type="text" 
                value={clienteInpestado} 
                onChange={e=>setclienteInpestado(e.target.value)} 
                className="form-control"/>
            </div>



            
            <div className="row">
              <div className="col text-center">
                {indexSelectCliente==null?
                  <button className="btn btn-outline-success btn-block" type="submit">Guardar</button>
                :
                  <div className="btn-group">
                    <button className="btn btn-sinapsis btn-block" type="submit">Editar</button>
                    <button className="btn btn-outline-danger btn-block" onClick={delCliente} type="button"><i className="fa fa-times"></i></button>
                  </div>
                }
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
	)
}

export default Clientes;