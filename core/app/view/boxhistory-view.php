<?php
	$sd = (isset($_GET["sd"])) ? $_GET["sd"] : date('d/m/Y');
	$ed = (isset($_GET["ed"])) ? $_GET["ed"] : date('d/m/Y');
?>
<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><i class='fa fa-archive'></i> Historial de caja</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Caja</a></li>
					<li class="breadcrumb-item"><a href="#">Cierre de caja</a></li>
					<li class="breadcrumb-item active">Historial de caja</li>
				</ol>
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
 <section class="content">
	<div class="container-fluid col-md-8">
		<div class="card card-default">
			<div class="card-header">
				<form>
					<input type="hidden" name="view" value="boxhistory">
					<div class="row">
						<div class="col-md-2">
							<a class="nav-link" href="./?view=box" role="button">
								<i class="fas fa-arrow-left"></i> Atras
							</a>
						</div>
						<div class="col-md-1.5">
							<h3> De :</h3>
						</div>
						<div class="col-md-3 mr-2">
							<div class="input-group date" id="fechaini" data-target-input="nearest">
								<input type="text" name="sd" value="<?=$sd?>" class="form-control datetimepicker-input" data-target="#fechaini"/>
								<div class="input-group-append" data-target="#fechaini" data-toggle="datetimepicker">
									<div class="input-group-text"><i class="fa fa-calendar"></i></div>
								</div>
							</div>
						</div>
						<div class="col-md-1.5">
							<h3> Hasta :</h3>
						</div>
						<div class="col-md-3">
							<div class="input-group date" id="fechafin" data-target-input="nearest">
								<input type="text" name="ed" value="<?=$ed?>" class="form-control datetimepicker-input" data-target="#fechafin"/>
								<div class="input-group-append" data-target="#fechafin" data-toggle="datetimepicker">
									<div class="input-group-text"><i class="fa fa-calendar"></i></div>
								</div>
							</div>
						</div>					
						<div class="col-md-2">
							<input type="submit" class="btn btn-success btn-block" value="Procesar">
						</div>
					</div>
				</form>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="row" style="display: flex; justify-content: center;">
					<div class="col-md-10">
						<div class="row" style="display: flex; justify-content: center;">
							<div class="col-md-12">
								<?php
								//$boxes = BoxData::getAll();
								if (isset($_GET["sd"]) && isset($_GET["ed"])):					
									$fechaini = DateTime::createFromFormat('d/m/Y', $_GET['sd']);
									$fechafin = DateTime::createFromFormat('d/m/Y', $_GET['ed']);
									$fechaSd = $fechaini->format('Y-m-d');
									$fechaEd = $fechafin->format('Y-m-d');

									$boxes = BoxData::getAllByDate($fechaSd, $fechaEd);
								else:
									// Crear un objeto DateTime con la fecha y hora actual
									$fecha = new DateTime();
									// Formatear la fecha en 'Y-m-d'
									$fecha_actual = $fecha->format('Y-m-d');
									$boxes = BoxData::getAllByDate($fecha_actual, $fecha_actual);
								endif;
								if (count($boxes) > 0) {
									$total_total = 0;
									?>

									<table class="table table-bordered table-hover datatable" id="boxhistory">
										<thead class="thead-dark">
											<th style="text-align: center;">#</th>
											<th style="text-align: center;">Total</th>
											<th style="text-align: center;">Fecha</th>
											<th style="text-align: center;">Usuario</th>
										</thead>
										<?php foreach ($boxes as $box):
											$sells = SellData::getByBoxId($box->id);
											$fechaObj = new DateTime($box->created_at);
											$fechaFormateada = $fechaObj->format('d/m/Y H:i:s');
											?>
											<tr>
												<td style="width:30px;">
													<a href="./?view=b&id=<?php echo $box->id; ?>" class="btn btn-default btn-xs">
														<i class="fa fa-arrow-right"></i></a>
												</td>
												<td style="text-align: center;">
													<?php
													$total = 0;
													foreach ($sells as $sell) {
														$operations = OperationData::getAllProductsBySellId($sell->id);
														foreach ($operations as $operation) {
															$product = $operation->getProduct();
															$total += $operation->q * $product->price_out;
														}
													}
													$total_total += $total;
													?>
													<?="<b>" . number_format($total, 2, ".", ",") . "</b>"?>
												</td>
												<td style="text-align: center;"><?=$fechaFormateada; ?></td>
												<td style="text-align: center;"><?=$box->user; ?></td>
											</tr>

										<?php endforeach; ?>

									</table>
									<h1>Total: <?php echo number_format($total_total, 2, ".", ","); ?></h1>
									<?php
								} else {

									?>
									<div class="jumbotron">
										<h2>No hay ventas</h2>
										<p>No se ha realizado ninguna venta.</p>
									</div>

								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>			