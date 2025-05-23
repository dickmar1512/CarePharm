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

				$admin = UserData::getById($_SESSION["user_id"])->is_admin;
				if (isset($_GET["sd"]) && isset($_GET["ed"])):					
					$fechaini = DateTime::createFromFormat('d/m/Y', $_GET['sd']);
					$fechafin = DateTime::createFromFormat('d/m/Y', $_GET['ed']);
					$fechaSd = $fechaini->format('Y-m-d');
					$fechaEd = $fechafin->format('Y-m-d');

					if ($_GET["user_id"] == 0):
						$products = SellData::getSellsXfecha($fechaSd, $fechaEd);
					else:
						$products = SellData::getSellsXfechaUsuario($fechaSd, $fechaEd, $_GET["user_id"]);
					endif;
				else:
					// Crear un objeto DateTime con la fecha y hora actual
					$fecha = new DateTime();
					// Formatear la fecha en 'Y-m-d'
					$fecha_actual = $fecha->format('Y-m-d');
					$products = SellData::getSellsXfecha($fecha_actual, $fecha_actual);
				endif;

				if (count($products) > 0) {
					?>
					<table class="table table-bordered table-hover datatable" id="sells">
						<thead class="thead-dark">
							<th>Ver</th>
							<th>Comprobante</th>
							<th>Cliente</th>
							<th>Importe </th>
							<th>Fecha Emision</th>
							<!-- <th>Fecha Gene.</th> -->
							<th>Fecha Envio</th>
							<th>Estado Envio</th>
							<th>Descargar</th>
							<th>Usuario</th>
						</thead>
						<?php foreach ($products as $sell): 

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
									$estado = $nombreSituacion;
								} catch (Exception $e) {
									die("Error al conectar o consultar la base de datos: " . $e->getMessage());
								}

								$fechaObj = new DateTime($sell->created_at);
								$fechaFormateada = $fechaObj->format('d/m/Y H:i:s');
							?>
							<tr>
								<td style="width:30px;">
									<a href="?view=onesell&id=<?= $sell->id ?>&tipodoc=<?= $sell->tipo_comprobante ?>"
										class="btn btn-xs btn-default">
										<i class="fas fa-eye"></i>
									</a>
									<?php
									$usuario = UserData::getById($sell->user_id);
									$cliente = PersonData::getById($sell->person_id);
									$objOper = OperationData::getAllProductsBySellId($sell->id);
									$tc = 0;

									foreach ($objOper as $oper) {
										$objProd = ProductData::getById($oper->product_id);
										$tc += $oper->q * $objProd->price_in;
									}
									?>
								</td>
								<td>
									<?= $sell->serie . "-" . $sell->comprobante ?>
								</td>
								<td><?= $cliente->name . " " . $cliente->lastname ?></td>
								<td>

									<?php
									$total = $sell->total;
									echo "<b>" . number_format($total, 2) . "</b>";
									$tv += $total;

									?>

								</td>
								<td><?=$fechaFormateada?></td>
								<!-- <td><?//=$fechaGeneracion?></td> -->
								<td><?=$fechaEnvio?></td>
								<td><?=$estado?></td>
								<td><?="aqui"?></td>
								<td style="width:30px;">
									<?= $usuario->username ?>
								</td>
							</tr>

						<?php endforeach; ?>
					</table>
					<table class="table table-bordered table-hover">
						<tr>
							<th colspan="3" style="text-align: right;">Total de ventas :</th>
							<th><?= number_format($tv, 2, '.', ',') ?></th>
						</tr>
						<?php if ($admin == 1) { ?>
							<tr>
								<th colspan="3" style="text-align: right;">Total de capital :</th>
								<th><?= $tc ?></th>
							</tr>
							<tr>
								<th colspan="3" style="text-align: right;">Total de Ganancia :</th>
								<th><?= $tv - $tc ?></th>
							</tr>
						<?php }
						?>
					</table>

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