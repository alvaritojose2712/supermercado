import logo from "../../images/logo.png"
import carrito from "../../images/carrito1.png"
function Header({
  user, logout, getip,
  settoggleClientesBtn,toggleClientesBtn,getVentasClick,dolar,peso,view,setView,setMoneda,getPedidos,setViewCaja,viewCaja,setShowModalMovimientos,showModalMovimientos,auth}) {
  
    return (
    <header className="mb-3">
      <div className="container-fluid">
        <div className="row">
          <div className="col">
            <div className="d-flex header-justify-content-end flex-wrap align-items-center">
              <div className="p-3">
                <img src={logo} alt="sinapsis" className="logo" />
              </div>
              <div className="p-3">
                <img src={carrito} alt="sinapsis" width="50px" className="pointer carrito-icon" onClick={
                  ()=>{
                    setView("pedidos")
                    getPedidos()
                  }
                }/>
              </div>
            </div>

          </div>
          <div className="col-5 d-flex header-justify-content-end align-items-center">
            {auth(1)?<span className={"btn m-1 text-success"} onClick={() => setView("configuracion")}><i className="fa fa-cogs"></i></span>:null}
            
            <div onClick={getip}>
              <span className="fw-bold">{user.nombre}</span><br/>
              <span className="fst-italic">{user.role}</span>
            </div>
            <span className="m-1 btn text-danger" onClick={logout}><i className="fa fa-times"></i></span>
          </div>
        </div>
      </div>
      <div className="bg-sinapsis container-fluid">
        <div className="row row-header">
          <div className="col d-flex header-justify-content-end">
            
            {auth(3)?<span className={(view == "ventas" ? "btn btn-dark" : null) + (" p-3 pointer")} onClick={() => { setView("ventas"); getVentasClick()}}>Ventas</span>:null}

              {auth(3) ? <span className={(view == "seleccionar" ? "btn btn-dark" : null) + (" p-3 pointer")} onClick={() => setView("seleccionar")}>Facturar</span> : null}

            {auth(2)?<div className="dropdown align-self-center">
              <button className={(toggleClientesBtn ? "btn btn-dark" : null)+(" btn dropdown-toggle text-light")} type="button" onClick={() => settoggleClientesBtn(!toggleClientesBtn)}>
                Clientes
              </button>
              <ul className={("dropdown-menu ")+ (toggleClientesBtn?"show":null)}>
                <li>
                  <span className={(view == "vueltos" ? "btn btn-dark" : null) + (" p-3 pointer dropdown-item")} onClick={() => {setView("vueltos");settoggleClientesBtn(false)}}>Vueltos</span>
                </li>
                <li>
                  <span className={(view=="credito"?"btn btn-dark":null)+(" p-3 pointer dropdown-item")} onClick={()=>{setView("credito");settoggleClientesBtn(false)}}>Cuentas por cobrar</span>
                </li>
                <li>
                  <span className={(view == "clientes_crud" ? "btn btn-dark" : null) + (" p-3 pointer dropdown-item")} onClick={() => {setView("clientes_crud");settoggleClientesBtn(false)}}>Administrar Clientes</span>

                </li>
              </ul>
            </div>:null}


              {auth(2)?<span className={(view=="cierres"?"btn btn-dark":null)+(" p-3 pointer")} onClick={()=>setView("cierres")}>Cierre</span>:null}
            
            {auth(2)?
            <>
              <small className="p-3 monto-header" onClick={setMoneda} data-type="1">USD {dolar} </small>
              <small className="p-3 monto-header" onClick={setMoneda} data-type="2">COP {peso} </small>
            </>
            :null}
            
          </div>
            <div className="header-right d-flex header-justify-content-end">

            { 
              auth(2)?
                view=="seleccionar"?
                <>
                  <span className={(viewCaja?"btn btn-sinapsis":null)+(" p-3 pointer")} onClick={()=>setViewCaja(!viewCaja)}>Caja</span>
                  <span className={(showModalMovimientos?"btn btn-sinapsis":null)+(" p-3 pointer")} onClick={()=>setShowModalMovimientos(!showModalMovimientos)}>Movimientos</span>

                </>:null
              :null
            }

            {auth(1)?<span className={(view=="inventario"?"btn btn-dark":null)+(" p-3 pointer")} onClick={()=>setView("inventario")}>Administración</span>:null}
          </div>
        </div>
      </div>
    </header>
  )
}
export default Header