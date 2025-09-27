<?php
	$sd = (isset($_GET["sd"])) ? $_GET["sd"] : date('d/m/Y');
	$ed = (isset($_GET["ed"])) ? $_GET["ed"] : date('d/m/Y');
	$listaSituacion = [
		"ListaSituacion" => [
			["id" => "01", "nombre" => "Por Generar XML"],
			["id" => "02", "nombre" => "XML Generado"],
			["id" => "03", "nombre" => "Enviado y Aceptado SUNAT"],
			["id" => "04", "nombre" => "Enviado y Aceptado SUNAT con Obs."],
			["id" => "05", "nombre" => "Rechazado por SUNAT"],
			["id" => "06", "nombre" => "Con Errores"],
			["id" => "07", "nombre" => "Por Validar XML"],
			["id" => "08", "nombre" => "Enviado a SUNAT Por Procesar"],
			["id" => "09", "nombre" => "Enviado a SUNAT Procesando"],
			["id" => "10", "nombre" => "Rechazado por SUNAT"],
			["id" => "11", "nombre" => "Enviado y Aceptado SUNAT"],
			["id" => "12", "nombre" => "Enviado y Aceptado SUNAT con Obs."]
		]
	];

	$dbPath = '../efact1.3.4/bd/BDFacturador.db';	
	$rutaXML = '../efact1.3.4/sunat_archivos/sfs/FIRMA';
	$rutaCDR = '../efact1.3.4/sunat_archivos/sfs/RPTA';
