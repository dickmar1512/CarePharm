<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><i class='fa fa-cart-plus'></i> Reporte de Boleta para realizar notas de credito</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Reportes</a></li>
					<li class="breadcrumb-item active">Venta x Cliente</li>
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
					<div class="col-md-12">
						<form>
							<input type="hidden" name="view" value="reportsboleta">
							<div class="row">
								<?php
									$series = Boleta2Data::get_series_notas_credito();
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
				<div class="clearfix"></div>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="row">	
					<div class="col-md-12">
						<?php if(isset($_GET["sd"]) && isset($_GET["ed"]) && isset($_GET["selSerie"]) && isset($_GET["comprobante"])):?>
							<?php if($_GET["sd"]!=""&&$_GET["ed"]!=""):?>
							<?php 
								$boletas = array();
								$boletas = Boleta2Data::get_boletas_x_fecha($_GET["sd"],$_GET["ed"], $_GET["selSerie"], $_GET["comprobante"]);
							?>
						<?php if(count($boletas) > 0):?>
							<!-- <p align="center"><a href="./?view=excel_boletas&ini=<?php echo $_GET['sd'] ?>&fin=<?php echo $_GET['ed']?>"; class="btn btn-success"><i class="fa fa-file-excel-o"></i> Excel</a></p> -->
							
						<div class="div-imprimir">
						<table class="table table-bordered table-hover datatable">
							<?php 
								$subtotal = 0;
								$descuento = 0;
								$total = 0;
								$nro = 0;
								$permiso1 = PermisoData::get_permiso_x_key('institucion'); 
								?>
							<thead class="thead-dark">
								<th>N°</th>
								<th>SERIE</th>
								<th>COMPROBANTE</th>
								<th>FECHA EMISIÓN</th>
								<th>N° DOC USUARIO</th>
								<th>CLIENTE</th>
								<th>DETALLE</th>
								<th>PRECIO VENTA</th>
								<th class="boton">NOTA CREDITO</th>
								<th class="boton">VER</th>
							</thead>
						<?php foreach($boletas as $bol):
							$nro++;
							$notacomprobar = $bol->SERIE."-".$bol->COMPROBANTE; 
							$probar = Not_1_2Data::getByIdComprobado($notacomprobar);
							?>
							<tr
							style="background: <?php if (isset($probar)) {
											if ($probar->TIPO_DOC==8) {	echo "#C2FCCF"; }
											if ($probar->TIPO_DOC==7) {	echo "#FFC4C4"; }
										} ?>">
								<td><?php echo $nro ?></td>
								<td><?php echo $bol->SERIE ?></td>
								<td><?php echo $bol->COMPROBANTE ?></td>
								<td><?php echo $bol->fecEmision ?></td>
								<td><?php echo $bol->numDocUsuario ?></td>
								<td><?php echo $bol->rznSocialUsuario ?></td>
								<td>
									<?php 
										$operations = OperationData::getAllProductsBySellId($bol->EXTRA1);

										foreach ($operations as $ope) 
										{
											$product = ProductData::getById($ope->product_id);
											echo $product->name;

											if($ope->descripcion != "")
											{
												echo "|".$ope->descripcion;
											}

											echo "<br>";
										}
									?>						
								</td>
								<td><?php 
								$valor = 0;
								
								if (isset($probar)) {
									if ($probar->TIPO_DOC==8) {	
										$valor = $bol->sumPrecioVenta  + (float)$probar->sumPrecioVenta;
									}elseif ($probar->TIPO_DOC==7) {
										$valor=0; 
									}else {
										$valor = $bol->sumPrecioVenta;
									}
										$total += $valor;
								}else {
										$valor = $bol->sumPrecioVenta;
										$total += $valor;
									}
								echo $valor; ?></td>

								<td class="text-center">
									<?php 
									//VERIFICAR SI SE EMITIO NOTAS DE CREDITO O DEBITO 
										if (isset($probar)) {
											if ($probar->TIPO_DOC==7) {
												echo $probar->SERIE."-".$probar->COMPROBANTE;
											}
											if ($probar->TIPO_DOC==8) {
												echo "Nota de Debito emitida";
											}
										}else{
									?>

									<a href="./?view=nocboleta&id=<?php echo $bol->EXTRA1 ?>" 
										class="btn btn-xs " style="background: red; color: #FFF">
										<i class="fa fa-file"> N.Cre.</i></a>
									<!--a href="./?view=nodboleta&id=<?php echo $bol->EXTRA1 ?>" 
										class="btn btn-xs " style="background: green; color: #FFF">
										<i class="fa fa-file"> N.Deb.</i></a-->

										<?php } ?>
								</td>


								<td class="text-center boton">
									<a href="./?view=onesell&id=<?php echo $bol->EXTRA1 ?>&tipodoc=3" class="btn btn-info btn-xs" 
										style="background-color: #000; font-size: 15px;"><i class="fa fa-eye"> Ver</i></a>
								</td>
							</tr>
						<?php
							endforeach; 
							?>
							<tfoot>
								<td colspan="7"></td>
								<td><?php echo number_format($total,2,'.',',');?></td>
							</tfoot>
						</table>
						</div>
				<h1>Total: S/ <?php echo number_format($total,2,'.',','); ?></h1>

							<?php else:
							// si no hay operaciones
							?>
				<script>
					$("#wellcome").hide();
				</script>
				<div class="jumbotron">
					<h2>No hay boletas</h2>
					<p>El rango de fechas seleccionado no proporciono ningun resultado de boletas.</p>
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
			</div>
		</div>
	</div>
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