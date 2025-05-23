<?php
$clients = PersonData::getClients();
?>
<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><i class='fa fa-cart-plus'></i> Reporte de Venta por cliente</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Reportes</a></li>
					<li class="breadcrumb-item active">Venta x Cliente</li>
				</ol>
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<!-- Main content -->
<section class="content">
	<div class="container-fluid">
		<div class="card card-default">
			<div class="card-header">
				<div class="row" style="display: flex; justify-content: center;">
					<div class="col-md-8">
						<form>
							<input type="hidden" name="view" value="sellreports">
							<div class="row">
								<div class="col-md-3">
									<select name="client_id" class="form-control">
										<option value="">TODOS</option>
										<?php foreach ($clients as $p): ?>
											<option value="<?php echo $p->id; ?>"><?php echo $p->name; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-md-3">
									<input type="date" name="sd" value="<?php if (isset($_GET["sd"])) {
										echo $_GET["sd"];
									} else {
										echo date("Y-m-d");
									} ?>" class="form-control">
								</div>
								<div class="col-md-3">
									<input type="date" name="ed" value="<?php if (isset($_GET["ed"])) {
										echo $_GET["ed"];
									} else {
										echo date("Y-m-d");
									} ?>" class="form-control">
								</div>
								<div class="col-md-3">
									<input type="submit" class="btn btn-success btn-block" value="Procesar">
								</div>

							</div>
						</form>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="row" style="display: flex; justify-content: center;">
					<div class="col-md-8">
						<?php if (isset($_GET["sd"]) && isset($_GET["ed"])): ?>
							<?php if ($_GET["sd"] != "" && $_GET["ed"] != ""): ?>
								<?php
								$operations = array();

								if ($_GET["client_id"] == "") {
									$operations = SellData::getAllByDateOp($_GET["sd"], $_GET["ed"], 2);
								} else {
									$operations = SellData::getAllByDateBCOp($_GET["client_id"], $_GET["sd"], $_GET["ed"], 2);
								}
								?>

								<?php if (count($operations) > 0): ?>
									<?php $supertotal = 0; ?>
									<table class="table table-bordered datatable">
										<thead class="thead-dark">
											<!-- <th>Id</th> -->
											<th>Comprobante</th>
											<th>Subtotal</th>
											<th>Descuento</th>
											<th>Total</th>
											<th>Fecha</th>
										</thead>
										<?php foreach ($operations as $operation): ?>
											<tr>
												<!-- <td><?php echo $operation->id; ?></td> -->
												<td><?= $operation->serie . "-" . $operation->comprobante ?>
												<td><?php echo number_format($operation->total, 2, '.', ','); ?></td>
												<td><?php echo number_format($operation->discount, 2, '.', ','); ?></td>
												<td><?php echo number_format($operation->total - $operation->discount, 2, '.', ','); ?>
												</td>
												<td><?php echo $operation->created_at; ?></td>
											</tr>
											<?php
											$supertotal += ($operation->total - $operation->discount);
										endforeach; ?>

									</table>
									<h3>Total de ventas: S/ <?php echo number_format($supertotal, 2, '.', ','); ?></h3>
								<?php else:
									?>
									<script>
										$("#wellcome").hide();
									</script>
									<div class="jumbotron">
										<h2>No hay operaciones</h2>
										<p>El rango de fechas seleccionado no proporciono ningun resultado de operaciones.</p>
									</div>
								<?php endif; ?>
							<?php else: ?>
								<script>
									$("#wellcome").hide();
								</script>
								<div class="jumbotron">
									<h2>Fecha Incorrectas</h2>
									<p>Puede ser que no selecciono un rango de fechas, o el rango seleccionado es incorrecto.
									</p>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>