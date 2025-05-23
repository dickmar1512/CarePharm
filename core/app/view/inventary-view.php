<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><i class='fas fa-chart-bar'></i> Inventario de Productos</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Inventarios</a></li>
					<li class="breadcrumb-item active">Inventario</li>
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
					<div class="col-md-10">
						<h3> Lista de Productos</h3>
					</div>
				</div>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="row" style="display: flex; justify-content: center;">
					<div class="col-md-10">
						<div class="row">
							<div class="col-md-12">
								<?php
								$products = ProductData::getAll();
								if (count($products) > 0) {
									?>
									<table class="table table-bordered table-hover datatable">
										<thead class="thead-dark">
											<th>Codigo</th>
											<th>Nombre</th>
											<th>Disponible</th>
											<th></th>
										</thead>
										<?php foreach ($products as $product):
											$q = OperationData::getQYesF($product->id);
											?>
											<tr class="
												<?php
												if ($product->is_stock == 0) {
													echo "";
												} else if ($q <= $product->inventary_min / 2) {
													echo "danger";
												} else if ($product->stock <= $product->inventary_min) {
													echo "warning";
												}
												?>">
												<td><?php echo $product->barcode; ?></td>
												<td><?php echo $product->name; ?></td>
												<td>
													<?php
													if ($product->is_stock == 0) {
														echo "Ilimitado";
													} else if ($product->stock < 0) {
														echo -1 * $product->stock;
													} else {
														echo $product->stock;
													}
													?>

												</td>
												<td style="min-width:100px;">
													<a href="index.php?view=input&product_id=<?php echo $product->id; ?>" class="btn btn-xs btn-primary"><i class="fas fa-arrow-circle-up"></i> Alta</a>
													<a href="index.php?view=history&product_id=<?php echo $product->id; ?>"
														class="btn btn-xs btn-success"><i class="fas fa-file"></i>
														GENERAR KARDEX</a>
												</td>
											</tr>
										<?php endforeach; ?>
									</table>

									<?php
								} else {
									?>
									<div class="jumbotron">
										<h2>No hay productos</h2>
										<p>No se han agregado productos a la base de datos, puedes agregar uno dando click
											en el boton <b>"Agregar
												Producto"</b>.</p>
									</div>
									<?php
								}

								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>