<?php
	$product = Factura2Data::getByNumDoc($_GET["num"]);
	$comp_cab = Not_1_2Data::getById($product->id, 7);
	// $comp_aca = Aca_1_2Data::getById($product->id, 1);
	$detalles = Det_1_2Data::getById($product->id, 7);
	$comp_tri = Tri_1_2Data::getById($product->id, 7);
	$comp_ley = Ley_1_2Data::getById($product->id, 7);

	$sell = SellData::getByNroDoc($comp_cab->serieDocModifica);
    $cajero= null;
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

<!-- Main content -->
<section class="content">
	<div class="container-fluid col-md-8">
		<div class="card card-default">
			<div class="card-header">
				<h2 class="card-title">Nota de Crédito</h2>				
					<div class="text-center">
						<button id="imprimir" class="btn btn-md btn-info"><i class="fa fa-print"></i> IMPRIMIR</button>
					</div>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="row" style="background: #FFF; margin-top: 0px; padding-top: 0px; display: flex; justify-content: center;">
					<div class="col-md-8 col-md-offset-2">
						<div class="row" id="ticketera">
							<div class="col-md-8 col-md-offset-2" style="border-right: 1px solid #ccc; border-left: 1px solid #cccc; border: 1px solid #cccc;">
								<table class="table">
									<tr style="text-align: center;">
										<td class="logo">
											<img src="dist/img/logo.jpg" style="height: 65px; width: 200px"><br>
										</td>
									</tr>
									<tr>	
										<td class="text-center" style="font-family: courier new; font-size: 0.7em">
											<b><?php echo $empresa->Emp_RazonSocial ?><br>
											<?php echo $empresa->Emp_Direccion ?><br>
											Teléfono <?php echo ": ".$empresa->Emp_Telefono ?><br>
											Correo <?php echo ": ".$empresa->Emp_Celular?></b>						
										</td>
									</tr>
									<tr>
										<td colspan="2" style="text-align: center;font-family: courier new; font-size: 0.7em">	  <strong>RUC <?php echo $empresa->Emp_Ruc ?><br>
											<span class="label-comprobante">NOTA DE CRÉDITO ELECTRÓNICA</span>
											<br>
											<span><?php echo $product->SERIE."-".$product->COMPROBANTE; ?></span>
											<div style=" color: red">
												<b><?php if($comp_cab->codTipoNota==1){ echo("Anulación en la Operación");} else if($comp_cab->codTipoNota==2){ echo("Anulación por error en el RUC");}  else if($comp_cab->codTipoNota==3){ echo("Correción por error en la descripción");} else if($comp_cab->codTipoNota==4){ echo("Descuento global");} else if($comp_cab->codTipoNota==5){ echo("Descuento por item");} else if($comp_cab->codTipoNota==6){ echo("Devolución total");} else if($comp_cab->codTipoNota==7){ echo("Devolución por item");} ?></b>
											</div>
											</strong>
										</td>
									</tr>
									<tr>
										<td style="font-family: courier new; font-size: 0.7em">	
											<b>DOCUMENTO QUE MODIFICA</b><br>
											<b>FACTURA ELECTRÓNICA <?php echo ": ".$comp_cab->serieDocModifica; ?></b>
											<br>
											<b>RUC <?php echo ": ".$comp_cab->numDocUsuario; ?>
											<br>
											RAZÓN SOCIAL <?php echo ": ".$comp_cab->rznSocialUsuario; ?>
											<br>
											MOTIVO <?php echo ": ".$comp_cab->descMotivo; ?></b>							
										</td>
									</tr>
								</table>				
								<div id="detalle_venta" <?php if(isset($_GET['con'])){ ?> style="display: none" <?php } ?>>
									<table class="table" style="font-family: courier new; font-size: 0.7em">
										<thead class="thead-dark">
											<th>CANTIDAD</th>							
											<th>DESCRIPCION</th>
											<th>P. UNIT.</th>
											<th>P. TOTAL</th>
										</thead>
										<tbody>
											<?php
												$total = 0;
												foreach ($detalles as $det) {
													?>
														<tr>
															<td><b><?php echo $det->ctdUnidadItem; ?></b></td>
															<td><b><?php echo $det->desItem; ?></b></td>
															<td><b><?php echo $det->mtoValorUnitario; ?></b></td>
															<td><b><?php echo $det->mtoValorVentaItem; ?></b></td>
														</tr>
													<?php
													$total = $det->mtoValorVentaItem + $total;
												}
											?>
										</tbody>
									</table>
								</div>
								<div style="text-align: right;">
									<table style="width: 40%">
										<tr></tr>
									</table>
									<table style="width: 60%; float: right; font-family: courier new; font-size: 0.7em;">
										<tbody style="text-align: left;">
											<tr>
												<td><b>OP. GRATUITA</b></td>
												<td><b>0.00</b></td>
											</tr>
											<tr>
												<td><b>OP. EXONERADA</b></td>
												<td><b><?php echo number_format($total, 2, '.', ','); ?></b></td>
											</tr>
											<tr>
												<td><b>OP. INAFECTA</b></td>
												<td><b>0.00</b></td>
											</tr>
											<tr>
												<td><b>OP. GRAVADA</b></td>
												<td><b>0.00</b></td>
											</tr>
											<tr>
												<td><b>IGV</b></td>
												<td><b>0.00</b></td>
											</tr>
											<tr>
												<td><b>MONTO TOTAL</b></td>
												<td><b><?php echo number_format($total, 2, '.', ','); ?></b></td>
											</tr>
											<tr>
												<td><b>EFECTIVO</td>
												<td><b><?php echo number_format($sell->cash,2,'.',','); ?></b></td>
											</tr>
											<tr>
												<td><b>VUELTO</b></td>
												<td><b><?php echo number_format($sell->cash - $total,2,'.',','); ?></b></td>
											</tr>							
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div><!--  fin col-md-6 -->				
			</div>
		</div>
	</div><!-- /.container-fluid -->
</section>	
<!-- /.content -->