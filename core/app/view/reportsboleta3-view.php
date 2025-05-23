<section class="content">
<div class="row">
	<div class="col-md-12">
		<h1>Reporte de Boleta</h1>
		<form>
			<input type="hidden" name="view" value="reportsboleta">
			<div class="row">
				<?php
					$series = Boleta2Data::get_series();
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
				$boletas = array();
				$boletas = Boleta2Data::get_boletas_x_fecha($_GET["sd"],$_GET["ed"], $_GET["selSerie"], $_GET["comprobante"]);
			?>
		<?php if(count($boletas) > 0):?>
		<table class="table table-bordered table-hover">
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
				<th>OPCIONES</th>
			</thead>
		<?php foreach($boletas as $bol):
			$nro++;
			?>
			<tr>
				<td><?php echo $nro ?></td>
				<td><?php echo $bol->SERIE ?></td>
				<td><?php echo $bol->COMPROBANTE ?></td>
				<td><?php echo $bol->fecEmision ?></td>
				<td><?php echo $bol->numDocUsuario ?></td>
				<td><?php echo $bol->rznSocialUsuario ?></td>
				<td><?php echo 'S/ '.$bol->sumPrecioVenta ?></td>
				<td class="text-center">
					<a href="index.php?view=onesellc&id=<?php echo $bol->EXTRA1 ?>" class="btn btn-info btn-xs"><i class="fa fa-file-pdf">Ver</i></a>
					<a href="index.php?view=nocboleta&id=<?php echo $bol->EXTRA1 ?>" class="btn btn-info btn-xs btn-warning"><i class="fa fa-file-pdf">N.C.</i></a>
								<a href="index.php?view=nodboleta&id=<?php echo $bol->EXTRA1 ?>" class="btn btn-info btn-xs btn-default"><i class="fa fa-file-pdf">N.D.</i></a>
				</td>
			</tr>
		<?php
		$total = $bol->sumPrecioVenta + $total;
			endforeach; 
			?>
			<tfoot>
				<td colspan="6"></td>
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