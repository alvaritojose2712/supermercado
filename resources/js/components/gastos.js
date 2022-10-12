export default function Gastos({
    qgastosfecha1,setqgastosfecha1,
    qgastosfecha2,setqgastosfecha2,
    qgastos,setqgastos,
    qcatgastos,setqcatgastos,

    gastosdescripcion,setgastosdescripcion,
    gastoscategoria,setgastoscategoria,
    gastosmonto,setgastosmonto,

    gastosData,
    delGastos,
    getGastos,
    setGasto,

    number,
    moneda,



}){
    return (
        <div className="container">
            <h1>Gastos</h1>

            
            <div className="mb-5">
                <form className="input-group mb-3" onSubmit={setGasto}>
                    <div className="input-group-prepend">
                        <span className="input-group-text">Nuevo gasto</span>
                    </div>

                    <input type="text" className="form-control"
                        placeholder="Descripción..."
                        value={gastosdescripcion} 
                        onChange={e => setgastosdescripcion(e.target.value)} />
                    <input type="text" className="form-control"
                        placeholder="Monto..."
                        value={gastosmonto}
                        onChange={e => setgastosmonto(number(e.target.value))} />
                    <select
                        className="form-control"
                        value={gastoscategoria}
                        onChange={e => setgastoscategoria(e.target.value)}>
                        <option value="3">Funcionamiento</option>
                        <option value="2">Nómina</option>
                   
                    </select>
                    <button className="btn btn-outline-success"><i className="fa fa-paper-plane"></i></button>

                </form>
            </div>

            <div className="input-group mb-1">


                <input type="text" className="form-control"
                    placeholder="Buscar..."
                    onChange={e => setqgastos(e.target.value)}
                    value={qgastos} />
                <select
                    className="form-control"
                    onChange={e => setqcatgastos(e.target.value)}
                    value={qcatgastos}>
                    <option value="">Todos</option>
                    <option value="3">Funcionamiento</option>
                    <option value="2">Nómina</option>

                </select>

                <input type="date" className="form-control"
                    onChange={e => setqgastosfecha1(e.target.value)}
                    value={qgastosfecha1} />

                <input type="date" className="form-control"
                    onChange={e => setqgastosfecha2(e.target.value)}
                    value={qgastosfecha2} />

                <div className="input-group-append">
                    <span className="btn btn-outline-secondary" type="button" onClick={getGastos}><i className="fa fa-search"></i></span>
                </div>
            </div>

            <table className="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th className="text-right">Monto</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {gastosData ? gastosData.gastos ? gastosData.gastos.length?
                        gastosData.gastos.map(e=><tr key={e.id}>
                            <td className="align-middle">
                                {e.id}<br/>
                                <small className="text-muted">{e.created_at}</small>
                            </td>
                            <td className="align-middle">{e.descripcion}</td>
                            <td className="align-middle">
                                {e.categoria==3?"Funcionamiento":null}
                                {e.categoria == 2 ? "Nómina" : null}
                            </td>
                            <td className="align-middle text-success text-right">{moneda(e.monto)}</td>
                            <td className="align-middle text-right"><span className="btn btn-outline-danger" data-id={e.id} onClick={delGastos}><i className="fa fa-times"></i></span></td>
                        </tr>)
                    :null:null:null}
                    <tr>
                        <th className="fs-4">Num. {gastosData ? gastosData.num ? gastosData.num:null:null}</th>
                        <th colSpan="3" className="fs-3 text-right text-success">
                            Tot. {gastosData ? gastosData.sum ? moneda(gastosData.sum) : null : null}
                        </th>
                        <th></th>
                    </tr>
                </tbody>
            </table>
        </div>
    )
}