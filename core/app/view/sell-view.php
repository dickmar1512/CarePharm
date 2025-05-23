<?php
####################### NUMERO DE REGISTRO DE COMPROBANTES ###############################
$mysqli = new mysqli('localhost', 'milenio', 'armagedon', 'dbcarepharm', 3306); //BASE DE DATOS

//cantidad de boleta en la version 1.2
$query = $mysqli->prepare("SELECT * FROM boleta");//TABLA
$query->execute();
$query->store_result();
$registros = $query->num_rows;

$query = $mysqli->prepare("SELECT * FROM factura");//TABLA
$query->execute();
$query->store_result();
$registros_factura = $query->num_rows;

$query = $mysqli->prepare("SELECT * FROM sell WHERE tipo_comprobante = '70' ");
$query->execute();
$query->store_result();
$registros_orden = $query->num_rows;

$id_comprobante_actual = $registros + 1;
$id_comprobante_actual_f = $registros_factura + 1;
$id_comprobante_actual_o = $registros_orden + 1;

$empresa = EmpresaData::getDatos();
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

function generaCerosSerie($numero2)
{
	$empresa = EmpresaData::getDatos();
	$largo_numero = strlen($numero2);//OBTENGO EL LARGO DEL NUMERO
	$largo_maximo = 3;//ESPECIFICO EL LARGO MAXIMO DE LA CADENA
	if ($empresa->Emp_Sucursal != 0) {
		$largo_maximo = 2;
	} //PARCHE OTRA SUCURSAL
	$agregar = $largo_maximo - $largo_numero;   //TOMO LA CANTIDAD DE 0 AGREGAR
	for ($i = 0; $i < $agregar; $i++) {
		$numero2 = "0" . $numero2;
	} //AGREGA LOS CEROS
	return $numero2; //RETORNA EL NUMERO CON LOS CEROS
}

//COMPROBANTE PRIMERA SERIE B001
$NUMERO_COMPROBANTE = generaCerosComprobante($id_comprobante_actual);
//CAPTAMOS SERIE DE B001 AL B999
$NUMERO_SERIE = (int) (($id_comprobante_actual / 99999999) + 1);
if ($empresa->Emp_Sucursal == 0) {
	$SERIE = "B" . generaCerosSerie($NUMERO_SERIE);
} else if ($empresa->Emp_Sucursal == 1) {
	$SERIE = "BB" . generaCerosSerie($NUMERO_SERIE);
} else if ($empresa->Emp_Sucursal == 2) {
	$SERIE = "BD" . generaCerosSerie($NUMERO_SERIE);
} else if ($empresa->Emp_Sucursal == 3) {
	$SERIE = "BF" . generaCerosSerie($NUMERO_SERIE);
} else if ($empresa->Emp_Sucursal == 4) {
	$SERIE = "BH" . generaCerosSerie($NUMERO_SERIE);
} else if ($empresa->Emp_Sucursal == 5) {
	$SERIE = "BJ" . generaCerosSerie($NUMERO_SERIE);
}


//COMPROBANTE SERIE B001 AL SUPERIOR
if ($NUMERO_SERIE > 1) {
	$NUMERO_COMPROBANTE = $id_comprobante_actual % 99999999;
}
$COMPROBANTE = generaCerosComprobante($NUMERO_COMPROBANTE);

//COMPROBANTE PRIMERA SERIE F001
$NUMERO_COMPROBANTE_F = generaCerosComprobante($id_comprobante_actual_f);
//CAPTAMOS SERIE DE F001 AL F999
$NUMERO_SERIE_F = (int) (($id_comprobante_actual_f / 99999999) + 1);

if ($empresa->Emp_Sucursal == 0) {
	$SERIE_F = "F" . generaCerosSerie($NUMERO_SERIE_F);
} else if ($empresa->Emp_Sucursal == 1) {
	$SERIE_F = "FB" . generaCerosSerie($NUMERO_SERIE_F);
} else if ($empresa->Emp_Sucursal == 2) {
	$SERIE_F = "FD" . generaCerosSerie($NUMERO_SERIE_F);
} else if ($empresa->Emp_Sucursal == 3) {
	$SERIE_F = "FF" . generaCerosSerie($NUMERO_SERIE_F);
} else if ($empresa->Emp_Sucursal == 4) {
	$SERIE_F = "FH" . generaCerosSerie($NUMERO_SERIE_F);
} else if ($empresa->Emp_Sucursal == 5) {
	$SERIE_F = "FJ" . generaCerosSerie($NUMERO_SERIE_F);
}
//COMPROBANTE SERIE B001 AL SUPERIOR
if ($NUMERO_SERIE_F > 1) {
	$NUMERO_COMPROBANTE_F = $id_comprobante_actual_f % 99999999;
}
$COMPROBANTE_F = generaCerosComprobante($NUMERO_COMPROBANTE_F);


