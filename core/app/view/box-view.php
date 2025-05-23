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
 <section class="content">
	<div class="container-fluid">
		<div class="card card-default">
			<div class="card-header">
				<div class="row" style="display: flex; justify-content: right;">
					<div class="col-md-8">
						<div class="btn-group pull-right">
							<a href="./index.php?view=boxhistory" class="btn btn-primary "><i class="fa fa-clock-o"></i>
								Historial</a>
							<a href="./index.php?view=processbox" class="btn btn-primary ">Procesar Ventas <i
									class="fa fa-arrow-right"></i></a>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="row" style="display: flex; justify-content: center;">
					<div class="col-md-8">
						<?php
						$products = SellData::getSellsUnBoxed();
						if (count($products) > 0) {
							$total_total = 0;
							$i = 1;
							?>
							<table class="table table-bordered table-hover datatable" id="box">
								<thead>
									<tr>
										<th>#</th>
										<th>Comprobante</th>
										<th>Total</th>
										<th>Fecha</th>
										<th>Usuario</th>
									</tr>
								</thead>
								<?php foreach ($products as $sell): ?>
									<tr>
										<td><?= $i++ ?></td>
										<td><?= $sell->serie . "-" . $sell->comprobante ?>
										<td style="text-align: center;">
											<?php
											$operations = OperationData::getAllProductsBySellId($sell->id);
											$total = 0;
											foreach ($operations as $operation) {
												$product = $operation->getProduct();
												$total += $operation->q * ($operation->prec_alt - $operation->descuento);
											}
											$total_total += $total;
											echo "<b> " . number_format($total, 2, ".", ",") . "</b>";

											?>
										<td style="text-align: center;"><?php echo $sell->created_at; ?></td>
										<td style="text-align: center;"><?php echo $sell->user; ?></td>
									</tr>
								<?php endforeach; ?>
							</table>
							<h1>Total: <?php echo "S/ " . number_format($total_total, 2, ".", ","); ?></h1>
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