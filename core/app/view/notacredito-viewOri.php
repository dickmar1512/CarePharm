<?php
	
	$product = Factura2Data::getByNumDoc($_GET["num"]);
	$comp_cab = Not_1_2Data::getById($product->id, 7);
	// $comp_aca = Aca_1_2Data::getById($product->id, 1);
	$detalles = Det_1_2Data::getById($product->id, 7);
	$comp_tri = Tri_1_2Data::getById($product->id, 7);
	$comp_ley = Ley_1_2Data::getById($product->id, 7);

	// print_r($comp_cab );
	// print_r($detalles );
	// print_r($comp_tri );
	// print_r($comp_ley );

	$sell = SellData::getByNroDoc($comp_cab->serieDocModifica);
    $cajero= '';
    $cajero = UserData::getById($sell->user_id);
    $empresa = EmpresaData::getDatos();
?>
<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><i class='fas fa-file-invoice'></i> Nota de credito</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Vista de Impresión</a></li>
				</ol>
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
 <section class="content">
	<div class="container-fluid">
		<div class="card card-default">
			<div class="card-header">
				<div class="row text-center">
					<div class="col-md-3">
						<div class="row">
							<h4>Imprimir Nota de Crédito</h4>
						</div>
					</div>
					<div class="col-md-9">
						<div class="row">
							<div class="col-md-2">
								<div class="float">
									<button id="imprimir80mm" class="btn btn-md btn-info">
										<i class="fa fa-print"></i>
										IMPRIMIR 80mm
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row" style="margin-top: 0px; padding-top: 0px; background: #fff">
					<div class="col-md-10" style="display: flex; justify-content: center;">
						<!-- <div class="row ">
							<div class="row pull-right">
								<button id="imprimir" class="btn btn-md btn-info"><i class="fa fa-print"></i> IMPRIMIR</button>
							</div>
						</div> -->
						<div class="row" style="display: flex; justify-content: center;">
							<div class="para_imprimir">
								<br>
								<table style="margin-top: 0px; padding-top: 0px">
									<tr style="margin-top: 0px; padding-top: 0px">
										<td style="text-align: center; width: 180px; margin-top: -5px; padding-top: 0px">
											<img src="dist/img/logo2.jpg" style="height: 150px; width: 100%"><br>
										</td>
										<td style="text-align: center; color: green; margin-top: 0px; padding-top: 0px; width: 420px">
											<h2 style="margin-top: 0px; padding-top: 0px"><b><?php echo $empresa->Emp_RazonSocial ?></b></h2>
											<h5><b><?php echo $empresa->Emp_Descripcion ?></b></h5>
											<p style="margin: 2px;"><?php echo $empresa->Emp_Direccion ?></p>
											<p style="margin: 2px;">Cel.: <?php echo $empresa->Emp_Celular?></p>
											<h5 style="">FEC. EMIS.: <?php echo ": ".$comp_cab->fecEmision." | ".$comp_cab->horEmision; ?></h5>
										</td>
										<td style="text-align: center; width: 230px; border-color: #222; border-width: 20px; margin-top: 0px; padding-top: 0px">
											<div class="row" >
												<h2><b>RUC: <?php echo $empresa->Emp_Ruc ?></b></h2>
												<div>
													<b>NOTA DE CRÉDITO</b>
												</div>
												<div>
													<h2><?php echo $product->SERIE."-".$product->COMPROBANTE; ?></h2>
												</div>
												<div style=" color: red">
													<h5>
														<b>
															<?php 	if($comp_cab->codTipoNota==1){ echo("Anulación en la Operación");} 
																	else if($comp_cab->codTipoNota==2){ echo("Anulación por error en el RUC");}  
																	else if($comp_cab->codTipoNota==3){ echo("Correción por error en la descripción");} 
																	else if($comp_cab->codTipoNota==4){ echo("Descuento global");} 
																	else if($comp_cab->codTipoNota==5){ echo("Descuento por item");} 
																	else if($comp_cab->codTipoNota==6){ echo("Devolución total");} 
																	else if($comp_cab->codTipoNota==7){ echo("Devolución por item");} ?>
														</b>
													</h5>
												</div>
											</div>
										</td>
									</tr>
								</table>
								<label>Documento que modifica:</label>
								<table>
									<tr>
										<td><div class="container" style="width: 100px">
												<p><b>Factura Electrónica</b></p>
											</div></td>
										<td>
										<td><div class="container" style="width: 130px">
												<p><b>:<?php echo $comp_cab->serieDocModifica; ?></b></p>
											</div></td>
										<td>
											<div class="container" style="width: 60px">
												<p><b>RUC</b></p>
											</div>
										</td>
										<td class="container" style="width: 120px">
											<p><?php echo ": ".$comp_cab->numDocUsuario; ?></p>
										</td>
										<td>
											<div class="container" style="width: 120px">
												<p><b>Razón Social</b></p>
											</div>
										</td>
										<td class="container" style="width: 130px">
											<p><?php echo ": ".$comp_cab->rznSocialUsuario; ?></p>
										</td>	        
									</tr>
								</table>
								<table>
									<tr>
										<td><div class="container" style="width: 200px">
												<p><b>Motivo</b></p>
											</div></td>
										<td class="container" style="width: 400px">
											<p><?php echo ": ".$comp_cab->descMotivo; ?></p>
										</td>	        
									</tr>
								</table>
								<table class="table-bordered" style="max-width: 900px">
									<thead class="thead-dark">
										<th style="width: 50px">CANTIDAD</th>
										<th style="width: 450px">DESCRIPCION</th>
										<th style="width: 200px">PRECIO UNIT.</th>
										<th style="width: 200px">IMPORTE</th>
									</thead>
									<tbody>
										<?php
											$total = 0;
											foreach ($detalles as $det) {
												?>
													<tr>
														<td><?php echo $det->ctdUnidadItem; ?></td>
														<td><?php echo $det->desItem; ?></td>
														<td><?php echo $det->mtoValorUnitario; ?></td>
														<td><?php echo $det->mtoValorVentaItem; ?></td>
													</tr>
												<?php
												$total = $det->mtoValorVentaItem + $total;
											}
										?>
									</tbody>
								</table>
								<table class="table table-bordered"  style="max-width: 900px; margin-bottom: 0px; padding-bottom:  0px ">
									<thead style="align-content: center; text-align: center; border-style: none">
										<th style="align-content: center; text-align: center;">
											<table>
												<tr scope="col"><td><b><?php echo $comp_ley->desLeyenda; ?></b></td></tr>
												<tr><td style="font-size: 11px">Consulte y/o descargue su comprobante electronico en www.sunat.gob.pe, utilizando su clave SOL</td></tr>
												<tr><td style="font-size: 11px"><p>Autorizado para ser emisor electrónico mediante la Resolución de Superintendencia N° 155-2017</p></td></tr>
											</table>
										</th>	
										<th>
											<table>
												<tr>
													<td style="width: 270px;">OP. GRATUITA</td>
													<td style="min-width: 190px">S/ 0.00</td></tr>
												<tr>
													<td>OP. EXONERADA</td>
													<td><?php if($comp_cab->codTipoNota==1 || $comp_cab->codTipoNota==2 || $comp_cab->codTipoNota==4 || $comp_cab->codTipoNota==5 || $comp_cab->codTipoNota==6|| $comp_cab->codTipoNota==7){  echo('S/'); echo number_format($total, 2, '.', ',');} else if($comp_cab->codTipoNota==3){ echo("S/ 0.00");}?></td></tr>
												<tr>
													<td>OP. INAFECTA</td>
													<td>S/ 0.00</td></tr>
												<tr>
													<td>OP. GRAVADA</td>
													<td>S/ 0.00</td></tr>
												<tr>
													<td>IGV</td>
													<td>S/ 0.00</td></tr>
												<tr>
													<td>MONTO TOTAL</td>
													<td><?php if($comp_cab->codTipoNota==1 || $comp_cab->codTipoNota==2 || $comp_cab->codTipoNota==4 || $comp_cab->codTipoNota==5 || $comp_cab->codTipoNota==6 || $comp_cab->codTipoNota==7){  echo('S/'); echo number_format($total, 2, '.', ',');} else if($comp_cab->codTipoNota==3){ echo("S/ 0.00");}?></td>
												</tr>				
											</table>
										</th>
									</thead>
								</table>
							</div>
						</div>
					</div>
				</div><!--  fin col-md-6 -->				
			</div>
		</div>
	</div><!-- /.container-fluid -->
</section>	




<script>
  $(document).ready(function(){
	    $("#product_code").keydown(function(e){
	        if(e.which==17 || e.which==74 ){
	            e.preventDefault();
	        }else{
	            console.log(e.which);
	        }
	    })

	    $('#imprimir').click(function() {
	    	$('#imprimir').hide();
	      	$('#div_opciones').hide();
	      	$('.logo').hide();
	      	window.print();

	      	$('#imprimir').show();
	      	$('#div_opciones').show(); 
	      	$('.logo').show(); 
	    });
	});
</script>