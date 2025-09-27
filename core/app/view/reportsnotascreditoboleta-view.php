<?php
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
<section class="content">
<div class="row">
	<div class="col-md-12">
		<h1><i class="fa fa-file"></i> Reporte de Notas de crédito</h1>
		<form>
			<input type="hidden" name="view" value="reportsnotascreditoboleta">
			<div class="row">
				<?php
					$series = Boleta2Data::get_series_notas_credito();
				?>
				<div class="col-md-2 hide">
					<select name="selSerie" class="form-control">
						<option class="hide" value="0">:: Seleccione ::</option>
						<?php
							foreach ($series as $serie) {
								?>
									<option value="<?php echo $serie->SERIE?>"><?php echo $serie->SERIE?></option>
								<?php
							}
						?>
					</select>
				</div>
				<div class="col-md-2">
					<input type="text" name="comprobante" placeholder="N° comprobante" class="form-control">
				</div>
				<div class="col-md-2">
					<input type="date" name="sd" value="<?php if(isset($_GET["sd"])){ echo $_GET["sd"]; } else { echo date("Y-m-d"); }?>" class="form-control">
				</div>
				<div class="col-md-2">
					<input type="date" name="ed" value="<?php if(isset($_GET["ed"])){ echo $_GET["ed"]; } else { echo date("Y-m-d"); } ?>" class="form-control">
				</div>
				<div class="col-md-2">
					<input type="submit" class="btn btn-primary btn-block" value="Procesar">
				</div>
			</div>
		</form>
	</div>
</div>
<br>
<!--- -->
<div class="row">	
	<div class="col-md-12">
		<?php if(isset($_GET["sd"]) && isset($_GET["ed"]) && isset($_GET["selSerie"]) && isset($_GET["comprobante"])):?>
			<?php if($_GET["sd"]!=""&&$_GET["ed"]!=""):?>
			<?php 
				$facturas = array();				
				$facturas = Not_1_2Data::get_notas_credito_boleta_x_fecha($_GET["sd"],$_GET["ed"]);
			?>
		<?php if(count($facturas) > 0):?>
			<button id="imprimir" class="btn btn-md btn-info"><i class="fa fa-print"></i> IMPRIMIR</button>
		<table class="table table-bordered">
			<?php 
				$subtotal = 0;
				$descuento = 0;
				$total = 0;
				$nro = 0;
			?>
			<thead>
				<th>N°</th>
				<th>SERIE</th>
				<th>COMPROBANTE</th>
				<th>FECHA EMISIÓN</th>
				<th>DOC MODIFICADO</th>
				<th>N° DOC USUARIO</th>
				<th>RAZÓN SOCIAL</th>
				<th>PRECIO VENTA</th>
				<th>ESTADO</th>
				<th class="text-center boton">OPCIONES</th>
			</thead>
			<?php
				foreach($facturas as $fac)
				{
					$nro++;

					try {
										$db = new SQLite3($dbPath);
										$query = "SELECT * FROM DOCUMENTO WHERE NUM_DOCU = '" . $fac->{"28"} . "-" . $fac->{"29"} . "'";
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
										//$estado = (isset($probar->TIPO_DOC) && $probar->TIPO_DOC ==7) ? "N.CRE: ".$probar->SERIE."-".$probar->COMPROBANTE :$nombreSituacion;
								
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
					?>
						<tr>
							<td><?php echo $nro ?></td>
							<td><?php echo $fac->{"28"} ?></td>
							<td><?php echo $fac->{"29"} ?></td>
							<td><?php echo $fac->fecEmision ?></td>
							<td><?php echo $fac->serieDocModifica ?></td>
							<td><?php echo $fac->numDocUsuario ?></td>
							<td><?php echo $fac->rznSocialUsuario ?></td>
							<td><?php echo 'S/ '.$fac->sumPrecioVenta ?></td>
							<td><?php echo $nombreSituacion ?></td>
							<td class="text-center boton">
								<?php 
								$nota_credito = Boleta2Data::getByID($fac->ID_TIPO_DOC);
								 ?>
								<!-- <a href="SFS_v1.1/sunat_archivos/sfs/REPO/<?php echo $fac->RUC ?>-01-<?php echo $fac->SERIE ?>-<?php echo $fac->COMPROBANTE ?>.pdf" class="btn btn-info btn-xs" target="_blank"><i class="fa fa-file-pdf">Ver</i> PDF</a> -->
								<a href="./?view=notacreditoboletat&num=<?php echo $nota_credito->SERIE . '-' . $nota_credito->COMPROBANTE ?>" class="btn btn-info btn-xs"><i class="fa fa-file">Ver</i></a>
							</td>
						</tr>
					<?php
					$total = $fac->sumPrecioVenta + $total;
				}
				
			?>
			<tfoot>
				<td colspan="7"></td>
				<td><?php echo 'S/ '. number_format($total,2,'.',',');?></td>
			</tfoot>
		</table>
		<h1>Total: S/ <?php echo number_format($total,2,'.',','); ?></h1>

		<?php else:
		// si no hay operaciones
		?>
	<script>
		$("#wellcome").hide();
	</script>
	<div class="jumbotron">
		<h2>No hay notas de crédito</h2>
		<p>El rango de fechas seleccionado no proporciono ningun resultado de notas.</p>
	</div>
	<?php endif; ?>
	<?php else:?>
	<script>
		$("#wellcome").hide();
	</script>
	<div class="jumbotron">
		<h2>Fecha Incorrectas</h2>
		<p>Puede ser que no selecciono un rango de fechas, o el rango seleccionado es incorrecto.</p>
	</div>
	<?php endif;?>

	<?php endif; ?>
	</div>
</div>

<br><br><br><br>
</section>
<script type="text/javascript">
	$('#imprimir').click(function() {
	    	imprimir();
	    });
	function imprimir()
    {
    	$('#imprimir').hide();
      	$('.boton').hide();
      	// $('.logo').hide();
      	window.print();

      	$('#imprimir').show();
      	$('.boton').show(); 
      	// $('.logo').show(); 
    }
</script>