?>
<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><i class='fa fa-cart-plus'></i> Registro de venta </h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Ventas</a></li>
					<li class="breadcrumb-item active">Registro Venta</li>
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
				<form>
					<input type="hidden" name="view" value="sells">
					<div class="row">
						<div class="col-md-3">
							<div class="input-group date" id="fechaini" data-target-input="nearest">
								<input type="text" name="sd" value="<?=$sd?>" class="form-control datetimepicker-input" data-target="#fechaini"/>
								<div class="input-group-append" data-target="#fechaini" data-toggle="datetimepicker">
									<div class="input-group-text"><i class="fa fa-calendar"></i></div>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="input-group date" id="fechafin" data-target-input="nearest">
								<input type="text" name="ed" value="<?=$ed?>" class="form-control datetimepicker-input" data-target="#fechafin"/>
								<div class="input-group-append" data-target="#fechafin" data-toggle="datetimepicker">
									<div class="input-group-text"><i class="fa fa-calendar"></i></div>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<?php
							$users = UserData::getAll();
							?>
							<select name="user_id" class="form-control">
								<option value="0">SELECCIONAR</option>
								<?php foreach ($users as $user): ?>
									<option value="<?php echo $user->id; ?>"><?php echo $user->username; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-md-3">
							<input type="submit" class="btn btn-success btn-block" value="Procesar">
						</div>

					</div>
				</form>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<?php
				$tv = 0;
				$tc = 0;
				$fechaSd = '';
				$fechaEd = '';
				$user_id = isset($_GET["user_id"]) ? $_GET["user_id"] : $_SESSION["user_id"];

				$admin = UserData::getById($_SESSION["user_id"])->is_admin;
				if (isset($_GET["sd"]) && isset($_GET["ed"])):					
					$fechaini = DateTime::createFromFormat('d/m/Y', $_GET['sd']);
					$fechafin = DateTime::createFromFormat('d/m/Y', $_GET['ed']);
					$fechaSd = $fechaini->format('Y-m-d');
					$fechaEd = $fechafin->format('Y-m-d');
					$products = SellData::getSells($fechaSd, $fechaEd,$_GET["user_id"]);
				else:
					// Crear un objeto DateTime con la fecha y hora actual
					$fecha = new DateTime();
					// Formatear la fecha en 'Y-m-d'
					$fecha_actual = $fecha->format('Y-m-d');
					$products = SellData::getSells($fecha_actual, $fecha_actual,$_SESSION["user_id"]);
					$fechaSd = $fecha_actual;
					$fechaEd = $fecha_actual;
				endif;

				if (count($products) > 0) {
					?>
					<table class="table table-bordered table-hover datatable" id="sells">
						<thead class="thead-dark">
							<th>Ver</th>
							<th>Comprobante</th>
							<th>Cliente</th>
							<th>Importe </th>
							<th>Medio Pago</th>
							<th>Fecha Emision</th>
							<th>Fecha Envio</th>
							<th>Estado Envio</th>
							<th>Descargar</th>
							<th>Usuario</th>
						</thead>
						<tbody>
							<?php foreach ($products as $sell):
									$notacomprobar = $sell->serie . "-" . $sell->comprobante; 
									$probar = Not_1_2Data::getByIdComprobado($notacomprobar);

									switch ($sell->tipo_pago){
										case 1:
											$medioPago = "EFECTIVO";
											break;
										case 2:
											$medioPago = "PLIN";
											break;
										case 3:
											$medioPago = "YAPE";
											break;
										case 4:
											$medioPago = "TARJETA DEBITO";
											break;
										case 5:
											$medioPago = "TARJETA CREDITO";
											break;	
										default:
											$medioPago = "OTRO MEDIO DE PAGO";
											break;				
									}

									try {
										$db = new SQLite3($dbPath);
										$query = "SELECT * FROM DOCUMENTO WHERE NUM_DOCU = '" . $sell->serie . "-" . $sell->comprobante . "'";
										$results = $db->query($query);

										// Verificar si la consulta devolvió resultados
										if ($results === false) {
											die("Error en la consulta SQL: " . $db->lastErrorMsg());
										}

										$documento = $results->fetchArray(SQLITE3_ASSOC);

										// Si no hay resultados, asignar valores por defecto
										if ($documento === false) {
											$documento = [
												'FEC_GENE' => null,
												'FEC_ENVI' => null,
												'FEC_CARG' => null,
												'TIP_DOCU' => null,
												'NUM_DOCU' => null,
												'NUM_RUC' => null,
												'NOM_ARCH' => null,
												'TIP_ARCH' => null,
												'DES_OBSE' => null,
												'FIRM_DIGITAL' => null,
												'IND_SITU' => null
											];
										}

										// Asignar valores con operador ternario y validación
										$estado = "";
										$fechaGeneracion = $documento['FEC_GENE'] ?? '-';
										$fechaEnvio = $documento['FEC_ENVI'] ?? '-';
										$fechaCarga = $documento['FEC_CARG'] ?? '-';
										$tipoComprobante = $documento['TIP_DOCU'] ?? '-';
										$numeroComprobante = $documento['NUM_DOCU'] ?? '-';
										$numeroRuc = $documento['NUM_RUC'] ?? '-';
										$nombreArchivo = $documento['NOM_ARCH'] ?? '-';
										$tipoArchivo = $documento['TIP_ARCH'] ?? '-';
										$observaciones = $documento['DES_OBSE'] ?? '-';
										$firmadoDigital = $documento['FIRM_DIGITAL'] ?? '-';
										$estadoSituacion = $documento['IND_SITU'] ?? '-';

										// Buscar situación (con validación)
										$situacion = array_filter($listaSituacion['ListaSituacion'], function($item) use ($estadoSituacion) {
											return $item['id'] == $estadoSituacion;
										});

										// Obtener nombre de situación (si existe)
										$nombreSituacion = !empty($situacion) ? current($situacion)['nombre'] : 'Ejecutar Facturador sunat';
										$estado = (isset($probar->TIPO_DOC) && $probar->TIPO_DOC ==7) ? "N.CRE: ".$probar->SERIE."-".$probar->COMPROBANTE :$nombreSituacion;
								
										$descargarXML = false;
										$descargarCDR = false;

										if($estadoSituacion == "02" || $estadoSituacion == "07" || $estadoSituacion == "08" || $estadoSituacion == "09") {
											$descargarXML = true;
										}elseif($estadoSituacion == "03" || $estadoSituacion == "04" || $estadoSituacion == "05" || $estadoSituacion == "10" || $estadoSituacion == "11" || $estadoSituacion == "12") {
											$descargarXML = true;
											$descargarCDR = true;
										}
										
									} catch (Exception $e) {
										die("Error al conectar o consultar la base de datos: " . $e->getMessage());
									} 

									$fechaObj = new DateTime($sell->created_at);
									$fechaFormateada = $fechaObj->format('d/m/Y H:i:s');
								?>
								<tr style="background: <?php if (isset($probar)) {
												if ($probar->TIPO_DOC==8) {	echo "#C2FCCF"; }
												if ($probar->TIPO_DOC==7) {	echo "#FFC4C4"; }
											} ?>">
									<td style="width:60px;">
										<a href="?view=onesell&id=<?= $sell->id ?>&tipodoc=<?= $sell->tipo_comprobante ?>"
											class="btn btn-xs btn-default">
											<i class="fas fa-eye"></i>
										</a>
										<?php
										if(isset($probar->TIPO_DOC) && $probar->TIPO_DOC ==7)
										{
											?>
											<a href="./?view=notacreditoboletat&num=<?=$probar->SERIE."-".$probar->COMPROBANTE ?>" class="btn btn-xs btn-danger" title="Ver Nota de Credito">
												<i class="fas fa-file-invoice"></i>
											</a>
											<?php
										}
										?>
										<?php
										$usuario = UserData::getById($sell->user_id);
										$cliente = PersonData::getById($sell->person_id);
										$objOper = OperationData::getAllProductsBySellId($sell->id);
										$tc = 0;

										foreach ($objOper as $oper) {
											$objProd = ProductData::getById($oper->product_id);
											$tc += (isset($probar->TIPO_DOC) && $probar->TIPO_DOC ==7)  ? 0 : $oper->q * $objProd->price_in;
										}
										?>
									</td>
									<td>
										<?= $sell->serie . "-" . $sell->comprobante ?>
									</td>
									<td><?= $cliente->name . " " . $cliente->lastname ?></td>
									<td>

										<?php
										$total = (isset($probar->TIPO_DOC) && $probar->TIPO_DOC ==7)  ? 0 : $sell->total;
										echo "<b>" . number_format($total, 2) . "</b>";
										$tv += (isset($probar->TIPO_DOC) && $probar->TIPO_DOC ==7)  ? 0 : $total;

										?>

									</td>
									<td><?=$medioPago?></td>
									<td><?=$fechaFormateada?></td>
									<!-- <td><?//=$fechaGeneracion?></td> -->
									<td><?=$fechaEnvio?></td>
									<td><?=$estado?></td>
									<td>
										<?php
										if ($descargarXML) {
											$comprobanteXML = $nombreArchivo . ".xml";
											$comprobanteCDR = "R" . $nombreArchivo . ".zip";
											?>
											<a href="<?= $rutaXML ?>/<?= $comprobanteXML ?>" class="btn btn-xs btn-default" download="<?= $comprobanteXML ?>">
												<i class="fas fa-download"></i> XML
											</a>
											<?php
											if ($descargarCDR) {
												?>

											<a href="<?= $rutaCDR ?>/<?= $comprobanteCDR ?>" class="btn btn-xs btn-default" target="_blank">
												<i class="fas fa-download"></i> CDR
											</a>
										<?php 
											}
										} ?>
									</td>
									<td style="width:30px;">
										<?= $usuario->username ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<div style="display: flex; justify-content: center;">
						<table class="table table-bordered table-hover col-md-4">
							<thead class="thead-dark">
								<tr>
									<th colspan="4" style="text-align: center;">Medios de pago</th>
									<?php
										$yape = json_decode(json_encode(SellData::getVentasOtroTipoPago('X',3,$fechaSd, $fechaEd,$user_id)),true)['total'];
										$plin = json_decode(json_encode(SellData::getVentasOtroTipoPago('X',2,$fechaSd, $fechaEd,$user_id)),true)['total'];								
										$tcredito = json_decode(json_encode(SellData::getVentasOtroTipoPago('X',5,$fechaSd, $fechaEd,$user_id)),true)['total'];								
										$tdebito = json_decode(json_encode(SellData::getVentasOtroTipoPago('X',4,$fechaSd, $fechaEd,$user_id)),true)['total'];
									?>
								</tr>
							</thead>
							<tr>
								<th style="text-align: right;">Total Yape </th>
								<th style="text-align: right;">
									<?php
										if ($yape > 0): 
											echo number_format($yape, 2, '.', ',');
										else:
											echo "0.00";
										endif;		
									?>
								</th>
								<th style="text-align: right;">Total efectivo</th>
								<th style="text-align: right;"><?= number_format(($tv -($yape+$plin+$tcredito+$tdebito)), 2, '.', ',') ?></th>
							</tr>
							<tr>
								<th style="text-align: right;">Total Plin </th>
								<th style="text-align: right;">
									<?php
										if ($plin > 0): 
											echo number_format($plin, 2, '.', ',');
										else:
											echo "0.00";
										endif;	
									?>
								</th>
								<th rowspan="3">&nbsp;</th>
								<th rowspan="3">&nbsp;</th>
							</tr>
							<tr>
								<th style="text-align: right;">Total T.Credito </th>
								<th style="text-align: right;">
									<?php
										if ($tcredito > 0): 
											echo number_format($tcredito, 2, '.', ',');
										else:
											echo "0.00";
										endif;	
									?>
								</th>
							</tr>
							<tr>
								<th style="text-align: right;">Total T.Debito </th>
								<th style="text-align: right;">
									<?php 
										if ($tdebito > 0): 
											echo number_format($tdebito, 2, '.', ',');
										else:
											echo "0.00";
										endif;
									?>
								</th>
							</tr>
							<?php if ($admin == 1) { ?>
								<!-- <tr>
									<th colspan="3" style="text-align: right;">Total de capital :</th>
									<th><?= number_format($tc,2,'.',',') ?></th>
								</tr>
								<tr>
									<th colspan="3" style="text-align: right;">Total de Ganancia :</th>
									<th><?= number_format(($tv - $tc),2,'.',',') ?></th>
								</tr> -->
							<?php }
							?>
							<tr class="bg-warning">
								<th style="text-align: right;">Sub Total</th>
								<th style="text-align: right;"><?= number_format(($yape + $plin + $tcredito + $tdebito), 2, '.', ',') ?></th>
								<th style="text-align: right;">Sub Total</th>
								<th style="text-align: right;"><?= number_format(($tv -($yape+$plin+$tcredito+$tdebito)), 2, '.', ',') ?></th>
							</tr>						
							<tr class="bg-success">
								<th colspan="3" style="text-align: right;">Total de ventas </th>
								<th style="text-align: right;"><?= number_format($tv, 2, '.', ',') ?></th>
							</tr>
						</table>
					</div>

					<div class="clearfix"></div>

					<?php
				} else {
					?>
					<div class="jumbotron text-center">
						<h2>No hay ventas</h2>
						<p>No se ha realizado ninguna venta.</p>
					</div>
					<?php
				}

				?>
			</div>
		</div>
	</div>
</section>