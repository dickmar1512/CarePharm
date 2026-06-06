<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 font-weight-bold">
					<i class="fa fa-list text-primary"></i> Lista de Productos y Servicios
					<a href="./?view=actstock" class="btn btn-outline-info btn-sm ml-2" title="Sincronizar Stock Real (Reparar inconsistencias)">
						<i class="fas fa-sync-alt"></i> Sincronizar
					</a>
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
					<!-- <a href="./?view=newproduct" class="btn btn-primary"><i class="fas fa-box-open"></i> Agregar
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
						$products = OperationData::getProductsWithMovement();
						if (count($products) > 0) { ?>
							<table class="table table-bordered table-striped table-hover table-sm datatable" id="gridProducts" style="width:100%">
								<thead class="thead-dark text-xs">
									<tr>
										<th>Cód Digemid</th>
										<!-- <th>Imagen</th> -->
										<th>Nombre / Laboratorio</th>
										<!-- <th class="text-center">P. Entrada</th> -->
										<th class="text-center">P. Caja</th>
										<th class="text-center text-primary">P. Unitario</th>
										<!-- <th class="text-center">Anaquel</th> -->
										<th class="text-center bg-info">Stock Real</th>
										<th class="text-center">Min.</th>
										<th class="text-center">Acciones</th>
									</tr>
								</thead>
								<tbody>
								<?php foreach ($products as $product): ?>
									<tr class="text-sm">
										<td class="text-xs font-weight-bold"><?=$product->cod_digemid?></td>
										<!--<td class="text-center">
											<?php if ($product->image != ""): ?>
												<img src="storage/products/<?=$product->image?>" class="img-thumbnail" style="width:35px; height:35px; object-fit: cover;">
											<?php else: ?>
												<i class="fas fa-box text-gray"></i>
											<?php endif; ?>
										</td> -->
										<td>
											<div class="font-weight-bold text-primary"><?=$product->name?></div>
											<small class="badge badge-light border text-muted"><?=$product->laboratorio?></small>
										</td>
										<!-- <td class="text-center"><?=number_format($product->price_in, 2)?></td> -->
										<td class="text-center"><?=number_format($product->price_may, 2)?></td>
										<td class="text-center font-weight-bold text-primary"><?=number_format($product->price_out, 2)?></td>
										<!-- <td class="text-center"><?=$product->anaquel?></td> -->
										<td class="text-center font-weight-bold <?=$product->stock_real <= $product->inventary_min ? 'text-danger' : 'text-dark'?>">
											<?=number_format($product->stock_real, 0)?>
										</td>
										<td class="text-center text-muted"><?=$product->inventary_min?></td>
										<td class="text-center">
											<div class="btn-group">
												<button class="btn btn-xs btn-warning edit-product shadow-sm" data-id="<?=$product->id?>" title="Editar">
													<i class="fas fa-pencil-alt"></i>
												</button>
												<button class="btn btn-xs btn-danger delete-product shadow-sm" data-id="<?=$product->id?>" title="Eliminar">
													<i class="fa fa-trash"></i>
												</button>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
								</tbody>
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
function generarCodigoBarras() {
    $primerDigito = '7';
    $resto = '';
    for ($i = 0; $i < 12; $i++) {
        $resto .= mt_rand(0, 9);
    }
    
    $codigo = $primerDigito . $resto;
    
    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
        $sum += ($i % 2 === 0) ? $codigo[$i] * 1 : $codigo[$i] * 3;
    }
    $digitoControl = (10 - ($sum % 10)) % 10;
    
    return $codigo . $digitoControl;
}

$categories = CategoryData::getAll();
$unidades = UnidadMedidaData::getAll();

$codigo = ProductData::getbarcode();
do {
    $barcodegen = generarCodigoBarras();
} while (in_array($barcodegen, $codigo));


$fecha_actual = date("Y-m-d");
?>
<input type="hidden" id="genbarcode" value="<?= $barcodegen?>">
<script>
    var categories = <?php echo json_encode($categories); ?>;
	var unidades = <?php echo json_encode($unidades); ?>;
	var fechaVencimiento = '<?= date("Y-m-d", strtotime($fecha_actual . "+ 1 year")) ?>';
</script>