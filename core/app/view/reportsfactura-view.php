<section class="content">
<div class="row">
	<div class="col-md-12">
		<h1>Reporte de Facturas</h1>
		<form>
			<input type="hidden" name="view" value="reportsfactura">
			<div class="row">
				<?php
					$series = Factura2Data::get_series();
				?>
				<div class="col-md-2">
					<select name="selSerie" class="form-control">
						<option value="0">:: Seleccione ::</option>
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
				$facturas = Factura2Data::get_facturas_x_fecha($_GET["sd"],$_GET["ed"], $_GET["selSerie"], $_GET["comprobante"]);
			?>
		<?php if(count($facturas) > 0):?>
			<p align="center"><a href="./?view=excel_facturas&ini=<?php echo $_GET['sd'] ?>&fin=<?php echo $_GET['ed']?>"; class="btn btn-success"><i class="fa fa-file-excel-o"></i> Excel</a></p>
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
				<th>N° DOC USUARIO</th>
				<th>RAZÓN SOCIAL</th>
				<th>PRECIO VENTA</th>
				<!-- <th class="boton">ESTADO SUNAT</th> -->
				<th class="boton">NOTA CREDITO</th>
				<th class="boton">VER</th>
			</thead>
			<?php
				foreach($facturas as $fac)
				{
					$nro++;
					//VERIFICAR SI SE EMITIO NOTAS DE CREDITO O DEBITO 
					$notacomprobar = $fac->SERIE."-".$fac->COMPROBANTE; 
					$probar = Not_1_2Data::getByIdComprobado($notacomprobar);
					?>
						<tr

						style="background: <?php if (isset($probar)) {
							if ($probar->TIPO_DOC==8) {	echo "#C2FCCF"; }
							if ($probar->TIPO_DOC==7) {	echo "#FFC4C4"; }
						} ?>
						"
						>
							<td><?php echo $nro ?></td>
							<td><?php echo $fac->SERIE ?></td>
							<td><?php echo $fac->COMPROBANTE ?></td>
							<td><?php echo $fac->fecEmision ?></td>
							<td><?php echo $fac->numDocUsuario ?></td>
							<td><?php echo $fac->rznSocialUsuario ?></td>
							<td>
								<?php 
						$valor = 0;
						 if (isset($probar)) {
									if ($probar->TIPO_DOC==8) {	
										$valor = $fac->sumPrecioVenta  + (float)$probar->sumPrecioVenta;

									}
									elseif ($probar->TIPO_DOC==7) {	$valor=$probar->sumTotValVenta; }
									else {
											$valor = $fac->sumPrecioVenta;
									}
							$total += $valor;
						}else {
											$valor = $fac->sumPrecioVenta;
											$total += $valor;
									}

						echo $valor ?></td>
							
				<td class="text-center">
					<?php 
						if (isset($probar)) {
							if ($probar->TIPO_DOC==7) {
								echo "N.C: ".$probar->SERIE."-".$probar->COMPROBANTE;
							}
							if ($probar->TIPO_DOC==8) {
								echo "Nota de Debito emitida";
							}
						}else{
					?>

					<a href="./?view=nocfactura&id=<?php echo $fac->EXTRA1 ?>" 
						class="btn btn-xs " style="background: red; color: #FFF">
						<i class="fa fa-file"> N.Cre.</i></a>
					<!--a href="./?view=nodfactura&id=<?php echo $fac->EXTRA1 ?>" 
						class="btn btn-xs " style="background: green; color: #FFF">
						<i class="fa fa-file"> N.Deb.</i></a-->

						<?php } ?>
				</td>
				<td>
					<a href="./?view=onesell&id=<?php echo $fac->EXTRA1 ?>&tipodoc=1"
						style="background-color: #000; font-size: 15px;"
						class="btn btn-info btn-xs"><i class="fa fa-eye"> Ver</i></a>		
				</td>
						</tr>
					<?php
					//$total = $fac->sumPrecioVenta + $total;
				}
				
			?>
			<tfoot>
				<td colspan="6"></td>
				<td><?php echo ''. number_format($total,2,'.',',');?></td>
			</tfoot>
		</table>
		<h1>Total:<?php echo number_format($total,2,'.',','); ?></h1>

		<?php else:
		// si no hay operaciones
		?>
	<script>
		$("#wellcome").hide();
	</script>
	<div class="jumbotron">
		<h2>No hay facturas</h2>
		<p>El rango de fechas seleccionado no proporciono ningun resultado de facturas.</p>
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