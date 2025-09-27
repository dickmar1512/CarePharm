<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><i class='bx bxs-shopping-bag'></i> Reporte de Compras</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Reportes</a></li>
					<li class="breadcrumb-item active">Registro Compra</li>
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
				<h4>Compras Reabastecimientos</h4>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="row" style="display: flex; justify-content: center;">
					<div class="col-md-10">
						<?php
						$products = SellData::getRes();
						$total = 0;
						$admin = UserData::getById($_SESSION["user_id"])->is_admin;

						if (count($products) > 0) {
							?>
							<br>
							<table class="table table-bordered table-hover datatable">
								<thead class="thead-dark">
									<th>Ver</th>
									<th>Comprobante</th>
									<th>Cantidad</th>
									<th>Imp. Total</th>
									<th>Fecha</th>
									<?php if ($admin == 1) { ?>
										<th>Accion</th>
									<?php } ?>
								</thead>
								<?php foreach ($products as $sell): 
										$fechaObj = new DateTime($sell->created_at);
										$fechaFormateada = $fechaObj->format('d/m/Y H:i:s');
									?>
									<tr>
										<td style="width:30px;"><a href="?view=onere&id=<?php echo $sell->id; ?>"
												class="btn btn-xs btn-default"><i class="fas fa-eye"></i></a>
										</td>
										<td><?= $sell->serie . "-" . $sell->comprobante ?></td>
										<td style="text-align: center;">
											<?php
											$operations = OperationData::getAllProductsBySellId($sell->id);
											echo count($operations);
											?>
										</td>	
										<td style="text-align: right;">
											<?php
											foreach ($operations as $operation) {
												$product = $operation->getProduct();
												$total += $operation->q * $product->price_in;
											}
											echo "<b> " . number_format($total,2,'.',',') . "</b>";

											?>

										</td>
										<td><?=$fechaFormateada?></td>
										<?php if ($admin == 1) { ?>
											<td style="width:30px;">
												<a href="./?view=editre&id=<?=$sell->id; ?>"
													class="btn btn-xs btn-info mr-2"><i class="fa fa-pencil"></i></a>
												<a href="./?view=delre&id=<?=$sell->id; ?>"
													class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>
											</td>
										<?php } ?>
									</tr>

								<?php endforeach; ?>
							</table>
							<?php
						} else {
							?>
							<div class="jumbotron">
								<h2>No hay datos</h2>
								<p>No se ha realizado ninguna operacion.</p>
							</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>