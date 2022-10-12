export default function InventarioForzado({
    setporcenganancia,
    productosInventario,
    qBuscarInventario,
    setQBuscarInventario,
    type,

    changeInventario,

    Invnum,
    setInvnum,
    InvorderColumn,
    setInvorderColumn,
    InvorderBy,
    setInvorderBy,
    inputBuscarInventario, 
    guardarNuevoProductoLote,

    proveedoresList,
    number,
    refsInpInvList,
    categorias,
    
    setSameGanancia,
    setSameCat,
    setSamePro,
    sameCatValue,
    sameProValue,
    busquedaAvanazadaInv,
    setbusquedaAvanazadaInv,

    busqAvanzInputsFun,
    busqAvanzInputs,
    buscarInvAvanz,

    setCtxBulto,
    setPrecioAlterno,
}){
    const getPorGanacia = (precio,base) => {
        try{
            let por = 0

            precio = parseFloat(precio)
            base = parseFloat(base)

            let dif = precio-base

            por = ((dif*100)/base).toFixed(2)
            if (por) {
                return (dif<0?"":"+")+por+"%"

            }else{
                return ""

            }
        }catch(err){
            return ""
        }
    } 
    return (
        <div className="container-fluid">
            <div className="row auto-colum">
                <div className="col auto-colum">
                    <div className="container-fluid p-0">
                        <div className="row">
                            <div className="col">
                                {busquedaAvanazadaInv ? <>
                                    <div className="input-group">
                                        <span className="input-group-text cell1">
                                            codigo_barras
                                        </span>
                                        <input type="text" className="form-control cell1" onChange={e => busqAvanzInputsFun(e, "codigo_barras")} value={busqAvanzInputs["codigo_barras"]} placeholder="codigo_barras" />
                                        <span className="input-group-text cell1">
                                            codigo_proveedor
                                        </span>
                                        <input type="text" className="form-control cell1" onChange={e => busqAvanzInputsFun(e, "codigo_proveedor")} value={busqAvanzInputs["codigo_proveedor"]} placeholder="codigo_proveedor" />

                                        <span className="input-group-text cell1">
                                            id_proveedor
                                        </span>

                                        <select
                                            className="form-control cell1"
                                            onChange={e => busqAvanzInputsFun(e, "id_proveedor")} value={busqAvanzInputs["id_proveedor"]}
                                        >
                                            <option value="">--Select--</option>
                                            {proveedoresList.map(e => <option value={e.id} key={e.id}>{e.descripcion}</option>)}

                                        </select>

                                        <span className="input-group-text cell1">
                                            id_categoria
                                        </span>

                                        <select
                                            className="form-control cell1"
                                            onChange={e => busqAvanzInputsFun(e, "id_categoria")} value={busqAvanzInputs["id_categoria"]}
                                        >
                                            <option value="">--Select--</option>
                                            {categorias.map(e => <option value={e.id} key={e.id}>{e.descripcion}</option>)}

                                        </select>

                                        <span className="input-group-text cell1">
                                            unidad
                                        </span>
                                        <input type="text" className="form-control cell1" onChange={e => busqAvanzInputsFun(e, "unidad")} value={busqAvanzInputs["unidad"]} placeholder="unidad" />

                                    </div>
                                    <div className="input-group">
                                        <span className="input-group-text cell1">
                                            descripcion
                                        </span>
                                        <input type="text" className="form-control cell1" onChange={e => busqAvanzInputsFun(e, "descripcion")} value={busqAvanzInputs["descripcion"]} placeholder="descripcion" />
                                        <span className="input-group-text cell1">
                                            iva
                                        </span>
                                        <input type="text" className="form-control cell1" onChange={e => busqAvanzInputsFun(e, "iva")} value={busqAvanzInputs["iva"]} placeholder="iva" />
                                        <span className="input-group-text cell1">
                                            precio_base
                                        </span>
                                        <input type="text" className="form-control cell1" onChange={e => busqAvanzInputsFun(e, "precio_base")} value={busqAvanzInputs["precio_base"]} placeholder="precio_base" />
                                        <span className="input-group-text cell1">
                                            precio
                                        </span>
                                        <input type="text" className="form-control cell1" onChange={e => busqAvanzInputsFun(e, "precio")} value={busqAvanzInputs["precio"]} placeholder="precio" />
                                        <span className="input-group-text cell1">
                                            cantidad
                                        </span>
                                        <input type="text" className="form-control cell1" onChange={e => busqAvanzInputsFun(e, "cantidad")} value={busqAvanzInputs["cantidad"]} placeholder="cantidad" />


                                    </div>
                                </> : null}
                                <div className="input-group">
                                    {busquedaAvanazadaInv ? null :
                                        <input type="text" ref={inputBuscarInventario} className="form-control form-control-lg" placeholder="Buscar...(esc)" onChange={e => setQBuscarInventario(e.target.value)} value={qBuscarInventario} />
                                    }
                                </div>
                            </div>
                            <div className="col-md-auto">
                                <div className="input-group">
                                    <select value={Invnum} onChange={e => setInvnum(e.target.value)} className="form-control">
                                        <option value="25">Num.25</option>
                                        <option value="50">Num.50</option>
                                        <option value="100">Num.100</option>
                                        <option value="500">Num.500</option>
                                        <option value="2000">Num.2000</option>
                                        <option value="10000">Num.100000</option>
                                    </select>
                                    <select value={InvorderBy} onChange={e => setInvorderBy(e.target.value)} className="form-control">
                                        <option value="asc">Orden Asc</option>
                                        <option value="desc">Orden Desc</option>
                                    </select>
                                    {busquedaAvanazadaInv ?
                                        <button className="btn btn-success" onClick={buscarInvAvanz}><i className="fa fa-search"></i></button>

                                        : null}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div className="col-md-auto text-right">
                    <button className="btn btn-success text-light" onClick={guardarNuevoProductoLote}>Guardar (f1)</button>
                </div>
            </div>
            <a href="#" onClick={() => setbusquedaAvanazadaInv(!busquedaAvanazadaInv)}>Búsqueda {busquedaAvanazadaInv ? "sencilla" :"avanazada"}</a>
            <br/>
            <form ref={refsInpInvList} onSubmit={e => e.preventDefault()} className="inventario-pc">
                <table className="table">
                    <thead>
                        <tr>
                            <th className="cell05 pointer"><span onClick={() => setInvorderColumn("id")}>ID</span></th>
                            <th className="cell1 pointer"><span onClick={() => setInvorderColumn("codigo_proveedor")}>C. Alterno</span></th>
                            <th className="cell1 pointer"><span onClick={() => setInvorderColumn("codigo_barras")}>C. Barras</span></th>
                            <th className="cell05 pointer"><span onClick={() => setInvorderColumn("unidad")}>Unidad</span></th>
                            <th className="cell2 pointer"><span onClick={() => setInvorderColumn("descripcion")}>Descripción</span></th>
                            <th className="cell05 pointer"><span onClick={() => setInvorderColumn("cantidad")}>Ct.</span></th>
                            <th className="cell1 pointer"><span onClick={() => setInvorderColumn("precio_base")}>Base</span></th>
                            <th className="cell15 pointer">
                                <span onClick={() => setInvorderColumn("precio")}>Venta </span>
                                <span className="btn btn-outline-success mr-1 btn-sm" onClick={setSameGanancia}>% general <i className="fa fa-coin"></i></span>
                            </th>
                            <th className="cell15 pointer" >
                                <span onClick={() => setInvorderColumn("id_categoria")}>
                                    Categoría
                                </span>
                                <br />
                                    <select
                                        className=""
                                        value={sameCatValue}
                                        onChange={e=>setSameCat(e.target.value)}
                                    >
                                        <option value="">--Select--</option>
                                        {categorias.map(e => <option value={e.id} key={e.id}>{e.descripcion}</option>)}
                                        
                                    </select> 
                                <br/>
                                <span onClick={() => setInvorderColumn("id_proveedor")}>
                                    Preveedor
                                </span>
                                <br />
                                    <select
                                        className=""
                                        value={sameProValue}
                                        onChange={e => setSamePro(e.target.value)}
                                    >
                                        <option value="">--Select--</option>
                                        {proveedoresList.map(e => <option value={e.id} key={e.id}>{e.descripcion}</option>)}

                                    </select> 
                            </th>
                            <th className="cell05 pointer"><span onClick={() => setInvorderColumn("iva")}>IVA</span></th>
                            <th className="cell1"></th>

                        </tr>
                    </thead>
                    <tbody>
                        {productosInventario.length?productosInventario.map((e,i)=>
                            <tr key={i} className="pointer" onDoubleClick={() => changeInventario(null, i, e.id, "update")}>
                                <td className="cell05">
                                    {e.id}
                                </td>
                                {type(e.type)?
                                <>
                                    <td className="cell1">{e.codigo_proveedor}</td>
                                    <td className="cell1">{e.codigo_barras}</td>
                                    <td className="cell05">{e.unidad}</td>
                                    <td className="cell2">{e.descripcion}</td>
                                    <th className="cell05">{e.cantidad}
                                        <br/><span className="btn btn-outline-success btn-sm" 
                                        data-id={e.id} 
                                        onClick={setCtxBulto}>CtxBulto.{e.bulto}</span>
                                    </th>
                                    <td className="cell1">{e.precio_base}</td>
                                    <td className="cell15 text-success">
                                    {e.precio}<br/>
                                    <span className="text-success">
                                        {getPorGanacia(!e.precio?0:e.precio,!e.precio_base?0:e.precio_base)}
                                    </span>
                                        <br/>
                                        <div className="btn-group w-100">
                                            <span className="btn btn-outline-success btn-sm" 
                                            data-id={e.id} 
                                            data-type="p1" 
                                            onClick={setPrecioAlterno}>P1.<br/>{e.precio1}</span>

                                            <span className="btn btn-outline-success btn-sm" 
                                            data-id={e.id} 
                                            data-type="p2" 
                                            onClick={setPrecioAlterno}>P2.<br/>{e.precio2}</span>

                                            

                                        </div>
                                    </td>
                                        <td className="cell15">{e.categoria.descripcion} <br /> {e.proveedor.descripcion}</td>
                                    <td className="cell05">{e.iva}</td>
                                </>

                                :
                                <>
                                    <td className="cell1">
                                        <input type="text"
                                            disabled={type(e.type)} className="form-control form-control-sm"
                                            value={!e.codigo_proveedor?"":e.codigo_proveedor}
                                            onChange={e => changeInventario((e.target.value), i, e.id, "changeInput", "codigo_proveedor")}
                                            placeholder="codigo_proveedor..." />

                                    </td>
                                    <td className="cell1">
                                        <input type="text"
                                            required={true}
                                            disabled={type(e.type)} className={("form-control form-control-sm ")+(!e.codigo_barras?"invalid":null)}
                                            value={!e.codigo_barras?"":e.codigo_barras}
                                            onChange={e => changeInventario((e.target.value), i, e.id, "changeInput", "codigo_barras")}
                                            placeholder="codigo_barras..." />

                                    </td>
                                    <td className="cell05">
                                        <select
                                            disabled={type(e.type)}
                                            className={("form-control form-control-sm ")+(!e.unidad?"invalid":null)}
                                            value={!e.unidad?"":e.unidad}
                                            onChange={e => changeInventario((e.target.value), i, e.id, "changeInput", "unidad")}
                                        >
                                            <option value="">--Select--</option>
                                            <option value="UND">UND</option>
                                            <option value="PAR">PAR</option>
                                            <option value="JUEGO">JUEGO</option>
                                            <option value="PQT">PQT</option>
                                            <option value="MTR">MTR</option>
                                            <option value="KG">KG</option>
                                            <option value="GRS">GRS</option>
                                            <option value="LTR">LTR</option>
                                            <option value="ML">ML</option>
                                        </select>
                                    </td>
                                    <td className="cell2">
                                        <textarea type="text"
                                            required={true}
                                            disabled={type(e.type)} className={("form-control form-control-sm ")+(!e.descripcion?"invalid":null)}
                                            value={!e.descripcion?"":e.descripcion}
                                            onChange={e => changeInventario((e.target.value.replace("\n","")), i, e.id, "changeInput", "descripcion")}
                                            placeholder="descripcion..."></textarea>

                                    </td>
                                    <td className="cell05">
                                            <input type="number"
                                            required={true}
                                            disabled={type(e.type)} className={("form-control form-control-sm ")+(!e.cantidad?"invalid":null)}
                                            value={!e.cantidad?"":e.cantidad}
                                            onChange={e => changeInventario(number(e.target.value), i, e.id, "changeInput", "cantidad")}
                                            placeholder="cantidad..." />

                                    </td>
                                    <td className="cell1">
                                        <input type="number"
                                            required={true}
                                            disabled={type(e.type)} className={("form-control form-control-sm ")+(!e.precio_base?"invalid":null)}
                                            value={!e.precio_base?"":e.precio_base}
                                            onChange={e => changeInventario(number(e.target.value), i, e.id, "changeInput", "precio_base")}
                                            placeholder="Base..." />



                                    </td>
                                    <td className="cell15">
                                        <div className="input-group">
                                                <input type="number"
                                                required={true}
                                                disabled={type(e.type)} className={("form-control form-control-sm ")+(!e.precio?"invalid":null)}
                                                value={!e.precio?"":e.precio}
                                                onChange={e => changeInventario(number(e.target.value), i, e.id, "changeInput", "precio")}
                                                placeholder="Venta..." />
                                            <span className="btn btn-sm" onClick={()=>setporcenganancia("list",e.precio_base,(precio)=>{
                                                    changeInventario(precio, i, e.id, "changeInput", "precio")
                                                })}>%</span>
                                        </div>
                                        <span className="text-success">
                                            {getPorGanacia(!e.precio?0:e.precio,!e.precio_base?0:e.precio_base)}
                                        </span>


                                    </td>
                                    <td className="cell15">
                                        <select
                                            required={true}
                                            disabled={type(e.type)} 
                                            className={("form-control form-control-sm ")+(!e.id_categoria?"invalid":null)}
                                            value={!e.id_categoria?"":e.id_categoria}
                                            onChange={e => changeInventario((e.target.value), i, e.id, "changeInput", "id_categoria")}
                                        >
                                            <option value="">--Select--</option>
                                            {categorias.map(e => <option value={e.id} key={e.id}>{e.descripcion}</option>)}
                                            
                                        </select>
                                        <br/>
                                        <select
                                            required={true}
                                            disabled={type(e.type)}
                                            className={("form-control form-control-sm ")+(!e.id_proveedor?"invalid":null)}
                                            value={!e.id_proveedor?"":e.id_proveedor}
                                            onChange={e => changeInventario((e.target.value), i, e.id, "changeInput", "id_proveedor")}
                                        >
                                            <option value="">--Select--</option>
                                            {proveedoresList.map(e => <option value={e.id} key={e.id}>{e.descripcion}</option>)}

                                        </select>
                                    </td>
                                    <td className="cell05">
                                        <input type="text"
                                            disabled={type(e.type)} className="form-control form-control-sm"
                                            value={!e.iva?"":e.iva}
                                            onChange={e => changeInventario(number(e.target.value,2), i, e.id, "changeInput", "iva")}
                                            placeholder="iva..." />

                                    </td>
                                </>
                                }
                                    <td className="cell1">
                                        <div className='d-flex justify-content-between'>
                                            {!e.type ?
                                                <>
                                                    <span className="btn-sm btn btn-danger" onClick={() => changeInventario(null, i, e.id, "delMode")}><i className="fa fa-trash"></i></span>
                                                    <span className="btn-sm btn btn-warning" onClick={() => changeInventario(null, i, e.id, "update")}><i className="fa fa-pencil"></i></span>
                                                </>
                                                : null}
                                            {e.type === "new" ?
                                                <span className="btn-sm btn btn-danger" onClick={() => changeInventario(null, i, e.id, "delNew")}><i className="fa fa-times"></i></span>
                                                : null}
                                            {e.type === "update" ?
                                                <span className="btn-sm btn btn-warning" onClick={() => changeInventario(null, i, e.id, "delModeUpdateDelete")}><i className="fa fa-times"></i></span>
                                                : null}
                                            {e.type === "delete" ?
                                                <span className="btn-sm btn btn-danger" onClick={() => changeInventario(null, i, e.id, "delModeUpdateDelete")}><i className="fa fa-arrow-left"></i></span>
                                                : null}
                                        </div>
                                    </td>
                                
                            </tr>
                        ):<tr>
                            <td colSpan={7}>Sin resultados</td>
                        </tr>}
                    </tbody>
                </table>
            </form>

            <form onSubmit={e => e.preventDefault()} className="inventario-telefono">
                <div className="mb-4">
                    <div className="filtros-orden">
                        <span className="pointer p-1" onClick={() => setInvorderColumn("id")}>ID</span>
                        <span className="pointer p-1" onClick={() => setInvorderColumn("codigo_proveedor")}>Alterno</span>
                        <span className="pointer p-1" onClick={() => setInvorderColumn("codigo_barras")}>Barras</span>
                        <span className="pointer p-1" onClick={() => setInvorderColumn("unidad")}>Unidad</span>
                        <span className="pointer p-1" onClick={() => setInvorderColumn("descripcion")}>Descripción</span>
                        <span className="pointer p-1" onClick={() => setInvorderColumn("cantidad")}>Ct</span>
                        <span className="pointer p-1" onClick={() => setInvorderColumn("precio_base")}>Base</span>
                        <span className="pointer p-1" onClick={() => setInvorderColumn("iva")}>IVA</span>
                        <span className="pointer p-1" onClick={() => setInvorderColumn("precio")}>Venta</span>
                        <span className="pointer p-1" onClick={() => setInvorderColumn("id_categoria")}>Categoría</span>
                        <span className="pointer p-1" onClick={() => setInvorderColumn("id_proveedor")}>Proveedor</span>
                    </div>

                    <select
                        className="form-control"
                        value={sameCatValue}
                        onChange={e => setSameCat(e.target.value)}
                    >
                        <option value="">--Select Categoría General--</option>
                        {categorias.map(e => <option value={e.id} key={e.id}>{e.descripcion}</option>)}

                    </select>
                    
                    <select
                        className="form-control"
                        value={sameProValue}
                        onChange={e => setSamePro(e.target.value)}
                    >
                        <option value="">--Select Proveedor General--</option>
                        {proveedoresList.map(e => <option value={e.id} key={e.id}>{e.descripcion}</option>)}

                    </select>
                </div>

                {productosInventario.length ? productosInventario.map((e, i) =>
                    <div key={i} className={("card mb-3 shadow p-1 ") + (type(e.type) ? "bg-ivory" :"bg-light-success")} onDoubleClick={() => changeInventario(null, i, e.id, "update")}>
                        {type(e.type) ?
                            <>  
                                <div className=" d-flex justify-content-between">
                                    <div className="">
                                        <div className="fst-italic">{e.codigo_proveedor}</div>
                                        <div className="fst-italic">{e.codigo_barras}</div>
                                    </div>
                                    <div className=" text-right">
                                        <span className="btn btn-outline-success btn-sm"
                                        data-id={e.id}
                                        data-type="p1"
                                        onClick={setPrecioAlterno}>P1. {e.precio1}</span><br />
                                        
                                        <span className="h6 text-muted font-italic">Base. {e.precio_base}</span><br />
                                        <span className="h3 text-success">{e.precio}</span><br/>
                                        <span className="text-success">
                                            {getPorGanacia(!e.precio ? 0 : e.precio, !e.precio_base ? 0 : e.precio_base)}
                                        </span>
                                    </div>
                                </div>
                                <div className=" d-flex justify-content-between">
                                    <div className="">
                                        <span
                                            className="card-title "
                                        ><b>{e.descripcion}</b></span>
                                    </div>
                                    <p className="card-text p-1">
                                        Ct. <b className="h3">{e.cantidad}</b> / {e.unidad} <br/>
                                        <span className="btn btn-outline-success btn-sm"
                                            data-id={e.id}
                                            onClick={setCtxBulto}>CtxBulto.{e.bulto}</span>
                                    </p>
                                </div>
                                <div className="">
                                    <div className=""><small> {e.categoria.descripcion} <br /> {e.proveedor.descripcion}</small></div>
                                    <div className="">IVA {e.iva}</div>
                                </div>
                               
                            </>

                            :
                            <>
                                

                                <div className=" d-flex justify-content-between">
                                    <div className="">
                                        <div className="fst-italic">
                                            Alterno <input type="text"
                                                disabled={type(e.type)} className="form-control form-control-sm"
                                                value={!e.codigo_proveedor ? "" : e.codigo_proveedor}
                                                onChange={e => changeInventario((e.target.value), i, e.id, "changeInput", "codigo_proveedor")}
                                                placeholder="codigo_proveedor..." />    
                                        </div>
                                        <div className="fst-italic">
                                            Barras <input type="text"
                                                required={true}
                                                disabled={type(e.type)} className={("form-control form-control-sm ") + (!e.codigo_barras ? "invalid" : null)}
                                                value={!e.codigo_barras ? "" : e.codigo_barras}
                                                onChange={e => changeInventario((e.target.value), i, e.id, "changeInput", "codigo_barras")}
                                                placeholder="codigo_barras..." />
                                        </div>
                                    </div>
                                    <div className=" text-right p-2">
                                        <span className="btn btn-outline-success btn-sm"
                                            data-id={e.id}
                                            data-type="p1"
                                            onClick={setPrecioAlterno}>P1. {e.precio1}</span><br />

                                        <span className="h6 text-muted font-italic">Base. <input type="number"
                                            required={true}
                                            disabled={type(e.type)} className={("form-control form-control-sm ") + (!e.precio_base ? "invalid" : null)}
                                            value={!e.precio_base ? "" : e.precio_base}
                                            onChange={e => changeInventario(number(e.target.value), i, e.id, "changeInput", "precio_base")}
                                            placeholder="Base..." />
                                        </span><br />
                                        <span className="h3 text-success">
                                            Venta. <input type="number"
                                                required={true}
                                                disabled={type(e.type)} className={("text-right form-control form-control-sm ") + (!e.precio ? "invalid" : null)}
                                                value={!e.precio ? "" : e.precio}
                                                onChange={e => changeInventario(number(e.target.value), i, e.id, "changeInput", "precio")}
                                                placeholder="Venta..." />
                                            
                                        </span>
                                        <span className="text-success" onClick={() => setporcenganancia("list", e.precio_base, (precio) => {
                                            changeInventario(precio, i, e.id, "changeInput", "precio")
                                        })}>
                                            {getPorGanacia(!e.precio ? 0 : e.precio, !e.precio_base ? 0 : e.precio_base)}
                                        </span>
                                    </div>
                                </div>
                                <div className=" d-flex justify-content-between">
                                    <div className="p-2">
                                        <textarea type="text"
                                            required={true}
                                            disabled={type(e.type)} className={("form-control form-control ") + (!e.descripcion ? "invalid" : null)}
                                            value={!e.descripcion ? "" : e.descripcion}
                                            onChange={e => changeInventario((e.target.value.replace("\n", "")), i, e.id, "changeInput", "descripcion")}
                                            placeholder="descripcion..."></textarea>
                                    </div>
                                    <p className="card-text text-right">
                                        <span className="h3 text-success">Ct.</span> 
                                        <input type="number"
                                                required={true}
                                                disabled={type(e.type)} className={("text-right form-control form-control-sm ") + (!e.cantidad ? "invalid" : null)}
                                                value={!e.cantidad ? "" : e.cantidad}
                                                onChange={e => changeInventario(number(e.target.value), i, e.id, "changeInput", "cantidad")}
                                                placeholder="cantidad..." />
                                        
                                        <br />
                                        <select
                                            disabled={type(e.type)}
                                            className={("form-control form-control-sm ") + (!e.unidad ? "invalid" : null)}
                                            value={!e.unidad ? "" : e.unidad}
                                            onChange={e => changeInventario((e.target.value), i, e.id, "changeInput", "unidad")}
                                        >
                                            <option value="">--Select--</option>
                                            <option value="UND">UND</option>
                                            <option value="PAR">PAR</option>
                                            <option value="JUEGO">JUEGO</option>
                                            <option value="PQT">PQT</option>
                                            <option value="MTR">MTR</option>
                                            <option value="KG">KG</option>
                                            <option value="GRS">GRS</option>
                                            <option value="LTR">LTR</option>
                                            <option value="ML">ML</option>
                                        </select> 
                                        
                                        <span className="btn btn-outline-success btn-sm"
                                            data-id={e.id}
                                            onClick={setCtxBulto}>CtxBulto.{e.bulto}</span>
                                    </p>
                                </div>
                                <div className="">
                                    <div className="input-group">
                                        <select
                                            required={true}
                                            disabled={type(e.type)}
                                            className={("form-control form-control-sm ") + (!e.id_categoria ? "invalid" : null)}
                                            value={!e.id_categoria ? "" : e.id_categoria}
                                            onChange={e => changeInventario((e.target.value), i, e.id, "changeInput", "id_categoria")}
                                        >
                                            <option value="">--Select Categoría--</option>
                                            {categorias.map(e => <option value={e.id} key={e.id}>{e.descripcion}</option>)}

                                        </select>
                                        
                                        <select
                                            required={true}
                                            disabled={type(e.type)}
                                            className={("form-control form-control-sm ") + (!e.id_proveedor ? "invalid" : null)}
                                            value={!e.id_proveedor ? "" : e.id_proveedor}
                                            onChange={e => changeInventario((e.target.value), i, e.id, "changeInput", "id_proveedor")}
                                        >
                                            <option value="">--Select Proveedor--</option>
                                            {proveedoresList.map(e => <option value={e.id} key={e.id}>{e.descripcion}</option>)}

                                        </select>
                                    </div>
                                    <div className="input-group mb-1">
                                        <span className="input-group-text">
                                            IVA 
                                        </span>
                                        <input type="text"
                                            disabled={type(e.type)} className="form-control form-control-sm"
                                            value={!e.iva ? "" : e.iva}
                                            onChange={e => changeInventario(number(e.target.value, 2), i, e.id, "changeInput", "iva")}
                                            placeholder="iva..." />
                                    </div>
                                </div>
                            </>
                        }
                        <div className="">
                            <div className='d-flex justify-content-between'>
                                {!e.type ?
                                    <>
                                        <span className="btn btn btn-danger" onClick={() => changeInventario(null, i, e.id, "delMode")}><i className="fa fa-trash"></i></span>
                                        <span className="btn-lg btn btn-warning" onClick={() => changeInventario(null, i, e.id, "update")}><i className="fa fa-pencil"></i></span>
                                    </>
                                    : null}
                                {e.type === "new" ?
                                    <span className="btn-lg btn btn-danger" onClick={() => changeInventario(null, i, e.id, "delNew")}><i className="fa fa-times"></i></span>
                                    : null}
                                {e.type === "update" ?
                                    <span className="btn-lg btn btn-warning" onClick={() => changeInventario(null, i, e.id, "delModeUpdateDelete")}><i className="fa fa-times"></i></span>
                                    : null}
                                {e.type === "delete" ?
                                    <span className="btn-lg btn btn-danger" onClick={() => changeInventario(null, i, e.id, "delModeUpdateDelete")}><i className="fa fa-arrow-left"></i></span>
                                    : null}
                            </div>
                        </div>

                    </div>
                ) : 
                    <span colSpan={7}>Sin resultados</span>
                }
                    
            </form>

        </div>    
    )
}