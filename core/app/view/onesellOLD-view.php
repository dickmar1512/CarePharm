<?php
if ($_GET["tipodoc"] == 3):
	$venta = Boleta2Data::getByExtra($_GET["id"]);
	$img = "img/bol.png";
	$docLabel = "DNI";
	$nomLabel = "SEÑOR(ES)";
else:
	$venta = Factura2Data::getByExtra($_GET["id"]);
	$img = "img/fac.png";
	$docLabel = "RUC";
	$nomLabel = "RAZON SOCIAL";
endif;

$comp_cab = Cab_1_2Data::getById($venta->id, $_GET["tipodoc"]);
$comp_aca = Aca_1_2Data::getById($venta->id, $_GET["tipodoc"]);
$detalles = Det_1_2Data::getById($venta->id, $_GET["tipodoc"]);
$comp_tri = Tri_1_2Data::getById($venta->id, $_GET["tipodoc"]);
$comp_ley = Ley_1_2Data::getById($venta->id, $_GET["tipodoc"]);
$sellTemp = SellData::getById($venta->EXTRA1);

$sell = (object)[
	'id'=> $sellTemp->id,
	'person_id'=> $sellTemp->person_id,
	'user_id'=> $sellTemp->user_id,
	'total'=> $sellTemp->total,
	'cash'=> $sellTemp->cash,
	'discount'=> $sellTemp->discount,
	'created_at'=> $sellTemp->created_at,
	'tipo_comprobante'=> $sellTemp->tipo_comprobante,
	'serie'=> $sellTemp->serie,
	'comprobante'=> $sellTemp->comprobante,
	'estado'=> $sellTemp->estado,
	'tipo_pago'=> $sellTemp->tipo_pago,
	'box_id'=> $sellTemp->box_id,
	'operation_type_id'=> $sellTemp->operation_type_id,
	'observacion'=> $sellTemp->observacion,
	'forma_pago'=> $sellTemp->forma_pago
];

$pagoParcial = SellData::getImportePagoParcial($venta->EXTRA1);

$datoPagoParcial = [
    'id' => $pagoParcial[0]->id ?? 0,
    'importepp' => $pagoParcial[0]->importepp ?? 0
];

$operations = OperationData::getAllProductsBySellId($_GET["id"]);

$cajero = null;
$cajero = UserData::getById($sell->user_id)->username;
$empresa = EmpresaData::getDatos();

$fechaObj = new DateTime($comp_cab->fecEmision);
$fechaFormateada = $fechaObj->format('d/m/Y');

$selected = isset($sell->tipo_pago) ? $sell->tipo_pago : null;