$ORDEN = generaCerosComprobante($id_comprobante_actual_o);
#####################################################################################
$empresa = EmpresaData::getDatos();
?>
<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><i class='fa fa-shopping-cart'></i> Añadir Producto</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
				    <li class="breadcrumb-item"><a href="#">Venta</a></li>
					<li class="breadcrumb-item active">Generar Venta</li>
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
				<h2 class="card-title">ELIJA COMPROBANTE</h2>
				<div class="text-center">
					<div class="icheck-primary d-inline">
						<input type="radio" id="optTipoComprobante1" name="optTipoComprobante" value="3" checked>
						<label for="optTipoComprobante1">BOLETA (<span style="color:red"><b>F4</b></span>)</label>
					</div>
					<div class="icheck-success d-inline">
						<input type="radio" id="optTipoComprobante2" name="optTipoComprobante" value="1">
						<label for="optTipoComprobante2">FACTURA (<span style="color:red"><b>F5</b></span>)</label>
					</div>
					<div class="icheck-danger d-inline">
						<input type="radio" id="optTipoComprobante3" name="optTipoComprobante" value="0">
						<label for="optTipoComprobante3">NOTA VENTA (<span style="color:red"><b>F6</b></span>)</label>
					</div>
				</div>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="row" style="display: flex; justify-content: center;">
					<div class="col-md-8">
						<?php
						$permiso = PermisoData::get_permiso_x_key('proforma');
						$permiso2 = PermisoData::get_permiso_x_key('comprobantes_fisicos');
						$permiso3 = PermisoData::get_permiso_x_key('otro_documento_no_dni');
						$total = 0;
						$dsctotal = 0;
						?>
						<div id="comprobante_boleta">
							<form action="?view=addboleta" id="formboleta" class="form-horizontal"
								method="post" onsubmit="enviado2(3,event)">
								<input type="hidden" name="person_id" value="">
								<div class="row">
									<div class="col-md-12">
										<input type="hidden" name="RUC" value="<?php echo $empresa->Emp_Ruc; ?>">
										<input type="hidden" name="TIPO" value="03">
										<input type="hidden" name="tipOperacion" value="0101">
										<input type="hidden" name="fecVencimiento" value="-">
										<input type="hidden" name="codLocalEmisor" value="0000">
										<input type="hidden" name="tipMoneda" value="PEN">
										<input type="hidden" name="porDescGlobal" value="-">
										<input type="hidden" name="mtoDescGlobal" value="0">
										<input type="hidden" name="mtoBasImpDescGlobal" value="0">
										<input type="hidden" name="sumTotTributos" value="0">
										<input type="hidden" name="sumDescTotal" value="<?= $dsctotal ?>">
										<input type="hidden" name="sumOtrosCargos" value="0">
										<input type="hidden" name="sumTotalAnticipos" value="0">
										<input type="hidden" name="ublVersionId" value="2.1">
										<input type="hidden" name="customizationId" value="2.0">
										<div class="form-group d-none">
											<label for="inputEmail1" class="col-lg-2 control-label">Fecha:</label>
											<div class="col-md-6">
												<input type="date" name="fecEmision" id="fecEmision"
													class="form-control" value="<?php echo date("Y-m-d"); ?>">
											</div>
										</div>

										<div class="row">
											<div class="col-md-3">
												<div class="form-group">
													<label for="inputEmail1"
														class="col-lg-8 control-label text-sm">FECHA
														EMISION:</label>
													<div class="col-md-12">
														<input type="date" name="fecEmision" id="fecEmision"
															class="form-control" value="<?php echo date("Y-m-d"); ?>">
													</div>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label for="inputEmail1"
														class="col-lg-6 control-label">HORA:</label>
													<div class="col-md-12">
														<input type="text" name="horEmision" id="horEmision"
															class="form-control" value="<?php echo date('H:i:s'); ?>">
													</div>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<!-- <label for="inputEmail1"
														class="col-lg-8 control-label text-sm">COMPROBANTE:</label>
													<div class="col-md-12">
														<select name="selEstado" class="form-control text-sm">
															<option value="1">Electrónico</option>
														</select>
													</div> -->
													<label for="formaPago"
														class="col-lg-8 control-label text-sm">FORMA DE PAGO:</label>
													<div class="col-md-12">
														<select name="formaPago" class="form-control text-sm">
															<option value="1" selected>Contado</option>
															<option value="2">Crédito</option>
														</select>
													</div>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-3">
												<div class="form-group">
													<label for="SERIE"
														class="col-lg-8 control-label">SERIE:</label>
													<div class="col-md-12">
														<input type="text" name="SERIE" id="SERIE" class="form-control"
															value="<?php echo $SERIE; ?>">
													</div>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label for="COMPROBANTE"
														class="col-lg-8 control-label">CORRELATIVO:</label>
													<div class="col-md-12">
														<input type="text" name="COMPROBANTE" id="COMPROBANTE"
															class="form-control" value="<?php echo $COMPROBANTE; ?>">
													</div>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label for="selTipoPago" class="col-lg-6 control-label text-sm">
														TIPO DE PAGO:
													</label>
													<div class="col-md-12">
														<select name="selTipoPago" class="form-control text-sm">
															<option value="1" selected>Efectivo</option>
															<option value="2">Plin</option>
															<option value="3">Yape</option>
															<option value="4">Tarjeta débito</option>
															<option value="5">Tarjeta crédito</option>
														</select>
													</div>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-3">
												<div class="form-group">
													<label for="numDocUsuario" class="col-lg-8 control-label">
														<?php
														if ($permiso3->Pee_Valor == 1) {
															echo 'Documento: ';
														} else {
															echo 'DNI';
														}
														?>

													</label>
													<div class="col-md-12">
														<input type="number" name="numDocUsuario" id="numDocUsuario"
															class="form-control" placeholder="DNI" onblur="
												<?php
												if ($permiso3->Pee_Valor == 1) {
													echo 'validar_no_dni()';
												} else {
													echo 'validar_dni()';
												} ?>" required="" value="00000000">
													</div>
												</div>
											</div>
											<div class="col-md-8">
												<div class="form-group">
													<label for="rznSocialUsuario"
														class="col-lg-6 control-label">CLIENTE:</label>
													<div class="col-md-11">
														<input type="text" name="rznSocialUsuario" class="form-control"
															id="rznSocialUsuario" value="Cliente General"
															placeholder="Cliente (Opcional)" required="">
													</div>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-3">
												<div class="form-group">
													<label for="codUbigeoCliente"
														class="col-lg-8 control-label">DISTRITO:</label>
													<div class="col-md-12">
														<select name="codUbigeoCliente" id="codUbigeoCliente"
															class="form-control">
															<option value="">::Seleccione::</option>
															<option value="160101">Iquitos</option>
															<option value="160108">Punchana</option>
															<option value="160112">Belén</option>
															<option value="160113">San Juan Bautista</option>
														</select>
													</div>
												</div>
											</div>
											<div class="col-md-8">
												<div class="form-group">
													<label for="desDireccionCliente"
														class="col-lg-6 control-label">DIRECIÓN:</label>
													<div class="col-md-11">
														<input type="text" name="desDireccionCliente"
															class="form-control" id="desDireccionCliente" value=""
															placeholder="Dirección">
													</div>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-3">
												<div class="form-group">
													<label for="discount"
														class="col-lg-8 control-label">DESCUENTO:</label>
													<div class="col-lg-12">
														<input type="number" name="discount" class="form-control"
															required value="<?= $dsctotal ?>" id="discount"
															placeholder="Descuento" step="any">
													</div>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label for="money"
														class="col-lg-6 control-label">EFECTIVO:</label>
													<div class="col-lg-10">
														<input type="number" name="money" required class="form-control"
															id="money" placeholder="Efectivo" step="any"
															value="<?php echo $total; ?>">
													</div>
												</div>
											</div>
										</div>
										<input type="hidden" name="tipDocUsuario" value="1">
										<input type="hidden" name="codUnidadMedida" value="NIU">
										<input type="hidden" name="codProducto" value="0">
										<input type="hidden" name="codProductoSUNAT" value="-">
										<input type="hidden" name="sumTotTributosItem" value="0">
										<input type="hidden" name="codTriIGV" value="9997">
										<input type="hidden" name="mtoIgvItem" value="0">
										<input type="hidden" name="nomTributoIgvItem" value="EXO">
										<input type="hidden" name="codTipTributoIgvItem" value="VAT">
										<input type="hidden" name="codCatTributoIgvItem" value="E">
										<input type="hidden" name="tipAfeIGV" value="20">
										<input type="hidden" name="mtoIscItem" value="0">
										<input type="hidden" name="tipSisISC" value="">
										<input type="hidden" name="porIgvItem" value="0">
										<input type="hidden" name="codTriISC" value="-">
										<input type="hidden" name="mtoIscItem" value="">
										<input type="hidden" name="nomTributoIscItem" value="">
										<input type="hidden" name="codTipTributoIscItem" value="-">
										<input type="hidden" name="codCatTributoIscItem" value="-">
										<input type="hidden" name="tipSisISC" value="">
										<input type="hidden" name="porIscItem" value="">
										<input type="hidden" name="mtoValorReferencialUnitario" value="0">
										<input type="hidden" name="codTipDescuentoItem" value="-">
										<input type="hidden" name="porDescuentoItem" value="0">
										<input type="hidden" name="mtoDescuentoItem" value="0">
										<input type="hidden" name="mtoBasImpDescuentoItem" value="0">
										<input type="hidden" name="codTipCargoItem" value="-">
										<input type="hidden" name="porCargoItem" value="0">
										<input type="hidden" name="mtoCargoItem" value="0">
										<input type="hidden" name="mtoBasImpCargoItem" value="0">
										<input type="hidden" name="ideTributo" value="9997">
										<input type="hidden" name="nomTributo" value="EXO">
										<input type="hidden" name="codTipTributo" value="VAT">
										<input type="hidden" name="codCatTributo" value="E">
										<input type="hidden" name="mtoTributo" value="0">
										<div class="row">
											<!--- Carrito de compras :) -->
											<?php
											if (isset($_SESSION["cart"])):
												?>
												<div class="col-md-10">
													<hr>
													<table class="table table-bordered">
														<thead>
															<th style="width: 6px">Nº</th>
															<th style="width:20px;">CODIGO</th>
															<th style="width:20px;">CANTIDAD</th>
															<th>DESCRIPCIÓN</th>
															<th style="width:40px;">ANAQUEL</th>
															<th style="width:100px;">P. UNIT.</th>
															<!-- <th style="width:25px;">Descuento</th> -->
															<th style="width:70px;">TOTAL</th>
															<th></th>
														</thead>
														<?php
														$contador = 0;
														$dsctotal = 0;
														foreach ($_SESSION["cart"] as $p):
															$contador++;
															$product = ProductData::getById($p["product_id"]);
															if ($product->is_may == 1) {
																$precio = $product->price_may;
															}
															if ($product->is_may == 0) {
																$precio = $p["precio_unitario"];
															}
															?>
															<tr>
																<td style="background-color: #444; color: #FFF">
																	<?php echo $contador; ?>
																</td>
																<td><?php echo $product->barcode; ?></td>
																<td><?php echo round($p["q"], 3); ?></td>
																<td>
																	<?php echo $product->name; ?>
																	<?php
																	if ($product->description != "") {
																		echo "<b>(" . $product->description . ")</b>";
																	}
																	?>
																</td>
																<td><?= $product->anaquel ?></td>
																<td style="text-align: right;">
																	<b><?php echo number_format($p["precio_unitario"], 2, '.', ','); ?></b>
																</td>
																<!-- <td style="text-align: right;"><b><?php echo number_format($p["descuento"], 2, '.', ','); ?></b></td> -->
																<td><b><?php
																$pt = number_format($precio - $p["descuento"], 5) * round($p["q"], 3);
																$total += $pt;
																$dsctotal += $p["descuento"] * $p["q"];
																echo number_format($pt, 2);
																?>

																	</b></td>
																<td style="width:30px;"><a
																		href="index.php?view=clearcart&product_id=<?php echo $product->id; ?>"
																		class="btn btn-danger"><i class="fa fa-trash"></i></a>
																</td>
															</tr>
														<?php endforeach; ?>
													</table>
												</div>
												<?php
												//COMPRABAMOS QUE LOS DATOS SEAN VALIDOS
												$total = round($total, 2);
											else: ?>
												<div class="col-md-10"
													style="display: flex; justify-content: center; align-items: center;">
													<div class="icon-container">
														<i class="fa fa-inbox icon-inbox"></i>
													</div>
												</div>
											<?php endif; ?>
											<div class="col-md-10">
												<table class="table table-bordered">
													<tr>
														<td colspan="8"
															style="border: 2px dashed #fff; background-color: #888">
															<button type="button" class="btn-full" id="btnAgregarItem">
																<i class="fa fa-plus"></i>&nbsp;&nbsp;Agregar un
																item (<span><b>F1</b></span>)
															</button>
														</td>
													</tr>
													<?php if (isset($_SESSION["errors"])): ?>
														<tr>
															<td>
																<h2>Errores</h2>
																<p></p>
																<table class="table table-bordered table-hover">
																	<tr class="danger">
																		<th>ID</th>
																		<th>Codigo</th>
																		<th>Producto</th>
																		<th>Mensaje</th>
																	</tr>
																	<?php foreach ($_SESSION["errors"] as $error):
																		$product = ProductData::getById($error["product_id"]);

																		?>
																		<tr class="danger">
																			<td><?php echo $product->id; ?></td>
																			<td><?php echo $product->barcode; ?></td>
																			<td><?php echo $product->name; ?></td>
																			<td><b><?php echo $error["message"]; ?></b></td>
																		</tr>

																	<?php endforeach; ?>
																</table>
															</td>
														</tr>
														<?php
														unset($_SESSION["errors"]);
													endif; ?>
												</table>
											</div>
										</div>
										<div class="row">
											<div class="col-md-10">
												<table class="table table-bordered">
													<tr>
														<td>&nbsp;</td>
														<td style="text-align: right;" colspan="6">
															<div class="checkbox">
																<label>
																	<b>IMPORTE TOTAL:</b>&nbsp;&nbsp;&nbsp;
																	<b><?php echo number_format($total, 2, '.', ','); ?></b>
																	<input type="hidden" name="total"
																		value="<?php echo $total; ?>">
																</label>
															</div>
														</td>
														<td>&nbsp;</td>
													</tr>
													<tr>
														<td colspan="8" style="text-align: right;">
															<div class="checkbox">
																<label>
																	<input name="is_oficial" type="hidden" value="1">
																	<a href="index.php?view=clearcart"
																		class="btn btn-lg btn-danger"><i
																			class="glyphicon glyphicon-remove"></i>
																		Cancelar</a>
																	<button class="btn btn-lg btn-primary">Emitir
																		Boleta (<span><b>F3</b></span>)</button>
																</label>
															</div>
														</td>
													</tr>
												</table>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
						<div id="comprobante_factura" style="display: none">
							<form action="?view=addfactura" id="formfactura" class="form-horizontal"
								method="post" onsubmit="enviado2(1,event)">
								<div class="row">
									<div class="col-md-12">
										<?php $total = 0; ?>
										<input type="hidden" name="RUC" value="<?php echo $empresa->Emp_Ruc; ?>">
										<input type="hidden" name="TIPO" value="01">
										<input type="hidden" name="tipOperacion" value="0101">
										<input type="hidden" name="fecVencimiento" value="-">
										<input type="hidden" name="codLocalEmisor" value="0000">
										<input type="hidden" name="tipMoneda" value="PEN">
										<input type="hidden" name="porDescGlobal" value="-">
										<input type="hidden" name="mtoDescGlobal" value="0">
										<input type="hidden" name="mtoBasImpDescGlobal" value="0">
										<input type="hidden" name="sumTotTributos" value="0">
										<input type="hidden" name="sumDescTotal" value="<?= $dsctotal ?>">
										<input type="hidden" name="sumOtrosCargos" value="0">
										<input type="hidden" name="sumTotalAnticipos" value="0">
										<input type="hidden" name="ublVersionId" value="2.1">
										<input type="hidden" name="customizationId" value="2.0">

										<div class="row">
											<div class="col-md-3">
												<div class="form-group">
													<label for="fecEmision" class="col-lg-8 control-label">FECHA
														EMISIÓN:</label>
													<div class="col-md-12">
														<input type="date" name="fecEmision" id="fecEmision"
															class="form-control" value="<?php echo date("Y-m-d"); ?>">
													</div>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label for="horEmision"
														class="col-lg-8 control-label">HORA:</label>
													<div class="col-md-12">
														<input type="text" name="horEmision" id="horEmision"
															class="form-control" value="<?php echo date('H:i:s'); ?>">
													</div>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<!-- <label for="selEstado"
														class="col-lg-8 control-label">COMPROBANTE:</label>
													<div class="col-md-10">
														<select name="selEstado" class="form-control">
															<option value="1">Electrónico</option>
														</select>
													</div> -->
													<label for="formaPago"
														class="col-lg-8 control-label text-sm">FORMA DE PAGO:</label>
													<div class="col-md-12">
														<select name="formaPago" class="form-control text-sm">
															<option value="1" selected>Contado</option>
															<option value="2">Crédito</option>
														</select>
													</div>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-3">
												<div class="form-group">
													<label for="SERIE"
														class="col-lg-8 control-label">SERIE:</label>
													<div class="col-md-12">
														<input type="text" name="SERIE" id="SERIE" class="form-control"
															value="<?php echo $SERIE_F; ?>">
													</div>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label for="COMPROBANTE"
														class="col-lg-8 control-label">CORRELATIVO:</label>
													<div class="col-md-12">
														<input type="text" name="COMPROBANTE" id="COMPROBANTE"
															class="form-control" value="<?php echo $COMPROBANTE_F; ?>">
													</div>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label for="selTipoPago" class="col-lg-6 control-label text-sm">
														TIPO DE PAGO:
													</label>
													<div class="col-md-12">
														<select name="selTipoPago" class="form-control text-sm">
															<option value="1" selected>Efectivo</option>
															<option value="2">Plin</option>
															<option value="3">Yape</option>
															<option value="4">Tarjeta débito</option>
															<option value="5">Tarjeta crédito</option>
														</select>
													</div>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-3">
												<div class="form-group">
													<label for="numDocUsuario" class="col-lg-8 control-label">RUC:</label>
													<div class="col-md-12">
														<input type="number" name="numDocUsuario" class="form-control"
															id="ruc" placeholder="Nº de RUC" onblur="validar_ruc()" required="required"> 
													</div>
												</div>
											</div>
											<div class="col-md-8">
												<div class="form-group">
													<label for="rznSocialUsuario" class="col-lg-6 control-label">RAZÓN
														SOCIAL:</label>
													<div class="col-md-11">
														<input type="text" name="rznSocialUsuario" class="form-control"
															id="rznSocialUsuario" value="" placeholder="DATOS CLIENTE"
															required="required">
													</div>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-3">
												<div class="form-group">
													<label for="inputEmail1"
														class="col-lg-8 control-label">DISTRITO</label>
													<div class="col-md-12">
														<select name="codUbigeoCliente" id="codUbigeoCliente"
															class="form-control">
															<option value="">::Seleccione::</option>
															<option value="160101">Iquitos</option>
															<option value="160108">Punchana</option>
															<option value="160112">Belén</option>
															<option value="160113">San Juan Bautista</option>
														</select>
													</div>
												</div>
											</div>
											<div class="col-md-8">
												<div class="form-group">
													<label for="inputEmail1"
														class="col-lg-6 control-label">DIRECCIÓN:</label>
													<div class="col-md-11">
														<input type="text" name="desDireccionCliente"
															class="form-control" id="desDireccionCliente" value=""
															placeholder="Dirección">
													</div>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-3">
												<div class="form-group">
													<label for="inputEmail1"
														class="col-lg-8 control-label">DESCUENTO</label>
													<div class="col-lg-12">
														<input type="text" name="discount" class="form-control" required
															value="<?= $dsctotal ?>" id="discount2"
															placeholder="Descuento">
													</div>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label for="inputEmail1"
														class="col-lg-8 control-label">EFECTIVO</label>
													<div class="col-lg-10">
														<input type="text" name="money" required class="form-control"
															id="money2" placeholder="Efectivo"
															value="<?php echo $total; ?>">
													</div>
												</div>
											</div>
										</div>
										<input type="hidden" name="tipDocUsuario" value="6">
										<input type="hidden" name="codUnidadMedida" value="NIU">
										<input type="hidden" name="codProducto" value="0">
										<input type="hidden" name="codProductoSUNAT" value="-">
										<input type="hidden" name="sumTotTributosItem" value="0">
										<input type="hidden" name="codTriIGV" value="9997">
										<input type="hidden" name="mtoIgvItem" value="0">
										<input type="hidden" name="nomTributoIgvItem" value="EXO">
										<input type="hidden" name="codTipTributoIgvItem" value="VAT">
										<input type="hidden" name="codCatTributoIgvItem" value="E">
										<input type="hidden" name="tipAfeIGV" value="20">
										<input type="hidden" name="mtoIscItem" value="0">
										<input type="hidden" name="tipSisISC" value="">
										<input type="hidden" name="porIgvItem" value="0">
										<input type="hidden" name="codTriISC" value="-">
										<input type="hidden" name="mtoIscItem" value="">
										<input type="hidden" name="nomTributoIscItem" value="">
										<input type="hidden" name="codTipTributoIscItem" value="-">
										<input type="hidden" name="codCatTributoIscItem" value="-">
										<input type="hidden" name="porIscItem" value="">
										<input type="hidden" name="mtoValorReferencialUnitario" value="0">
										<input type="hidden" name="codTipDescuentoItem" value="-">
										<input type="hidden" name="porDescuentoItem" value="0">
										<input type="hidden" name="mtoDescuentoItem" value="0">
										<input type="hidden" name="mtoBasImpDescuentoItem" value="0">
										<input type="hidden" name="codTipCargoItem" value="-">
										<input type="hidden" name="porCargoItem" value="0">
										<input type="hidden" name="mtoCargoItem" value="0">
										<input type="hidden" name="mtoBasImpCargoItem" value="0">
										<input type="hidden" name="ideTributo" value="9997">
										<input type="hidden" name="nomTributo" value="EXO">
										<input type="hidden" name="codTipTributo" value="VAT">
										<input type="hidden" name="codCatTributo" value="E">
										<input type="hidden" name="mtoTributo" value="0">
										<div class="row">
											<!--- Carrito de compras :) -->
											<?php
											if (isset($_SESSION["cart"])):
												?>
												<div class="col-md-10">
													<hr>
													<table class="table table-bordered">
														<thead>
															<th style="width: 6px">Nº</th>
															<th style="width:20px;">CODIGO</th>
															<th style="width:20px;">CANTIDAD</th>
															<th>DESCRIPCIÓN</th>
															<th style="width:40px;">ANAQUEL</th>
															<th style="width:100px;">P. UNIT.</th>
															<!-- <th style="width:25px;">Descuento</th> -->
															<th style="width:70px;">TOTAL</th>
															<th></th>
														</thead>
														<?php
														$contador = 0;
														$dsctotal = 0;
														foreach ($_SESSION["cart"] as $p):
															$contador++;
															$product = ProductData::getById($p["product_id"]);
															if ($product->is_may == 1) {
																$precio = $product->price_may;
															}
															if ($product->is_may == 0) {
																$precio = $p["precio_unitario"];
															}
															?>
															<tr>
																<td style="background-color: #444; color: #FFF">
																	<?php echo $contador; ?>
																</td>
																<td><?php echo $product->barcode; ?></td>
																<td><?php echo round($p["q"], 3); ?></td>
																<td>
																	<?php echo $product->name; ?>
																	<?php
																	if ($product->description != "") {
																		echo "<b>(" . $product->description . ")</b>";
																	}
																	?>
																</td>
																<td><?= $product->anaquel ?></td>
																<td style="text-align: right;">
																	<b><?php echo number_format($p["precio_unitario"], 2, '.', ','); ?></b>
																</td>
																<!-- <td style="text-align: right;"><b><?php echo number_format($p["descuento"], 2, '.', ','); ?></b></td> -->
																<td><b><?php
																$pt = number_format($precio - $p["descuento"], 5) * round($p["q"], 3);
																$total += $pt;
																$dsctotal += $p["descuento"] * $p["q"];
																echo number_format($pt, 2);
																?>

																	</b></td>
																<td style="width:30px;"><a
																		href="index.php?view=clearcart&product_id=<?php echo $product->id; ?>"
																		class="btn btn-danger"><i class="fa fa-trash"></i></a>
																</td>
															</tr>
														<?php endforeach; ?>
													</table>
												</div>
												<?php
												//COMPRABAMOS QUE LOS DATOS SEAN VALIDOS
												$total = round($total, 2);
											else: ?>
												<div class="col-md-10"
													style="display: flex; justify-content: center; align-items: center;">
													<div class="icon-container">
														<i class="fa fa-inbox icon-inbox"></i>
													</div>
												</div>
											<?php endif; ?>
											<div class="col-md-10">
												<table class="table table-bordered">
													<tr>
														<td colspan="8"
															style="border: 2px dashed #fff; background-color: #888">
															<button type="button" class="btn-full" id="btnAgregarItem2">
																<i class="fa fa-plus"></i>&nbsp;&nbsp;Agregar un item (<span><b>F1</b></span>)
															</button>
														</td>
													</tr>
													<?php if (isset($_SESSION["errors"])): ?>
														<tr>
															<td>
																<h2>Errores</h2>
																<p></p>
																<table class="table table-bordered table-hover">
																	<tr class="danger">
																		<th>ID</th>
																		<th>Codigo</th>
																		<th>Producto</th>
																		<th>Mensaje</th>
																	</tr>
																	<?php foreach ($_SESSION["errors"] as $error):
																		$product = ProductData::getById($error["product_id"]);

																		?>
																		<tr class="danger">
																			<td><?php echo $product->id; ?></td>
																			<td><?php echo $product->barcode; ?></td>
																			<td><?php echo $product->name; ?></td>
																			<td><b><?php echo $error["message"]; ?></b></td>
																		</tr>

																	<?php endforeach; ?>
																</table>
															</td>
														</tr>
														<?php
														unset($_SESSION["errors"]);
													endif; ?>
												</table>
											</div>
										</div>
										<div class="row">
											<div class="col-md-10">
												<table class="table table-bordered">
													<tr>
														<td>&nbsp;</td>
														<td style="text-align: right;" colspan="6">
															<div class="checkbox">
																<label>
																	<b>IMPORTE TOTAL:</b>&nbsp;&nbsp;&nbsp;
																	<b><?php echo number_format($total, 2, '.', ','); ?></b>
																	<input type="hidden" name="total"
																		value="<?php echo $total; ?>">
																</label>
															</div>
														</td>
														<td>&nbsp;</td>
													</tr>
													<tr>
														<td colspan="8" style="text-align: right;">
															<div class="checkbox">
																<label>
																	<input name="is_oficial" type="hidden" value="1">
																	<a href="index.php?view=clearcart"
																		class="btn btn-lg btn-danger"><i
																			class="glyphicon glyphicon-remove"></i>
																		Cancelar</a>
																	<button class="btn btn-lg btn-primary">Emitir
																		Factura (<span><b>F3</b></span>)</button>
																</label>
															</div>
														</td>
													</tr>
												</table>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
						<div id="comprobante_orden" style="display: none;">
							<form action="?view=processsell" id="formnotaventa" class="form-horizontal" method="post"
								onsubmit="return enviado2(0)">
								<input type="hidden" name="TIPO" value="70">
								<?php $total = 0; ?>
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label for="inputEmail1" class="col-lg-8 control-label">FECHA</label>
											<div class="col-md-12">
												<input type="date" name="fecEmision" id="fecEmision"
													class="form-control" value="<?php echo date("Y-m-d"); ?>">
											</div>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="inputEmail1" class="col-lg-8 control-label">HORA</label>
											<div class="col-md-12">
												<input type="text" name="horEmision" id="horEmision"
													class="form-control" value="<?php echo date('H:i:s'); ?>">
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label for="inputEmail1" class="col-lg-8 control-label">SERIE:</label>
											<div class="col-md-12">
												<input type="text" name="SERIE" id="SERIE" class="form-control"
													value="0002">
											</div>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="inputEmail1" class="col-lg-8 control-label">NÚMERO
												ORDEN:</label>
											<div class="col-md-12">
												<input type="text" name="COMPROBANTE" id="COMPROBANTE"
													class="form-control" value="<?php echo $ORDEN; ?>">
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-7">
										<div class="form-group">
											<label for="inputEmail1" class="col-lg-6 control-label">CLIENTE</label>
											<div class="col-lg-12">
												<?php
												$clients = PersonData::getClients();
												?>
												<select name="client_id" class="form-control select2bs4"
													data-live-search="true" style="width: 100%;">
													<?php foreach ($clients as $client): ?>
														<option value="<?php echo $client->id; ?>">
															<?php echo $client->name . " " . $client->lastname; ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-7">
										<div class="form-group">
											<label for="inputEmail1" class="col-lg-6 control-label">EFECTIVO</label>
											<div class="col-lg-5">
												<input class="form-control" type="text" id="money3" name="money3"
													placeholder="Efectivo" required>
												<input class="form-control" type="hidden" id="discount3" name="discount3"
													value="0" placeholder="Descuento">
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<!--- Carrito de compras :) -->
									<?php
									if (isset($_SESSION["cart"])):
										?>
										<div class="col-md-10">
											<hr>
											<table class="table table-bordered">
												<thead>
													<th style="width: 6px">Nº</th>
													<!-- <th style="width:20px;">CODIGO</th> -->
													<th style="width:20px;">CANTIDAD</th>
													<th>DESCRIPCIÓN</th>
													<th style="width:40px;">ANAQUEL</th>
													<th style="width:100px;">P. UNIT.</th>
													<!-- <th style="width:25px;">Descuento</th> -->
													<th style="width:70px;">TOTAL</th>
													<th></th>
												</thead>
												<?php
												$contador = 0;
												$dsctotal = 0;
												foreach ($_SESSION["cart"] as $p):
													$contador++;
													$product = ProductData::getById($p["product_id"]);
													if ($product->is_may == 1) {
														$precio = $product->price_may;
													}
													if ($product->is_may == 0) {
														$precio = $p["precio_unitario"];
													}
													?>
													<tr>
														<td style="background-color: #444; color: #FFF">
															<?php echo $contador; ?>
														</td>
														<!-- <td><?php echo $product->barcode; ?></td> -->
														<td><?php echo round($p["q"], 3); ?></td>
														<td>
															<?php echo $product->name; ?>
															<?php
															if ($product->description != "") {
																echo "<b>(" . $product->description . ")</b>";
															}
															?>
														</td>
														<td><?= $product->anaquel ?></td>
														<td style="text-align: right;">
															<b><?php echo number_format($p["precio_unitario"], 2, '.', ','); ?></b>
														</td>
														<!-- <td style="text-align: right;"><b><?php echo number_format($p["descuento"], 2, '.', ','); ?></b></td> -->
														<td><b><?php
														$pt = number_format($precio - $p["descuento"], 5) * round($p["q"], 3);
														$total += $pt;
														$dsctotal += $p["descuento"] * $p["q"];
														echo number_format($pt, 2);
														?>

															</b></td>
														<td style="width:30px;"><a
																href="index.php?view=clearcart&product_id=<?php echo $product->id; ?>"
																class="btn btn-danger"><i class="fa fa-trash"></i></a>
														</td>
													</tr>
												<?php endforeach; ?>
											</table>
										</div>
										<?php
										//COMPRABAMOS QUE LOS DATOS SEAN VALIDOS
										$total = round($total, 2);
									else: ?>
										<div class="col-md-10"
											style="display: flex; justify-content: center; align-items: center;">
											<div class="icon-container">
												<i class="fa fa-inbox icon-inbox"></i>
											</div>
										</div>
									<?php endif; ?>
									<div class="col-md-10">
										<table class="table table-bordered">
											<tr>
												<td colspan="8" style="border: 2px dashed #fff; background-color: #888">
													<button type="button" class="btn-full" id="btnAgregarItem3">
														<i class="fa fa-plus"></i>&nbsp;&nbsp;Agregar un item (<span><b>F1</b></span>)
													</button>
												</td>
											</tr>
											<?php if (isset($_SESSION["errors"])): ?>
												<tr>
													<td>
														<h2>Errores</h2>
														<p></p>
														<table class="table table-bordered table-hover">
															<tr class="danger">
																<th>ID</th>
																<th>Codigo</th>
																<th>Producto</th>
																<th>Mensaje</th>
															</tr>
															<?php foreach ($_SESSION["errors"] as $error):
																$product = ProductData::getById($error["product_id"]);

																?>
																<tr class="danger">
																	<td><?php echo $product->id; ?></td>
																	<td><?php echo $product->barcode; ?></td>
																	<td><?php echo $product->name; ?></td>
																	<td><b><?php echo $error["message"]; ?></b></td>
																</tr>

															<?php endforeach; ?>
														</table>
													</td>
												</tr>
												<?php
												unset($_SESSION["errors"]);
											endif; ?>
										</table>
										<input class="form-control" type="hidden" id="total" name="total"
											value="<?php echo $total; ?>" placeholder="Total">
									</div>
								</div>

								<div class="row">
									<div class="col-md-10">
										<table class="table table-bordered">
											<tr>
												<td style="text-align: right;" colspan="7">
													<p><b>Total: </b></p>
												</td>
												<td style="text-align: right;">
													<p><b><?php echo number_format($total, 2); ?></b></p>
												</td>
											</tr>
											<tr>
												<td colspan="8" style="text-align: right;">
													<label>
														<input name="is_oficial" type="hidden" value="1">
														<a href="index.php?view=clearcart"
															class="btn btn-lg btn-danger">
															<i class="glyphicon glyphicon-remove"></i> Cancelar
														</a>
														<button class="btn btn-lg btn-primary">
															<i class="glyphicon glyphicon-ok"></i>
															<i class="glyphicon glyphicon-ok"></i> Finalizar Venta (<span><b>F3</b></span>)
														</button>
													</label>
												</td>
											</tr>
										</table>
									</div>
								</div>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
</section>
</div>
</div>