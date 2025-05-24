<div class="row" style="display: flex; justify-content: center;">
	<div class="col-md-10">
		<?php
		if (isset($_GET["product_id"])):
			$product = ProductData::getById($_GET["product_id"]);
			$operations = OperationData::getAllByProductId($product->id);
			?>
			<div class="row" style="display: flex; justify-content: center;">
				<div class="col-md-10">
					<div class="btn-group pull-right">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-download"></i> Descargar <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="report/history-word.php?id=<?php echo $product->id; ?>">Word 2007 (.docx)</a></li>
						</ul>
					</div>
					<h1><b><?php echo $product->name;?></b> <small>KARDEX</small></h1>
					<p style="font-size-adjust: 20px;">En esta seccion usted podra descargar el Kardex del mes que desea en
						formato EXCEL</p>
				</div>
			</div>
			<div class="row" style="display: flex; justify-content: center;">
				<div class="col-md-4">
					<?php
					$itotal = OperationData::GetInputQYesF($product->id);
					?>
					<div class="jumbotron">
						<center>
							<h2>Entradas</h2>
							<h1><?php echo $itotal; ?></h1>
						</center>
					</div>
					<br>
				</div>
				<div class="col-md-4">
					<?php
					$total = OperationData::GetQYesF($product->id);
					?>
					<div class="jumbotron">
						<center>
							<h2>Disponibles</h2>
							<h1><?php echo $product->stock; ?></h1>
						</center>
					</div>
					<br>
				</div>
				<div class="col-md-4">
					<?php
					$ototal = -1 * OperationData::GetOutputQYesF($product->id);
					?>
					<div class="jumbotron">
						<center>
							<h2>Salidas</h2>
							<h1><?php echo $ototal; ?></h1>
						</center>
					</div>
					<br>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?php if (count($operations) > 0): ?>
						<table class="table table-bordered table-hover">
							<thead>
								<th></th>
								<th>Cantidad</th>
								<th>Precio Entrada</th>
								<th>Tipo</th>
								<th>Fecha</th>
								<th></th>
							</thead>
							<?php foreach ($operations as $operation): ?>
								<tr>
									<td></td>
									<td><?php echo $operation->q; ?></td>
									<td>S/ <?php echo number_format($operation->cu, 2, ".", ","); ?></td>
									<td><?php echo $operation->getOperationType()->name; ?></td>
									<td><?php echo $operation->created_at; ?></td>
									<td style="width:40px;"><a href="#" id="oper-<?php echo $operation->id; ?>"
											class="btn tip btn-xs btn-danger" title="Eliminar"><i
												class="glyphicon glyphicon-trash"></i></a> </td>
									<script>
										$("#oper-" + <?php echo $operation->id; ?>).click(function () {
											x = confirm("Estas seguro que quieres eliminar esto ??");
											if (x == true) {
												window.location = "./?view=deleteoperation&ref=history&pid=<?php echo $operation->product_id; ?>&opid=<?php echo $operation->id; ?>";
											}
										});

									</script>
								</tr>
							<?php endforeach; ?>
						</table>
					<?php endif; ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="row pull-left">
						<form action="excel_kardex.php" method="POST" onsubmit="return enviado3()">
							<div class="col-md-4">
								<div class="form-group">
									<input type="hidden" name="id_producto" value="<?php echo $_GET["product_id"] ?>"
										id="id_producto">
									<select class="form-control" width="30px" id="selMes" name="selMes">
										<option value="">::Mes::</option>
										<option value="1">Enero</option>
										<option value="2">Febrero</option>
										<option value="3">Marzo</option>
										<option value="4">Abril</option>
										<option value="5">Mayo</option>
										<option value="6">Junio</option>
										<option value="7">Julio</option>
										<option value="8">Agosto</option>
										<option value="9">Setiembre</option>
										<option value="10">Octubre</option>
										<option value="11">Noviembre</option>
										<option value="12">Diciembre</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select class="form-control" width="30px" id="selAnio" name="selAnio">
										<option value="2018">2018</option>
										<option value="2019">2019</option>
										<option value="2020">2020</option>
										<option value="2021">2021</option>
										<option value="2022">2022</option>
										<option value="2023">2023</option>
										<option value="2024">2024</option>
										<option value="2025">2025</option>
										<option value="2026">2026</option>
										<option value="2027">2027</option>
										<option value="2028">2028</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<button type="submit" class="btn btn-success" id="btnGenerarKardex"><i
											class="fa fa-download"></i> Descargar Kardex</button>
									<!-- <a href="excel_kardex.php?id_producto=<?php echo $_GET["product_id"] ?>" class="btn btn-success" id="generar_excel"><i class="fa fa-download"></i> Descargar Kardex</a> -->
								</div>
							</div>
						</form>
					</div>
					<div class="hide">
						<?php if (count($operations) > 0): ?>
							<table class="table table-bordered table-hover">
								<thead>
									<th></th>
									<th>Cantidad</th>
									<th>Tipo</th>
									<th>Fecha</th>
									<th></th>
								</thead>
								<?php foreach ($operations as $operation): ?>
									<tr>
										<td></td>
										<td><?php echo $operation->q; ?></td>
										<td><?php echo $operation->getOperationType()->name; ?></td>
										<td><?php echo $operation->created_at; ?></td>
										<td style="width:40px;"><a href="#" id="oper-<?php echo $operation->id; ?>"
												class="btn tip btn-xs btn-danger hide" title="Eliminar"><i
													class="glyphicon glyphicon-trash"></i></a> </td>
										<script>
											$("#oper-" + <?php echo $operation->id; ?>).click(function () {
												x = confirm("Estas seguro que quieres eliminar esto ??");
												if (x == true) {
													window.location = "./?view=deleteoperation&ref=history&pid=<?php echo $operation->product_id; ?>&opid=<?php echo $operation->id; ?>";
												}
											});
										</script>
									</tr>
								<?php endforeach; ?>
							</table>
						<?php endif; ?>
					</div>
				</div>
			</div>

		<?php endif; ?>
	</div>
</div>
<script type="text/javascript">
	var cuenta = 0;

	function enviado3() {
		var selMes = $('#selMes').val();
		var selAnio = $('#selAnio').val();

		if (selMes == "" || selAnio == "") {
			alert('Campos mes y año');

			return false;
		}
	}
</script>