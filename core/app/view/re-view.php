<?php
$mysqli = new mysqli('localhost', 'milenio', 'armagedon', 'dbcarepharm', 3306); //BASE DE DATOS
$query = $mysqli->prepare("SELECT * FROM sell WHERE tipo_comprobante = '60' ");
$query->execute();
$query->store_result();
$registros_orden = $query->num_rows;

$id_comprobante_actual_o = $registros_orden + 1;

function generaCerosComprobante($numero)
{
	$empresa = EmpresaData::getDatos();
	$largo_numero = strlen($numero); //OBTENGO EL LARGO DEL NUMERO

	$largo_maximo = 8; //ESPECIFICO EL LARGO MAXIMO DE LA CADENA
	if ($empresa->Emp_Sucursal != 0) {
		$largo_maximo = 8;
	} //PARCHE OTRA SUCURSAL
	$agregar = $largo_maximo - $largo_numero; //TOMO LA CANTIDAD DE 0 AGREGAR
	for ($i = 0; $i < $agregar; $i++) {
		$numero = "0" . $numero;
	} //AGREGA LOS CEROS
	return $numero; //RETORNA EL NUMERO CON LOS CEROS
}

$ORDEN = generaCerosComprobante($id_comprobante_actual_o);
?>
<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><i class='fa fa-shopping-cart'></i> Reabastecer</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
				    <li class="breadcrumb-item"><a href="#">Compras</a></li>
					<li class="breadcrumb-item active">Reabastecer</li>
				</ol>
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
  <!-- Main content -->