?>
<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><i class='fa fa-shopping-cart'></i> Comprobante</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Imprimir comprobante</a></li>
				</ol>
			</div><!-- /.col -->

		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<section class="content">
	<div class="container-fluid">
		<div class="card card-default">
			<div class="card-header">
				<div class="row text-center">
					<div class="col-md-1">
						<div class="row">
							<a class="nav-link" onclick="goBack()" data-widget="pushmenu" href="#" role="button">
								<i class="fas fa-arrow-left"></i> Volver
							</a>
							<!-- <h4>Imprimir Comprobante</h4> -->
						</div>
					</div>
					<div class="col-md-2">
						<div class="row">
							<!-- <div class="col-md-2">
								<div class="float">
									<button id="imprimir50mm" class="btn btn-md btn-info"><i class="fa fa-print"></i>
										IMPRIMIR
										50mm</button>
								</div>
							</div> -->
							<div class="col-md-10">
								<div class="float">
									<button id="imprimir80mm" class="btn btn-md btn-info"><i class="fa fa-print"></i>
										IMPRIMIR
										80mm</button>
								</div>
							</div>
							<!-- <div class="col-md-2">
								<div class="float">
									<button id="imprimirA5" class="btn btn-md btn-info"><i class="fa fa-print"></i>
										IMPRIMIR
										A5</button>
								</div>
							</div> -->
							<!-- <div class="col-md-2">
								<div class="float">
									<button id="imprimirA4" class="btn btn-md btn-info"><i class="fa fa-print"></i>
										IMPRIMIR
										A4</button>
								</div>
							</div> -->
						</div>
					</div>
					<div class="col-md-1">
						<label for="selTipoPago" class="col-lg-16 control-label">
							TIPO DE PAGO:
						</label>
					</div>
					<div class="col-md-2">
						<select id="selTipoPago" name="selTipoPago" class="form-control text-sm">
							<option value="1" <?= $selected == 1 ? 'selected' : '' ?>>Efectivo</option>
							<option value="2" <?= $selected == 2 ? 'selected' : '' ?>>Plin</option>
							<option value="3" <?= $selected == 3 ? 'selected' : '' ?>>Yape</option>
							<option value="4" <?= $selected == 4 ? 'selected' : '' ?>>Tarjeta débito</option>
							<option value="5" <?= $selected == 5 ? 'selected' : '' ?>>Tarjeta crédito</option>
						</select>
						<input type="hidden" id="sellid" name="sellid" value="<?=$venta->EXTRA1?>">
					</div>
					<div class="col-md-1.5">
						<label for="importeParcial" class="col-lg-16 control-label">
							PAGO PARCIAL:
						</label>
					</div>
					<div class="col-md-1">
					<input type="text" id="importeParcial" name="importeParcial" class="form-control text-sm" placeholder="Pago parcial" value="<?= number_format($datoPagoParcial['importepp'], 2, '.', ',') ?>">
					</div>
					<div class="col-md-2">
						<div class="float">
							<button id="actualizarTipoPago" class="btn btn-md btn-info"><i class="fa fa-print"></i>
								Actualizar Pago	
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row" style="margin-top: 0px; padding-top: 0px; background: #fff">
					<div class="col-md-12" style="display: flex; justify-content: center;">
						<div class="row" style="display: flex; justify-content: center;">
							<div class="col-sm-10">
								<!-- card -->
								<div class="card col-sm-10">
									<div class="row" style="display: flex; justify-content: center;">
										<table class="table table-bordered">
											<tr>
												<td style="text-align: center; width: 100px">
													<img src="dist/img/logo2.jpg" style="height: 80px; width: 100%" />
												</td>
												<td
													style="text-align: center; font-family: courier new; font-size: 0.9em; width: 250px">
													<center>
														<b>
															<?php echo $empresa->Emp_RazonSocial ?><br>
															<?php echo $empresa->Emp_Direccion ?><br>
															Telf.: <?php echo $empresa->Emp_Telefono ?><br>
															Correo.: <?php echo $empresa->Emp_Celular ?>
														</b>
													</center>
												</td>
												<td
													style="text-align: center; font-family: courier new; font-size: 0.9em; width: 150px">
													<b>RUC:<?php echo $empresa->Emp_Ruc ?></b>
													<div style=" color: #FFF">
														<img src="<?= $img ?>" style="width: 100%; height: 35%">
													</div>
													<b><?php echo $venta->SERIE . "-" . $venta->COMPROBANTE; ?></b>
												</td>
											</tr>
										</table>
									</div>
									<div class="row">
										<table class="table table-bordered"
											style="font-family: courier new; font-size: 0.9em">
											<tr>
												<td class="col-sm-2">
													<b><?= $docLabel ?></b>
												</td>
												<td>
													<b><?php echo ": " . $comp_cab->numDocUsuario; ?></b>
												</td>
											</tr>
											<tr>
												<td class="col-sm-2">
													<b><?= $nomLabel ?></b>
												</td>
												<td>
													<b><?php echo ": " . $comp_cab->rznSocialUsuario; ?></b>
												</td>
											</tr>
											<tr>
												<td class="col-sm-2">
													<b>DIRECCIÓN</b>
												</td>
												<td>
													<b><?php echo ": " . $comp_aca->desDireccionCliente; ?></b>
												</td>
											</tr>
											<tr>
												<td class="col-sm-2">
													<b> FECHA EMISIÓN</b>
												</td>
												<td>
													<b><?php echo ": " . $fechaFormateada . "  " . $comp_cab->horEmision; ?></b>
												</td>
											</tr>
										</table>
										<table class="table table-bordered"
											style="font-family: courier new; font-size: 0.9em">
											<thead style="background-color: #000; color:#fff">
												<tr>
													<th scope="col">CANTIDAD</th>
													<th scope="col">DESCRIPCION</th>
													<th scope="col">P. UNIT.</th>
													<th scope="col">TOTAL</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$total = 0;
												foreach ($operations as $ope) {
													$product = ProductData::getById($ope->product_id);
													$subtotal = $ope->q * $ope->prec_alt;
													?>
													<tr>
														<td align="center"><b><?php echo $ope->q; ?></b></td>
														<td>
															&nbsp;&nbsp;<b><?php echo $product->name; ?></b>
															<?php
															if ($ope->descripcion != "") {
																echo "|" . $ope->descripcion;
															}
															?>
														</td>
														<td><b><?php echo $ope->prec_alt; ?></b></td>
														<td><b><?php echo number_format($subtotal, 2, '.', ','); ?></b></td>
													</tr>
													<?php
													$total = $subtotal + $total;
													$totalConDesc = $total - $comp_cab->sumDescTotal;
													$numLetra = NumeroLetras::convertir(number_format($totalConDesc, 2, '.', ','));
												}


												$datosComprobante = array(
													"venta"       => $venta,
													"detalles"    => $detalles,
													"comp_cab"    => $comp_cab,
													"comp_aca"    => $comp_aca,
													"comp_tri"    => $comp_tri,
													"comp_ley"    => $comp_ley,
													"empresa"     => $empresa,
													"cajero"      => $cajero,
													"numLetra"    => $numLetra,
													"sell"        => $sell,
													"pagoParcial" => $datoPagoParcial
												);
												?>
											</tbody>
										</table>
										<table class="table table-bordered"
											style="font-family: courier new; font-size: 0.9em ">
											<thead>
												<tr>
													<th style="width: 50%">
														<?php
														echo "<input type='hiddenxx' id='datosComprobante' name='datosComprobante' value='" . json_encode($datosComprobante) . "'>";
														?>
														<table class="table table-bordered">
															<tr>
																<td>
																	<?php echo "Usuario:" . $cajero; ?>
																</td>
															</tr>
														</table>
													</th>
													<th>
														<table class="table table-bordered">
															<tr>
																<td>OPERACIÓN GRATUITA</td>
																<td>0.00</td>
															</tr>
															<tr>
																<td>OPERACIÓN EXONERADA</td>
																<td><?php echo number_format($total, 2, '.', ','); ?>
																</td>
															</tr>
															<tr>
																<td>OPERACIÓN INAFECTA</td>
																<td>0.00</td>
															</tr>
															<tr>
																<td>OPERACIÓN GRAVADA</td>
																<td>0.00</td>
															</tr>
															<tr>
																<td>IGV</td>
																<td>0.00</td>
															</tr>
															<tr>
																<td>IMPORTE TOTAL</td>
																<td><?php echo number_format($total, 2, '.', ','); ?>
																</td>
															</tr>
															<tr>
																<td colspan="2">
																	<b>SON: <?php echo $numLetra; ?></b>
																</td>
															</tr>
														</table>
													</th>
												</tr>
											</thead>
										</table>
									</div>
								</div>
								<!-- end card  -->
							</div>
						</div>
					</div>
				</div><!--  fin col-md-6 -->
			</div>
		</div>
	</div>
</section>