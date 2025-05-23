<div class="row" style="display: flex; justify-content: center;">
	<div class="col-md-10">
		<div class="row" style="display: flex; justify-content: center;">
			<div class="col-md-8">
				<!-- Single button -->
				<div class="btn-group pull-right">
					<a href="./index.php?view=boxhistory" class="btn btn-default"><i class="fa fa-clock-o"></i>
						Historial</a>
					<div class="btn-group">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-download"></i> Descargar <span class="caret"></span>
						</button>
						<ul class="dropdown-menu pull-right" role="menu">
							<li><a href="report/box-word.php?id=<?php echo $_GET["id"]; ?>">Word 2007 (.docx)</a></li>
						</ul>
					</div>
				</div>
				<h1><i class='fa fa-archive'></i> Corte de Caja #<?php echo $_GET["id"]; ?></h1>
				<div class="clearfix"></div>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row" style="display: flex; justify-content: center;">
			<div class="col-md-8">

				<?php
				$products = SellData::getByBoxId($_GET["id"]);
				if (count($products) > 0) {
					$total_total = 0;
					?>
					<table class="table table-bordered table-hover	">
						<thead>
							<th>#</th>
							<th>Comprobante</th>
							<th>Total</th>
							<th>Fecha</th>
							<th>Usuario</th>
						</thead>
						<?php foreach ($products as $sell): ?>
							<?php
							if ($sell->serie == "F001") {
								$tipodoc = 1;
							} else {
								$tipodoc = 3;
							}
							?>
							<tr>
								<td style="width:30px;">
									<a href="./index.php?view=onesell&id=<?php echo $sell->id;?>&tipodoc=<?=$tipodoc?>"
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
										$total += $operation->q * $product->price_out;
									}
									$total_total += $total;
									echo "<b>" . number_format($total, 2, ".", ",") . "</b>";

									?>

								</td>
								<td><?php echo $sell->created_at; ?></td>
								<td><?php echo $sell->user; ?></td>
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