<section class="content">
	<div class="container-fluid" style="display: flex; justify-content: center;">
		<div class="card card-default">
			<div class="card-header">
				<div class="form-group">			
						<!-- <div class="row" style="display: flex; justify-content: center;"> -->
							<div class="col-md-10">
								<b>Buscar producto por nombre o por codigo:</b>
								<form id="searchForm" method="GET">
									<div class="row">
										<div class="col-md-6">
											<input type="hidden" name="view" value="re">
											<input type="text" id="product_code2" autofocus name="product" class="form-control">
										</div>
										<div class="col-md-3">
											<button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i>
												Buscar</button>
										</div>
									</div>
								</form>
							</div>
						<!-- </div> -->
				</div>		
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="row" style="display: flex; justify-content: center;">
					<div class="col-md-12">
						<div class="row" style="display: flex; justify-content: center;">
							<div class="col-md-10">
								<?php if (isset($_GET["product"])):
									$products = ProductData::getLikeSinStock($_GET["product"]);

									if (count($products) > 0) {
										?>
										<h3>Resultados de la Busqueda</h3>
										<table class="table table-bordered table-hover">
											<thead>
												<!--th>Codigo</th-->
												<th>#</th>
												<th style="width: 20%">Nombre</th>
												<th>Presentacion</th>
												<!--th>Unidad de Medida</th-->
												<th>Costo unitario</th>
												<th>En inventario</th>
												<th>Cantidad</th>
												<th>Registro Sanitario</th>
												<th>Numero de Lote</th>
												<th>Fecha de Vencimiento</th>
												<th>Laboratorio</th>
												<th style="width:80px;"></th>
											</thead>
											<?php
											$products_in_cero = 0;
											$i = 0;
											foreach ($products as $product):
												// $q = OperationData::getQYesF($product->id);
												$q = $product->stock;
												$i++;
												?>
												<form method="post" action="index.php?view=addtore">
													<tr class="<?php if ($q <= $product->inventary_min) {
														echo "danger";
													} ?>">
														<!--td style="width:80px;"><?php //echo $product->barcode; ?></td-->
														<td><?= $i ?></td>
														<td><?php echo $product->name; ?></td>
														<td><?php echo $product->presentation ?></td>
														<!--td>
																	<?php
																	//$unidad = UnidadMedidaData::getById($product->unit);
																	//echo $unidad->sigla; 
																	?>
																</td-->
														<td>
															<b>
																<input type="text" name="f_price_in"
																	value="<?php echo number_format($product->price_in, 2, '.', ','); ?>"
																	style="width: 50px">
															</b>
														</td>
														<td>
															<?php
															if ($product->is_stock == 1) {
																echo $q;
															} else {
																echo "Sin stock";
															}

															?>

														</td>
														<td>
															<input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
															<input type="number" class="form-control" required name="q" placeholder="0" step="any">
														</td>
														<td><input type="text" class="form-control" name="rs" placeholder="00"></td>
														<td><input type="text" class="form-control" name="nl" placeholder="00"></td>
														<td><input type="date" class="form-control" name="fec_venc"
																placeholder="Fecha de vencimiento">
														<td><input type="text" class="form-control" name="labo"
																value="<?php echo $product->laboratorio ?>"></td>
														</td>
														<td style="width:80px;">
															<button type="submit" class="btn btn-success"><i
																	class="glyphicon glyphicon-refresh"></i>
																Agregar</button>
														</td>
													</tr>
												</form>
											<?php endforeach; ?>
										</table>
										<?php
									}
									?>
									<hr>
								<?php else:
									?>						
								<?php endif; ?>

								<?php if (isset($_SESSION["errors"])): ?>
									<h2>Errores</h2>
									<p></p>
									<table class="table table-bordered table-hover">
										<tr class="danger">
											<th>Codigo</th>
											<th>Producto</th>
											<th>Mensaje</th>
										</tr>
										<?php foreach ($_SESSION["errors"] as $error):
											$product = ProductData::getById($error["product_id"]);
											?>
											<tr class="danger">
												<td><?php echo $product->id; ?></td>
												<td><?php echo $product->name; ?></td>
												<td><b><?php echo $error["message"]; ?></b></td>
											</tr>
										<?php endforeach; ?>
									</table>
									<?php
									unset($_SESSION["errors"]);
								endif; ?>

								<!--- Carrito de compras :) -->
								<?php if (isset($_SESSION["reabastecer"])):
									$total = 0;
									?>
									<h2>Lista de Reabastecimiento</h2>
									<table class="table table-bordered table-hover">
										<thead>
											<!--th style="width:30px;">Codigo</th-->
											<th style="width:30px;">Cantidad</th>
											<!--th style="width:30px;">Unidad</th-->
											<th>Producto</th>
											<th>Regitro Sanitario</th>
											<th>Numero de Lote</th>
											<th>Fecha Vencimiento</th>
											<th>Laboratorio</th>
											<th>Precio Unitario</th>
											<th>Precio Total</th>
											<th></th>
										</thead>
										<?php
										print_r($_SESSION["reabastecer"]);
										foreach ($_SESSION["reabastecer"] as $p):
											$product = ProductData::getById($p["product_id"]);
											?>
											<tr>
												<!--td><?php //echo $product->barcode; ?></td-->
												<td><?php echo $p["q"]; ?></td>
												<!--td>
												<?php
												//$unidad = UnidadMedidaData::getById($product->unit);
												//echo $unidad->sigla; 
												?>
												</td-->
												<td><?php echo $product->name; ?></td>
												<td><?= $p["rs"] ?></td>
												<td><?= $p["nl"] ?></td>
												<td><?php echo $p["fec_venc"] ?></td>
												<td><?php echo $p["labo"]; ?>
												<td><b><?php echo number_format($p["price_in"], 2, '.', ','); ?></b></td>
												<td>
													<b><?php $pt = $p["price_in"] * $p["q"];
													$total += $pt;
													echo number_format($pt, 2, '.', ','); ?>
													</b>
												</td>
												<td style="width:30px;">
													<a href="index.php?view=clearre&product_id=<?php echo $product->id; ?>"
														class="btn btn-danger">
														<i class="glyphicon glyphicon-remove"></i> Cancelar
													</a>
												</td>
											</tr>
										<?php endforeach; ?>
									</table>
									<form method="post" class="form-horizontal" id="processsell" action="index.php?view=processre">
										<h2>Ingrese documento d compra:</h2>
										<div class="form-group">
											<label for="inputEmail1" class="col-lg-2 control-label">TIPO COMPROBANTE</label>
											<div class="col-lg-10">
												<label class="radio-inline"><input type="radio" name="optTipoComprobante"
														value="3">Boleta</label>
												<label class="radio-inline"><input type="radio" name="optTipoComprobante" value="1"
														checked>Factura</label>
												<label class="radio-inline"><input type="radio" name="optTipoComprobante" value="60">Ingreso
													Diverso</label>
											</div>
										</div>
										<div class="form-group">
											<label for="inputEmail1" class="col-xs-2 control-label">FECHA EMISIÓN:</label>
											<div class="col-xs-2">
												<input type="text" name="fecemi" required class="form-control" id="fecemi"
													placeholder="Fecha emision">
											</div>
											<label for="inputEmail1" class="col-lg-1 control-label">PROVEEDOR:</label>
											<div class="col-lg-4">
												<?php
												$clients = PersonData::getProviders();
												?>
												<select name="client_id" class="form-control">
													<option value="">-- NINGUNO --</option>
													<?php foreach ($clients as $client): ?>
														<option value="<?php echo $client->id; ?>">
															<?php echo $client->name . " " . $client->lastname; ?></option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label for="inputEmail1" class="col-xs-2 control-label">SERIE:</label>
											<div class="col-xs-2">
												<input type="text" name="serie" required class="form-control" id="serie"
													placeholder="SERIE COMPROBANTE">
											</div>

											<label for="inputEmail1" class="col-xs-1 control-label">NUMERO:</label>
											<div class="col-xs-2">
												<input type="text" name="comprobante" required class="form-control" id="comprobante"
													placeholder="NUMERO COMPROBNTE">
											</div>
										</div>
										<div class="form-group">
											<label for="inputEmail1" class="col-lg-2 control-label">EFECTIVO:</label>
											<div class="col-lg-2">
												<input type="text" name="money" required class="form-control" id="money"
													value="<?php echo $total; ?>" placeholder="Efectivo">
												<input type="hidden" name="total" required class="form-control" id="total"
													value="<?php echo $total; ?>">
											</div>
											<label for="inputEmail1" class="col-lg-1 control-label">TOTAL:</label>
											<div class="col-lg-2">
												<input type="text" name="totalImporte" required class="form-control" id="totalImporte"
													value="<?php echo number_format($total, 2, '.', ','); ?>" disabled>
												<input name="is_oficial" type="hidden" value="1">
											</div>
										</div>
										<div class="form-group">
											<div class="col-lg-offset-2 col-lg-8">
												<div class="checkbox">
													<label>
														<a href="index.php?view=clearre" class="btn btn-lg btn-danger"><i
																class="glyphicon glyphicon-remove"></i> Cancelar</a>
														<button class="btn btn-lg btn-primary"><i class="fa fa-refresh"></i> Procesar
															Reabastecimiento</button>
													</label>
												</div>
											</div>
										</div>
									</form>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</seccion>			