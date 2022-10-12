import Historicocierre from '../components/historicocierre';

function Cierre({
	moneda,
	caja_usd,
	caja_cop,
	caja_bs,
	caja_punto,

	notaCierre,

	dejar_usd,
	dejar_cop,
	dejar_bs,

	setDejar_usd,
	setDejar_cop,
	setDejar_bs,

	onchangecaja,
	cierre,
	cerrar_dia,
	total_caja_neto,
	total_punto,
	fechaCierre,
	setFechaCierre,

	viewCierre,
	setViewCierre,
	toggleDetallesCierre,
	setToggleDetallesCierre,
	guardar_cierre,
	sendCuentasporCobrar,
	total_dejar_caja_neto,

	guardar_usd,
	setguardar_usd,
	guardar_cop,
	setguardar_cop,
	guardar_bs,
	setguardar_bs,

	number,

	billete1,
	billete5,
	billete10,
	billete20,
	billete50,
	billete100,
	
	setbillete1,
	setbillete5,
	setbillete10,
	setbillete20,
	setbillete50,
	setbillete100,

	dolar,
	peso,
	cierres,
	fechaGetCierre,
	setfechaGetCierre,
	fechaGetCierre2,
	setfechaGetCierre2,
	getCierres, 
	verCierreReq,
}) {

	const fun_setguardar = (type,val) =>{
		let total = number(cierre["efectivo_guardado"])
		if (type=="setguardar_cop") {
			
				setguardar_cop(val)

				let p = number((val/peso).toFixed(1))
				let u = total-p

				setguardar_bs("")
				setguardar_usd(u)
		}

		if (type=="setguardar_bs") {
			setguardar_bs(val)

			if (!val) {
				let p = number((guardar_cop/peso).toFixed(1))
				let u = total-p

				setguardar_usd(u)
			}else{
				let u = ((total-number((guardar_cop/peso).toFixed(1)))-number((val/dolar).toFixed(1))).toFixed(1)
				setguardar_usd(u)

			}
		}

	}
	
	return (
		<div className="container">
			<div className="btn-group mb-2">
				<button className={(viewCierre=="cuadre"?"btn-":"btn-outline-")+("sinapsis btn")} onClick={()=>setViewCierre("cuadre")}>Cuadre</button>
				<button className={(viewCierre=="historico"?"btn-":"btn-outline-")+("sinapsis btn")} onClick={()=>setViewCierre("historico")}>Histórico</button>
				
			</div>
			{viewCierre=="cuadre"?
			<>
				<div className="input-group">
					<button className="btn btn-sinapsis" onClick={cerrar_dia}><i className="fa fa-cogs"></i></button>

					<input type="date" required={true} value={fechaCierre} className="form-control" onChange={e=>setFechaCierre(e.target.value)}/>
					{cierre["fecha"]?
						<>
							<button className="btn" onClick={()=>setToggleDetallesCierre(!toggleDetallesCierre)}>Ver detalles</button>

							<button className="btn btn-sinapsis" onClick={guardar_cierre} type="button" data-type="ver">Ver</button>

							<button className="btn btn-warning" onClick={guardar_cierre} type="button" data-type="enviar">Enviar Cierre</button>
							{/*<button className="btn btn-warning" onClick={sendCuentasporCobrar} type="button" data-type="enviar">Enviar Cuentas por Cobrar</button>*/}
							
						</>
					:null}
				</div>
				{cierre["fecha"]?
					<form onSubmit={cerrar_dia}>
						<button hidden={true}></button>
						<table className="table">
							<tbody>
										{/*{cierre["match_cierre"]?
											<tr>
												<td colSpan="3" className="text-center">
													<div className="card-pedidos bg-success-light">
														<table className="table table-sm">
															<thead>
																<tr>
																	<th>Fecha</th>
																	<th>Débito</th>
																	<th>Efectivo</th>
																	<th>Transferencia</th>
																	<th>Dejar $</th>
																	<th>Dejar COP</th>
																	<th>Dejar BS.</th>
																	<th>Guardado $</th>
																	<th>Guardado COP</th>
																	<th>Guardado BS</th>
																	<th>Tasa</th>
																</tr>
																<tr>
																	<td>{cierre["match_cierre"]["fecha"]}</td>
																	<td>{cierre["match_cierre"]["debito"]}</td>
																	<td>{cierre["match_cierre"]["efectivo"]}</td>
																	<td>{cierre["match_cierre"]["transferencia"]}</td>
																	<td>{cierre["match_cierre"]["dejar_dolar"]}</td>
																	<td>{cierre["match_cierre"]["dejar_peso"]}</td>
																	<td>{cierre["match_cierre"]["dejar_bss"]}</td>
																	<td>{cierre["match_cierre"]["efectivo_guardado"]}</td>
																	<td>{cierre["match_cierre"]["efectivo_guardado_cop"]}</td>
																	<td>{cierre["match_cierre"]["efectivo_guardado_bs"]}</td>
																	<td>{cierre["match_cierre"]["tasa"]}</td>
																</tr>
															</thead>
														</table>
													</div>

												</td>
											</tr>
										:null}*/}
										{toggleDetallesCierre?
											<tr>
												<td colSpan="3" className="text-center d-flex justify-content-between">
														<div>
															<h6><span className="fw-bold">Entregado:</span> {cierre["entregado"]}</h6>
															
															<h6><span className="fw-bold">Pendiente:</span> {cierre["pendiente"]}</h6>
															
															<h6><span className="fw-bold">Caja inicial:</span> {cierre["caja_inicial"]}</h6>
															
														</div>
												</td>
											</tr>
										:null}
								<tr>
									<td></td>
									<th colSpan="2" className="text-center">
										¿Cuánto hay en caja?
										
									</th>
								</tr>
								<tr>
									<td className="align-middle">Billetes $</td>
									<th colSpan="2" className="align-middle">
										<div>
											<span className="h5">1</span>
			                <input type="text" value={billete1} name="billete1" onChange={e=>setbillete1(number(e.target.value))} className="input-50" placeholder="1 $" />
			                <span className="h5">5</span>
			                <input type="text" value={billete5} name="billete5" onChange={e=>setbillete5(number(e.target.value))} className="input-50" placeholder="5 $" />
			                <span className="h5">10</span>
			                <input type="text" value={billete10} name="billete10" onChange={e=>setbillete10(number(e.target.value))} className="input-50" placeholder="10 $" />
			                <span className="h5">20</span>
			                <input type="text" value={billete20} name="billete20" onChange={e=>setbillete20(number(e.target.value))} className="input-50" placeholder="20 $" />
			                <span className="h5">50</span>
			                <input type="text" value={billete50} name="billete50" onChange={e=>setbillete50(number(e.target.value))} className="input-50" placeholder="50 $" />
			                <span className="h5">100</span>
			                <input type="text" value={billete100} name="billete100" onChange={e=>setbillete100(number(e.target.value))} className="input-50" placeholder="100 $" />
										</div>
									</th>
								</tr>
								<tr>
									<th className="align-middle">$ / P / Bs</th>
									<th>
										<div className="container">
											<div className="row">
												<div className="col p-0">
													$ <input type="text" className="" placeholder="$" name="caja_usd" value={caja_usd} onChange={onchangecaja}/>
												</div>

												<div className="col p-0">
													P <input type="text" className="" placeholder="COP" name="caja_cop" value={caja_cop} onChange={onchangecaja}/>
												</div>

												<div className="col p-0">
													Bs. <input type="text" className="" placeholder="Bs." name="caja_bs" value={caja_bs} onChange={onchangecaja}/>
												</div>
											</div>
										</div>
									</th>
								</tr>
								<tr>
									<td className="text-success align-middle">Total Caja Actual</td>
									<td>
										<input type="text" value={total_caja_neto} className="form-control" disabled={true}/>
									</td>
									<td></td>
								</tr>
								<tr>
									<td></td>

									<th colSpan="2" className="text-center">
										¿Cuánto dejarás en caja?
									</th>
								</tr>
								<tr>
									<td></td>
									<th className="" colSpan="2">
										<div className="container-fluid">
											<div className="row">
												<div className="col">
													USD($)
													<input type="text" className="form-control" placeholder="$" name="dejar_usd" value={dejar_usd} onChange={e=>setDejar_usd(number(e.target.value))}/>
												</div>
												<div className="col">
													P
													<input type="text" className="form-control" placeholder="COP" name="dejar_cop" value={dejar_cop} onChange={e=>setDejar_cop(number(e.target.value))}/>
												</div>
												<div className="col">
													Bs.
													<input type="text" className="form-control" placeholder="Bs." name="dejar_bs" value={dejar_bs} onChange={e=>setDejar_bs(number(e.target.value))}/>
													
												</div>
											</div>
										</div>
									</th>
								</tr>
								<tr>
									<td className="text-success align-middle">Total Dejar Caja</td>

									<td className="align-middle text-center" colSpan="2">
										<span className="text-success">{total_dejar_caja_neto}</span>
									</td>
								</tr>
								<tr>
									<td></td>
									<th colSpan="2" className="text-center">
										Real
									</th>
								</tr>
								<tr>
									<th className="text-right">Punto</th>
									<td>
										<input type="text" className="form-control" placeholder="Punto de venta Bs." name="caja_punto" value={caja_punto} onChange={onchangecaja}/>
									</td>
									<td className="text-success">Facturado</td>
								</tr>
								<tr>
									<th className="text-right align-middle">Débito</th>
									<td>
										{total_punto}
										<div className={(cierre["estado_punto"]==1?"text-success":"text-danger")}>
											<small className="fst-italic">
												{cierre["msj_punto"]?cierre["msj_punto"]:null}
											</small>
										</div>
									</td>
									<td className="align-middle">
										{cierre[2]?cierre[2].toFixed(2):null}
									</td>
								</tr>
								<tr>
									<th className="text-right align-middle">Efectivo</th>
									<td>
										{cierre["total_caja"]?cierre["total_caja"]:0}
										<div className={(cierre["estado_efec"]==1?"text-success":"text-danger")}>
											<small className="fst-italic">
												{cierre["msj_efec"]?cierre["msj_efec"]:null}
											</small>
										</div>
									</td>
									<td className="align-middle">
										{cierre[3]?cierre[3].toFixed(2):null}
									</td>
								</tr>
								<tr>
									<th className="text-right align-middle">Transferencia</th>
									<td>
										{cierre[1]?cierre[1].toFixed(2):null}
									</td>
									<td>
										{cierre[1]?cierre[1].toFixed(2):null}
									</td>
								</tr>
								<tr>
									<th className="text-right">Nota</th>
									<td>
										<textarea name="notaCierre" placeholder="Novedades..." value={notaCierre?notaCierre:""} onChange={onchangecaja} cols="40" rows="2"></textarea>
									</td>
									<td>
										
									</td>
								</tr>
								<tr>
									<th className="text-right">Efectivo Guardado</th>
									<td colSpan="">
										<div className="container-fluid">
											<div className="row">
												<div className="col">
													  <span className="text-success fs-3 fw-bold">{cierre["efectivo_guardado"]?cierre["efectivo_guardado"]:null}</span>
												</div>
												<div className="col">
													<div className="input-group mb-3">
													  <div className="input-group-prepend w-50">
													    <span className="input-group-text">USD($)</span>
													  </div>
													  <input type="text" className="form-control" placeholder="$" name="guardar_usd" value={guardar_usd} onChange={e=>setguardar_usd(number(e.target.value))} disabled={true}/>
													</div>
													
												</div>
												<div className="col">
													<div className="input-group mb-3">
													  <div className="input-group-prepend w-50">
													    <span className="input-group-text">COP.</span>
													  </div>
													  <input type="text" className="form-control" placeholder="COP" name="guardar_cop" value={guardar_cop} onChange={e=>fun_setguardar("setguardar_cop",number(e.target.value))}/>
													</div>
													
												</div>
												<div className="col">
													<div className="input-group mb-3">
													  <div className="input-group-prepend w-50">
													    <span className="input-group-text">Bs.</span>
													  </div>
													  <input type="text" className="form-control" placeholder="Bs." name="guardar_bs" value={guardar_bs} onChange={e=>fun_setguardar("setguardar_bs",number(e.target.value))}/>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
							</tbody>
						</table>

					</form>

					:<div className="d-flex justify-content-center align-items-center">
						<button className="btn btn-xl btn-success" onClick={cerrar_dia}>¡Cerremos el día!</button>
					</div>}
			</>
			:null}	
			{viewCierre=="historico"?
				<Historicocierre 
				cierres={cierres}
				fechaGetCierre={fechaGetCierre}
				setfechaGetCierre={setfechaGetCierre}

				fechaGetCierre2={fechaGetCierre2}
				setfechaGetCierre2={setfechaGetCierre2}

				getCierres={getCierres}
				verCierreReq={verCierreReq}
				/>
			:null}
		</div>
	)
}

export default Cierre;