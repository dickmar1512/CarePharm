<style type="text/css">
	.procesar-venta[disabled] {
		pointer-events: none;
		opacity: 0.65;
		cursor: /*no-drop*/ not-allowed; /**cualquiera de los dos muestra como puntero un circulo con una raya crusada en diagonal */
	}

	.difference-container {
		margin-bottom: 20px;
		padding: 10px;
		background-color: #f8f9fa;
		border-radius: 5px;
		border: 1px solid #dee2e6;
	}
</style>
<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><i class='fa fa-archive'></i> Cierre de caja</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Caja</a></li>
					<li class="breadcrumb-item active">Cierre de caja</li>
				</ol>
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
 <section class="content">
	<div class="container-fluid col-md-12">
		<div class="card card-default">
			<div class="card-header">
				<div class="row" style="display: flex; justify-content: right;">
					<div class="col-md-8" style="display: flex; justify-content: right;">
						<div class="btn-group pull-right">
							<a href="./?view=boxhistory" class="btn btn-primary mr-2">
								<i class="fa fa-clock-o"></i>
								Historial
							</a>
							<button id="procesarVentasBtn" class="btn btn-primary procesar-venta">
								Procesar Ventas 
								<i class="fa fa-arrow-right"></i>
							</button>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="row" style="display: flex; justify-content: center;">
					<div class="col-md-6">
						<?php
						$products = SellData::getSellsUnBoxed();
						$total_total = 0;

						if (count($products) > 0) {
							$total_total = 0;
							$i = 1;
							?>
							<table class="table table-bordered table-hover" id="box">
								<thead class="thead-dark">
									<tr>
										<th colspan="5">Comprobantes generados</th>
									</tr>
									<tr>
										<th>#</th>
										<th>Comprobante</th>
										<th>Total</th>
										<th>Fecha</th>
										<th>Usuario</th>
									</tr>
								</thead>
								<?php foreach ($products as $sell): 
										$notacomprobar = $sell->serie . "-" . $sell->comprobante; 
										$probar = Not_1_2Data::getByIdComprobado($notacomprobar);
										$fechaObj = new DateTime($sell->created_at);
										$fechaFormateada = $fechaObj->format('d/m/Y H:i:s');
									?>
									<tr style="background: <?php if (isset($probar)) {
											if ($probar->TIPO_DOC==8) {	echo "#C2FCCF"; }
											if ($probar->TIPO_DOC==7) {	echo "#FFC4C4"; }
										} ?>">
										<td><?= $i++ ?></td>
										<td><?= $sell->serie . "-" . $sell->comprobante ?></td>
										<td style="text-align: center;">
											<?php
											$operations = OperationData::getAllProductsBySellId($sell->id);
											$total = 0;
											foreach ($operations as $operation) {
												$product = $operation->getProduct();
												$total += (isset($probar->TIPO_DOC) && $probar->TIPO_DOC ==7) ? 0 : $operation->q * ($operation->prec_alt - $operation->descuento);
											}
											$total_total += $total;
											echo "<b> " . number_format($total, 2, ".", ",") . "</b>";

											?>
										</td>	
										<td style="text-align: center;"><?=$fechaFormateada; ?></td>
										<td style="text-align: center;"><?=$sell->user; ?></td>
									</tr>
								<?php endforeach; ?>
							</table>
							<h1>Total: <?php echo  number_format($total_total, 2, ".", ","); ?></h1>
							<input type="hidden" id="totalVentas" name="totalVentas" value="<?=$total_total; ?>">
							<?php
						} else {

							?>
							<div class="jumbotron">
								<h2>No hay ventas</h2>
								<p>No se ha realizado ninguna venta.</p>
							</div>

						<?php } ?>
					</div>
					<div class="col-md-3">
						<?php 
						 if($total_total > 0): ?>
						<table class="table table-bordered table-hover" id="denominations">
							<thead class="thead-dark">
								<tr>
									<th colspan="2">Billetes y monedas</th>
								</tr>
								<tr>
									<th>Denominacion</th>
									<th>Cantidad</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><label for="b200" class="control-label">Billete &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;200</label></td>
									<td style="text-align: center;"><input type="number" name="b200" id="b200" class="form-control-sm" style="max-width: 50%; text-align: right;" value="0"></td>
								</tr>
								<tr>
									<td><label for="b100">Billete &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;100</label></td>
									<td style="text-align: center;"><input type="number" name="b100" id="b100" class="form-control-sm" style="max-width: 50%; text-align: right;" value="0"></td>
								</tr>
								<tr>
									<td><label for="b50">Billete &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;50</label></td>
									<td style="text-align: center;"><input type="number" name="b50" id="b50" class="form-control-sm" style="max-width: 50%; text-align: right;" value="0"></td>
								</tr>
								<tr>
									<td><label for="b20">Billete &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;20</label></td>
									<td style="text-align: center;"><input type="number" name="b20" id="b20" class="form-control-sm" style="max-width: 50%; text-align: right;" value="0"></td>
								</tr>
								<tr>
									<td><label for="b10">Billete &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;10</label></td>
									<td style="text-align: center;"><input type="number" name="b10" id="b10" class="form-control-sm" style="max-width: 50%; text-align: right;" value="0"></td>
								</tr>
								<tr>
									<td><label for="m5">Moneda &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5</label></td>
									<td style="text-align: center;"><input type="number" name="m5" id="m5" class="form-control-sm" style="max-width: 50%; text-align: right;" value="0"></td>
								</tr>
								<tr>
									<td><label for="m2">Moneda &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2</label></td>
									<td style="text-align: center;"><input type="number" name="m2" id="m2" class="form-control-sm" style="max-width: 50%; text-align: right;" value="0"></td>
								</tr>
								<tr>
									<td><label for="m1">Moneda &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1</label></td>
									<td style="text-align: center;"><input type="number" name="m1" id="m1" class="form-control-sm" style="max-width: 50%; text-align: right;" value="0"></td>
								</tr>
								<tr>
									<td><label for="c50">Moneda 0.5</label></td>
									<td style="text-align: center;"><input type="number" name="c50" id="c50" class="form-control-sm" style="max-width: 50%; text-align: right;" value="0"></td>
								</tr>
								<tr>
									<td><label for="c20">Moneda 0.2</label></td>
									<td style="text-align: center;"><input type="number" name="c20" id="c20" class="form-control-sm" style="max-width: 50%; text-align: right;" value="0"></td>
								</tr>
								<tr>
									<td><label for="c10">Moneda 0.1</label></td>
									<td style="text-align: center;"><input type="number" name="c10" id="c10" class="form-control-sm" style="max-width: 50%; text-align: right;" value="0"></td>
								</tr>
								<tr>
									<td><label for="yape">Yape</label></td>
									<?php $yape = json_decode(json_encode(SellData::getVentasOtroTipoPago(0,3)),true)['total'];
									if ($yape > 0): 
										$yape = number_format($yape, 2, '.', ',');
									else:
										$yape = "0.00";
									endif;
									?>
									<td style="text-align: center;"><input type="number" name="yape" id="yape" class="form-control-sm" style="max-width: 50%; text-align: right;" value="<?=$yape?>"></td>
								</tr>
								<tr>
									<td><label for="plin">Plin</label></td>
									<?php $plin = json_decode(json_encode(SellData::getVentasOtroTipoPago(0,2)),true)['total'];
									if ($plin > 0): 
										$plin = number_format($plin, 2, '.', ',');
									else:
										$plin = "0.00";
									endif;
									?>
									<td style="text-align: center;"><input type="number" name="plin" id="plin" class="form-control-sm" style="max-width: 50%; text-align: right;" value="<?=$plin?>"></td>
								</tr>
								<tr>
									<td><label for="tcredito">T.Credito</label></td>
									<?php $tcredito = json_decode(json_encode(SellData::getVentasOtroTipoPago(0,4)),true)['total'];
									if ($tcredito > 0): 
										$tcredito = number_format($tcredito, 2, '.', ',');
									else:
										$tcredito = "0.00";
									endif;
									?>
									<td style="text-align: center;"><input type="number" name="tcredito" id="tcredito" class="form-control-sm" style="max-width: 50%; text-align: right;" value="<?=$tcredito?>"></td>
								</tr>
							</tbody>
						</table>
						<?php else: ?>
						<div class="jumbotron">
							<h2>No hay ingresos</h2>
							<p>No se ha realizado ninguna venta.</p>
						</div>
						<?php endif; ?>		
					</div>
					<div class="col-md-3">
						<div class="jumbotron">
							<h2>Instrucciones</h2>
							<p style="text-align: justify;">Para procesar las ventas, primero debes ingresar las contidades de moneda por denominación.</p>
							<p style="text-align: justify;">Luego de ingresados las cantidades la sumatoria debe ser igual al total de ventas del dia, caso contrario no se activa el boton procesar ventas por ende no se podra realizar el cierre de caja.</p>
							<p style="text-align: justify;">Para procesar las ventas, primero debes hacer clic en el botón "Procesar Ventas".</p>
							<p style="text-align: justify;">Luego, se generará un cierre de caja con todas las ventas no procesadas.</p>
							<p style="text-align: justify;">Finalmente, podrás ver el historial de cierres de caja realizados.</p>
						</div>
					</div>	
				</div>
			</div>
	</div>
</div>