<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><i class="fa fa-list"></i>
					Lista de Productos y Servicios
					(<a href="index.php?view=actstock" class="btn btn-xs btn-info">
						<i class="fas fa-pencil-alt"></i>
					</a>)
				</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Productos - Servicios</a></li>
					<li class="breadcrumb-item active">Lista</li>
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
				<div class="btn-group float-sm-right">
					<!-- <a href="index.php?view=newproduct" class="btn btn-primary"><i class="fas fa-box-open"></i> Agregar
						Producto o Servicio</a> -->
					<button id="openModalNuevoProducto" class="btn btn-primary">
						<i class="fas fa-box-open"></i>
						Agregar Producto o Servicio
					</button>	
				</div>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="row" style="display: flex; justify-content: center;">
					<div class="col-md-10">
						<?php
						$products = ProductData::getAll2();
						if (count($products) > 0) { ?>
							<table class="table table-responsive table-hover table-sm datatable" id="gridProducts">
								<thead class="thead-dark">
									<th scope="col">Codigo</th>
									<th>Imagen</th>
									<th>Nombre</th>
									<th>Precio Entrada</th>
									<th>Precio Por Mayor</th>
									<th>Precio Salida</th>
									<th>Anaquel</th>
									<th>Inventario</th>
									<th>Minima</th>
									<th>Activo</th>
									<th></th>
								</thead>
								<?php foreach ($products as $product): ?>
									<tr>
										<td><?php echo $product->barcode; ?></td>
										<td>
											<?php if ($product->image != ""): ?>
												<img src="storage/products/<?php echo $product->image; ?>" style="width:30px;">
											<?php endif; ?>
										</td>
										<td><?php echo $product->name; ?></td>
										<td><?php echo number_format($product->price_in, 2, '.', ','); ?></td>
										<td><?php echo number_format($product->price_may, 2, '.', ','); ?></td>
										<td><?php echo number_format($product->price_out, 2, '.', ','); ?></td>
										<td class="text-center">
											<?php
											/*$unidad = UnidadMedidaData::getById($product->unit);
																																																												  echo $unidad->sigla; */
											echo $product->anaquel;
											?>
										</td>
										<!--td><?php if ($product->category_id != null) {
											echo $product->getCategory()->name;
										} else {
											echo "<center>----</center>";
										} ?></td-->
										<td><?php echo $product->stock; ?></td>
										<td><?php echo $product->inventary_min; ?></td>
										<td><?php if ($product->is_active): ?><i class="fa fa-check"></i><?php endif; ?>
										</td>


										<td style="width:70px;">
											<a href="#" class="btn btn-xs btn-warning edit-product" data-id="<?php echo $product->id; ?>">
												<i class="fas fa-pencil-alt"></i>
											</a>
											<!-- <a href="#" class="btn btn-warning btn-xs edit-client"	data-id="<?php echo $client->id; ?>">Editar</a>	 -->
											<a href="index.php?view=delproduct&id=<?php echo $product->id; ?>"
												class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>
										</td>
									</tr>
								<?php endforeach; ?>
							</table>
							<div class="clearfix"></div>

							<?php
						} else {
							?>
							<div class="jumbotron">
								<h2>No hay productos/servicios</h2>
								<p>No se han agregado productos/servicios a la base de datos, puedes agregar uno
									dando click
									en el
									boton
									<b>"Agregar Producto"</b>.
								</p>
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
<?php
$categories = CategoryData::getAll();
$codigo = ProductData::getbarcode();
$unidades = UnidadMedidaData::getAll();
foreach ($codigo as $cod) {
    $barcode = $cod->barcode;
}

$fecha_actual = date("Y-m-d");
?>
<input type="hidden" id="genbarcode" value="<?= $barcode?>">
<script>
    var categories = <?php echo json_encode($categories); ?>;
	var unidades = <?php echo json_encode($unidades); ?>;
	var fechaVencimiento = '<?= date("Y-m-d", strtotime($fecha_actual . "+ 1 year")) ?>';
</script>