export default function PedidosCentralComponent({
	updateinventario,
	getPedidosCentral,
	selectPedidosCentral,
	checkPedidosCentral,

	pedidosCentral,
	setIndexPedidoCentral,
	indexPedidoCentral,
	moneda,

	showaddpedidocentral,
	setshowaddpedidocentral,
	valheaderpedidocentral,
	setvalheaderpedidocentral,
	valbodypedidocentral,
	setvalbodypedidocentral,
	procesarImportPedidoCentral,

	pathcentral,
	setpathcentral,
	mastermachines,
	getmastermachine,
}){
	return (
		<div className="container">
			<div className="row">
				
				<div className="col-3">
					<div className="mb-2">
						{/*<button className="btn btn-success w-100" onClick={getmastermachine}>Buscar a Master</button>
						<ul className="list-group">
							{mastermachines ? mastermachines.map((e,i)=>
								<li key={i} onClick={() => setpathcentral(e)} className={(pathcentral==e?"active":null)+(" list-group-item-action list-group-item")}>{e}</li>
							):null}
						</ul>*/}
						<input placeholder="Código de master" type="text" className="form-control" onChange={e=>setpathcentral(e.target.value)} value={pathcentral}/>
					</div>
					<div className="btn-group">
						{pathcentral?
						<>
							<button className="btn btn-outline-success" onClick={updateinventario}>Actualizar Inventario</button>
							<button className="btn btn-outline-sinapsis" onClick={getPedidosCentral}>Importar Pedidos</button>
						</>
						:null}
						{/* <button className="btn btn-outline-success" onClick={()=>setshowaddpedidocentral(!showaddpedidocentral)}><i className="fa fa-plus"></i></button> */}
					</div>
					<div>
						{ 
							pathcentral? pedidosCentral.length
							? pedidosCentral.map( (e,i) =>
								e?
									<div 
									onClick={()=>setIndexPedidoCentral(i)} 
									data-index={i}
									key={e.id}
									className={(indexPedidoCentral==i?"":"bg-light text-secondary")+" card mt-2 pointer"}>
									<div className="card-body flex-row justify-content-between">
										<div>
										<h4>ID Central <button className="btn btn-secondary">{e.id}</button> </h4>
										<small className="text-muted fst-italic">Productos <b>{e.items.length}</b> </small>
										<br/>
										<small className="text-muted fst-italic text-center"><b>{e.created_at}</b> </small>
										</div>
									</div>
									
									</div>
								:null
							  )
							: <div className='h3 text-center text-dark mt-2'><i>¡Sin resultados!</i></div>
							:null
						}
					</div>
				</div>
				{!showaddpedidocentral?
				<div className="col">
					{indexPedidoCentral!==null&&pedidosCentral?
								pedidosCentral[indexPedidoCentral]?
									<div className="d-flex justify-content-between border p-1">
					          <div className="w-50">
					          	<div>
					          		<small className="text-muted fst-italic">{pedidosCentral[indexPedidoCentral].created_at}</small>
					          	</div>
					          	<div className="d-flex align-items-center">
					            	<span className="fs-3 fw-bold"></span>
					            	<span className="btn btn-secondary m-1">{pedidosCentral[indexPedidoCentral].id}</span>
					          		
					          	</div>
					          </div>
					          <div className="w-50 text-right">
					            <span>
					          		<span className="h6 text-muted font-italic">Base. </span>
					          		<span className="h6 text-sinapsis">{moneda(pedidosCentral[indexPedidoCentral].base)}</span>
					            </span>
					          	<br/>

					            <span>
					          		<span className="h6 text-muted font-italic">Venta. </span>
					          		<span className="h3 text-success">{moneda(pedidosCentral[indexPedidoCentral].venta)}</span>
					            </span>
					          	
					          	<br/><span className="h6 text-muted">Items. <b>{pedidosCentral[indexPedidoCentral].items.length}</b></span>
					          </div>
					        </div>
							:null
					:null}

					<table className="table">
						<thead>
							<tr>
								<th><small><span className="text-muted">Verificar</span></small></th>
								<th>Ct</th>
								<th>Cod.</th>
								<th>Desc.</th>
								<th>Base</th>
								<th>Venta</th>
								<th className="text-right">Monto</th>
							</tr>
						</thead>
						<tbody>
							{indexPedidoCentral!==null&&pedidosCentral?
								pedidosCentral[indexPedidoCentral]?
								pedidosCentral[indexPedidoCentral].items.map((e,i)=>
									<tr key={e.id} 
									className={(e.aprobado?"bg-success-light":(e.aprobado===false?"bg-sinapsis-light":null))+(" pointer")}>
										<td
											onClick={selectPedidosCentral} 
											data-index={i} 
											data-tipo="select"
										>
											<input type="checkbox" 
											className="form-check-input"
											readOnly={true}
											checked={typeof(e.aprobado)!="undefined"?true:false}
											/>
										</td>
										<th className="align-middle">
											<span className={(typeof(e.ct_real)!="undefined"?"text-decoration-line-through":null)}>{e.cantidad.toString().replace(/\.00/,"")}</span>
											<br/>
											{typeof(e.ct_real)!="undefined"?
												<input type="text" value={e.ct_real} 
													data-index={i} 
													data-tipo="changect_real"
													onChange={selectPedidosCentral}
													size="4"
												/>
											:null}
										</th>
										<td className="align-middle">
											<small className="text-muted">{e.producto.codigo_barras}</small>
											<br/>
											<small className="text-muted">{e.producto.codigo_proveedor}</small>
										</td>
										<td className="align-middle">{e.producto.descripcion}</td>
										<td className="align-middle text-sinapsis">{moneda(e.producto.precio_base)}</td>
										<td className="align-middle text-success">{moneda(e.producto.precio)}</td>
										<td className="align-middle text-right">{moneda(e.monto)}</td>
									</tr>
								)
								:null
							:null}
						</tbody>
					</table>
					{indexPedidoCentral!==null&&pedidosCentral?
						pedidosCentral[indexPedidoCentral]?
							!pedidosCentral[indexPedidoCentral].items.filter(e=>(typeof(e.aprobado)==="undefined")||(e.ct_real==""||e.ct_real==0)).length?
								<div className="btn-group">
									<button className="btn btn-outline-success btn-block btn-xl" onClick={checkPedidosCentral}>Guardar Pedido</button>
								</div>
							:null
						:null
					:null}
				</div>
				:<div className="col">
					<h3>Importar pedido</h3>
					<div className="form-group">
						<label htmlFor="">Cabezera del pedido</label>
						<input type="text" className="form-control" value={valheaderpedidocentral} onChange={e=>setvalheaderpedidocentral(e.target.value)} placeholder="Cabezera del pedido"/>
					</div>	

					<div className="form-group">
						<label htmlFor="">Cuerpo del pedido</label>
						<textarea className="form-control" value={valbodypedidocentral} onChange={e=>setvalbodypedidocentral(e.target.value)} placeholder="Cuerpo del pedido" cols="30" rows="15"></textarea>
						
					</div>	
					<div className="form-group">
						<button className="btn btn-block btn-success" onClick={procesarImportPedidoCentral}>Importar</button>
					</div>
				</div>}
			</div>

		</div>
	)

}