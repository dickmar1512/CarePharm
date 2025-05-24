<div class="row" style="display: flex; justify-content: center;">
	<div class="col-md-10">

		<div class="row" style="display: flex; justify-content: center;">
			<div class="col-md-8">
				<!-- Single button -->
				<div class="btn-group pull-right">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						<i class="fa fa-download"></i> Descargar <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a href="report/boxhistory-word.php">Word 2007 (.docx)</a></li>
					</ul>
				</div>
				<h1><i class='fa fa-archive'></i> Historial de Caja</h1>
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="row" style="display: flex; justify-content: center;">
			<div class="col-md-8">
				<?php
				$boxes = BoxData::getAll();
				$products = SellData::getSellsUnBoxed();
				if (count($boxes) > 0) {
					$total_total = 0;
					?>

					<table class="table table-bordered table-hover datatable" id="boxhistory">
						<thead>
							<th style="text-align: center;">#</th>
							<th style="text-align: center;">Total</th>
							<th style="text-align: center;">Fecha</th>
							<th style="text-align: center;">Usuario</th>
						</thead>
						<?php foreach ($boxes as $box):
							$sells = SellData::getByBoxId($box->id);
							// $notacomprobar = $sells->serie . "-" . $sells->comprobante;
							// $probar = Not_1_2Data::getByIdComprobado($notacomprobar);
							?>
							<tr>
								<td style="width:30px;">
									<a href="././?view=b&id=<?php echo $box->id; ?>" class="btn btn-default btn-xs">
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
									echo "<b>" . number_format($total, 2, ".", ",") . "</b>";

									?>

								</td>
								<td style="text-align: center;"><?php echo $box->created_at; ?></td>
								<td style="text-align: center;"><?php echo $box->user; ?></td>
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