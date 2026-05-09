<?php
	
	$product = Factura2Data::getByNumDoc($_GET["num"]);
	$comp_cab = Not_1_2Data::getById($product->id, 7);
	// $comp_aca = Aca_1_2Data::getById($product->id, 1);
	$detalles = Det_1_2Data::getById($product->id, 7);
	$comp_tri = Tri_1_2Data::getById($product->id, 7);
	$comp_ley = Ley_1_2Data::getById($product->id, 7);

	$sell = SellData::getByNroDoc($comp_cab->serieDocModifica);
    $cajero= '';
    $cajero = UserData::getById($sell->user_id)->username;
    $empresa = EmpresaData::getDatos();

	$fechaObj = new DateTime($comp_cab->fecEmision);
	$fechaFormateada = $fechaObj->format('d/m/Y');
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
                        <div class="row" style="display: flex; justify-content: center;">
							<div class="col-sm-10">
								<!-- card -->
								<div class="card col-sm-10">
									<div class="row" style="display: flex; justify-content: center;">
										<table class="table table-bordered">
											<tr>
												<td style="text-align: center; width: 100px">
													<img src="dist/img/logo.jpg" style="height: 80px; width: 100%"><br>
												</td>
												<td	style="text-align: center; font-family: courier new; font-size: 0.9em; width: 250px">
													<center>
														<b>
															<?php echo $empresa->Emp_RazonSocial ?><br>
															<?php echo $empresa->Emp_Direccion ?><br>
															CELULAR <?php echo ": ".$empresa->Emp_Telefono?><br>
															EMAIL <?php echo ": ".$empresa->Emp_Celular?>
														</b>
													</center>
												</td>
												<td	style="text-align: center; font-family: courier new; font-size: 0.9em; width: 150px">
														<b>RUC: <?php echo $empresa->Emp_Ruc ?></b>
														<div style="background-color: black; color: #fff;">
															<b>NOTA DE CRÉDITO</b>
														</div>
														<b><?php echo $product->SERIE."-".$product->COMPROBANTE; ?></b>
														<div style=" color: red">
																<b>
																	<?php 	if($comp_cab->codTipoNota==1){ echo("Anulación en la Operación");} 
																				else if($comp_cab->codTipoNota==2){ echo("Anulación por error en el RUC");}  
																				else if($comp_cab->codTipoNota==3){ echo("Correción por error en la descripción");} 
																				else if($comp_cab->codTipoNota==4){ echo("Descuento global");} 
																				else if($comp_cab->codTipoNota==5){ echo("Descuento por item");} 
																				else if($comp_cab->codTipoNota==6){ echo("Devolución total");} 
																				else if($comp_cab->codTipoNota==7){ echo("Devolución por item");} ?>
																</b>
														</div>
												</td>
											</tr>
										</table>
									</div>
									<div class="row">
										<table class="table table-bordered" style="font-family: courier new; font-size: 0.9em">
											<tr>
												<td class="col-sm-3">
													<b>FECHA EMISIÓN</b>
												</td>												
												<td>	
													 <b><?php echo ": ".$fechaFormateada."  ".$comp_cab->horEmision; ?></b>
												</td>
											</tr>
										</table>	
										<label>Documento que modifica:</label>
										<table class="table table-bordered" style="font-family: courier new; font-size: 0.9em">
											<tr>
												<td class="col-sm-3">
														<b>Factura Electrónica</b>
												</td>
												<td>
													<b><?php echo ": ". $comp_cab->serieDocModifica; ?></b>
												</td>
											</tr>
											<tr>	
												<td class="col-sm-3">
														<b>RUC</b>
												</td>
												<td>
													<b><?php echo ": ".$comp_cab->numDocUsuario; ?></b>
												</td>
											</tr>
											<tr>
												<td class="col-sm-3">
														<b>Razón Social</b>
												</td>
												<td>
													<b><?php echo ": ".$comp_cab->rznSocialUsuario; ?></b>
												</td>
											</tr>
										</table>
										<table class="table table-bordered">
											<tr>
												<td class="col-sm-3">
														<b>Motivo</b>
												</td>
												<td>
													<b><?php echo ": ".$comp_cab->descMotivo; ?></b>
												</td>
											</tr>
										</table>
										<table class="table table-bordered" style="font-family: courier new; font-size: 0.9em">
											<thead style="background-color: #000; color:#fff">
												<th scope="col">CANTIDAD</th>
												<th scope="col">DESCRIPCION</th>
												<th scope="col">P. UNIT.</th>
												<th scope="col">TOTAL</th>
											</thead>
											<tbody>
												<?php
														$total = 0;
														foreach ($detalles as $det) {
															?>
												<tr>
													<td align="center"><?php echo $det->ctdUnidadItem; ?></td>
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
										<table class="table table-bordered" style="font-family: courier new; font-size: 0.9em ">
											<thead>
												<th style="width: 50%">
													<table class="table table-bordered">
															<tr>
																<td>
																	<?php echo "Usuario:" . $cajero; ?>
																</td>
															</tr>
														</table>
												</th>
												<th>
													<table class="table table-bordered">
														<tr>
															<td>OPERACIÓN GRATUITA</td>
															<td>0.00</td>
														</tr>
														<tr>
															<td>OPERACIÓN EXONERADA</td>
															<td><?php if($comp_cab->codTipoNota==1 || $comp_cab->codTipoNota==2 || $comp_cab->codTipoNota==4 || $comp_cab->codTipoNota==5 || $comp_cab->codTipoNota==6|| $comp_cab->codTipoNota==7){  echo number_format($total, 2, '.', ',');} else if($comp_cab->codTipoNota==3){ echo("S/ 0.00");}?>
															</td>
														</tr>
														<tr>
															<td>OPERACIÓN INAFECTA</td>
															<td>0.00</td>
														</tr>
														<tr>
															<td>OPERACIÓN GRAVADA</td>
															<td>0.00</td>
														</tr>
														<tr>
															<td>IGV</td>
															<td>0.00</td>
														</tr>
														<tr>
															<td>MONTO TOTAL</td>
															<td><?php if($comp_cab->codTipoNota==1 || $comp_cab->codTipoNota==2 || $comp_cab->codTipoNota==4 || $comp_cab->codTipoNota==5 || $comp_cab->codTipoNota==6 || $comp_cab->codTipoNota==7){  echo number_format($total, 2, '.', ',');} else if($comp_cab->codTipoNota==3){ echo("S/ 0.00");}?>
															</td>
														</tr>
													</table>
												</th>
											</thead>
										</table>
									</div>
								</div>
								<!-- end card  -->
							</div>	 						
                        </div>
                    </div>
                </div><!--  fin col-md-6 -->
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>