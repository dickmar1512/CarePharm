<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><i class='fa fa-archive'></i> Corte de caja</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Caja</a></li>
					<li class="breadcrumb-item"><a href="#">Cierre de caja</a></li>
					<li class="breadcrumb-item"><a href="#">Historial de caja</a></li>
					<li class="breadcrumb-item active">Corte de caja</li>
				</ol>
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
	<div class="container-fluid col-md-10">
		<div class="card card-default">
			<div class="card-header">
				<div class="row">
					<div class="col-md-2">
						<a class="nav-link" href="./?view=boxhistory" role="button">
							<i class="fas fa-arrow-left"></i> Atras
						</a>
					</div>
					<div class="col-md-8">
						<h4>Corte numero #<?php echo $_GET["id"]; ?></h4>
					</div>
				</div>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="row" style="display: flex; justify-content: center;">
					<div class="col-md-6">
							<?php
							$products = SellData::getByBoxId($_GET["id"]);
							$datoCierre = BoxData::getByBoxIdDetalle($_GET["id"]);
							
							foreach ($datoCierre as $dato) {
								$b200 = $dato->b200;
								$b100 = $dato->b100;
								$b50 = $dato->b50;
								$b20 = $dato->b20;
								$b10 = $dato->b10;
								$m5 = $dato->m5;
								$m2 = $dato->m2;
								$m1 = $dato->m1;
								$c50 = $dato->c50;
								$c20 = $dato->c20;
								$c10 = $dato->c10;
								$totalBilletes = ($b200 * 200) + ($b100 * 100) + ($b50 * 50) + ($b20 * 20) + ($b10 * 10);
								$totalMonedas = ($m5 * 5) + ($m2 * 2) + ($m1 * 1) + ($c50 * 0.5) + ($c20 * 0.2) + ($c10 * 0.1);
								$totalEfectivo = $totalBilletes + $totalMonedas;		
							}

							if (count($products) > 0) {
								$total_total = 0;
								?>
								<table class="table table-bordered table-hover">
									<thead class="thead-dark">
										<th>#</th>
										<th>Comprobante</th>
										<th>Total</th>
										<th>Fecha</th>
										<th>Usuario</th>
									</thead>
									<?php foreach ($products as $sell):
										$notacomprobar = $sell->serie . "-" . $sell->comprobante;
										$probar = Not_1_2Data::getByIdComprobado($notacomprobar);
										$fechaObj = new DateTime($sell->created_at);
										$fechaFormateada = $fechaObj->format('d/m/Y H:i:s');
										if ($sell->serie == "F001") {
											$tipodoc = 1;
										} else {
											$tipodoc = 3;
										}
										?>
										<tr style="background: <?php if (isset($probar)) {
											if ($probar->TIPO_DOC == 8) {
												echo "#C2FCCF";
											}
											if ($probar->TIPO_DOC == 7) {
												echo "#FFC4C4";
											}
										} ?>">
											<td style="width:30px;">
												<a href="./?view=onesell&id=<?php echo $sell->id; ?>&tipodoc=<?= $tipodoc ?>"
													class="btn btn-default btn-xs"><i class="fa fa-arrow-right"></i></a>
												<?php
												$operations = OperationData::getAllProductsBySellId($sell->id);
												?>
											</td>
											<td><?= $sell->serie . "-" . $sell->comprobante ?></td>
											<td>
												<?php
												$total = 0;
												foreach ($operations as $operation) {
													$product = $operation->getProduct();
													$total += (isset($probar->TIPO_DOC) && $probar->TIPO_DOC == 7) ? 0 : $operation->q * $operation->prec_alt;
												}
												$total_total += $total;
												echo "<b>" . number_format($total, 2, ".", ",") . "</b>";
												?>

											</td>
											<td><?=$fechaFormateada; ?></td>
											<td><?=$sell->user; ?></td>
										</tr>

									<?php endforeach; ?>

								</table>
								<h1>Total: <?=number_format($total_total, 2, ".", ",")?></h1>
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
									<td style="text-align: center;"><input type="number" name="b200" id="b200" class="form-control-sm" style="max-width: 50%; text-align: right;" value="<?=$b200?>" disabled></td>
								</tr>
								<tr>
									<td><label for="b100">Billete &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;100</label></td>
									<td style="text-align: center;"><input type="number" name="b100" id="b100" class="form-control-sm" style="max-width: 50%; text-align: right;" value="<?=$b100?>" disabled></td>
								</tr>
								<tr>
									<td><label for="b50">Billete &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;50</label></td>
									<td style="text-align: center;"><input type="number" name="b50" id="b50" class="form-control-sm" style="max-width: 50%; text-align: right;" value="<?=$b50?>" disabled></td>
								</tr>
								<tr>
									<td><label for="b20">Billete &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;20</label></td>
									<td style="text-align: center;"><input type="number" name="b20" id="b20" class="form-control-sm" style="max-width: 50%; text-align: right;" value="<?=$b20?>" disabled></td>
								</tr>
								<tr>
									<td><label for="b10">Billete &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;10</label></td>
									<td style="text-align: center;"><input type="number" name="b10" id="b10" class="form-control-sm" style="max-width: 50%; text-align: right;" value="<?=$b10?>" disabled></td>
								</tr>
								<tr>
									<td><label for="m5">Moneda &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5</label></td>
									<td style="text-align: center;"><input type="number" name="m5" id="m5" class="form-control-sm" style="max-width: 50%; text-align: right;" value="<?=$m5?>" disabled></td>
								</tr>
								<tr>
									<td><label for="m2">Moneda &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2</label></td>
									<td style="text-align: center;"><input type="number" name="m2" id="m2" class="form-control-sm" style="max-width: 50%; text-align: right;" value="<?=$m2?>" disabled></td>
								</tr>
								<tr>
									<td><label for="m1">Moneda &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1</label></td>
									<td style="text-align: center;"><input type="number" name="m1" id="m1" class="form-control-sm" style="max-width: 50%; text-align: right;" value="<?=$m1?>" disabled></td>
								</tr>
								<tr>
									<td><label for="c50">Moneda 0.5</label></td>
									<td style="text-align: center;"><input type="number" name="c50" id="c50" class="form-control-sm" style="max-width: 50%; text-align: right;" value="<?=$c50?>" disabled></td>
								</tr>
								<tr>
									<td><label for="c20">Moneda 0.2</label></td>
									<td style="text-align: center;"><input type="number" name="c20" id="c20" class="form-control-sm" style="max-width: 50%; text-align: right;" value="<?=$c20?>" disabled></td>
								</tr>
								<tr>
									<td><label for="c10">Moneda 0.1</label></td>
									<td style="text-align: center;"><input type="number" name="c10" id="c10" class="form-control-sm" style="max-width: 50%; text-align: right;" value="<?=$c10?>" disabled></td>
								</tr>
								<tr>
									<td><label for="yape">Yape</label></td>
									<?php $yape = json_decode(json_encode(SellData::getVentasOtroTipoPago($_GET["id"],3)),true)['total'];
									if ($yape > 0): 
										$yape = number_format($yape, 2, '.', ',');
									else:
										$yape = "0.00";
									endif;
									?>
									<td style="text-align: center;"><input type="number" name="yape" id="yape" class="form-control-sm" style="max-width: 50%; text-align: right;" value="<?=$yape?>" disabled></td>
								</tr>
								<tr>
									<td><label for="plin">Plin</label></td>
									<?php $plin = json_decode(json_encode(SellData::getVentasOtroTipoPago($_GET["id"],2)),true)['total'];
									if ($plin > 0): 
										$plin = number_format($plin, 2, '.', ',');
									else:
										$plin = "0.00";
									endif;
									?>
									<td style="text-align: center;"><input type="number" name="plin" id="plin" class="form-control-sm" style="max-width: 50%; text-align: right;" value="<?=$plin?>" disabled></td>
								</tr>
								<tr>
									<td><label for="tcredito">T.Credito</label></td>
									<?php $tcredito = json_decode(json_encode(SellData::getVentasOtroTipoPago($_GET["id"],4)),true)['total'];
									if ($tcredito > 0): 
										$tcredito = number_format($tcredito, 2, '.', ',');
									else:
										$tcredito = "0.00";
									endif;
									?>
									<td style="text-align: center;"><input type="number" name="tcredito" id="tcredito" class="form-control-sm" style="max-width: 50%; text-align: right;" value="<?=$tcredito?>" disabled></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- /.card-body -->
		</div>
	</div>
</section>