<script type="text/javascript">
	function imprimir()
    {
    	$('#imprimir').hide();
      	$('#div_opciones').hide(); 
	    	$('.main-footer').hide();    	
      	// $('.logo').hide();
      	window.print();

      	$('#imprimir').show();
      	$('#div_opciones').show();         
	    	$('.main-footer').show();
      	// $('.logo').show(); 
    }
</script>
<?php
	if (isset($_GET['im'])) 
	{
		?>
			<script type="text/javascript">
				imprimir();
			</script>
		<?php
	}

	$product = Boleta2Data::getByNumDoc($_GET["num"]);
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
			</div><!-- /.card-header -->
			<div class="card-body">
				<div class="row" style="background: #FFF; margin-top: 0px; padding-top: 0px; display: flex; justify-content: center;">
					<div class="col-md-6 col-md-offset-1">
						<div class="row" id="ticketera">
							<div class="col-md-6 col-md-offset-1" style="border-right: 1px solid #ccc; border-left: 1px solid #cccc; border: 1px solid #cccc;">
								<table>
									<tr style="text-align: center;font-family: courier new; font-size: 0.7em">
										<td colspan="3">
											<img src="dist/img/logo.jpg" style="height: 65px; width: 200px"><br>
											<?php echo $empresa->Emp_Descripcion ?> <br>
											<?php echo $empresa->Emp_RazonSocial ?><br>
										</td>
									</tr>
									<tr>
										<td class="text-center" style="text-align: center;font-family: courier new; font-size: 0.7em">
											<?php echo $empresa->Emp_Direccion ?><br>
											Tel. <?php echo $empresa->Emp_Telefono ?><br>							
										</td>
									</tr>					
										<tr>
										<th>
										<center>
										<strong>--------------------------</strong>
										</center>
										</th>
										</tr>
									<tr>
										<td colspan="2" style="text-align: center;font-family: courier new; font-size: 0.7em">							
											<span>RUC <?php echo $empresa->Emp_Ruc ?></span><br>
											<span class="label-comprobante" style="font-weight: bold; background-color: black; color: white; height: 200px;">NOTA DE CRÉDITO ELECTRÓNICA</span>
											<br>
											<span><?php echo $product->SERIE."-".$product->COMPROBANTE; ?></span>
										</td>
									</tr>									
										<tr>
										<th>
										<center>
										<strong>--------------------------</strong>
										</center>
										</th>
										</tr>	
									<tr>
										<td>							
											<div style=" color: red; text-align: center;"><h5><b><?php if($comp_cab->codTipoNota==1){ echo("Anulación en la Operación");} else if($comp_cab->codTipoNota==2){ echo("Anulación por error en el RUC");}  else if($comp_cab->codTipoNota==3){ echo("Correción por error en la descripción");} else if($comp_cab->codTipoNota==4){ echo("Descuento global");} else if($comp_cab->codTipoNota==5){ echo("Descuento por item");} else if($comp_cab->codTipoNota==6){ echo("Devolución total");} else if($comp_cab->codTipoNota==7){ echo("Devolución por item");} ?></b></h5>
											</div>
										</td>
									</tr>				
									<tr>
										<th>
										<center>
										<strong>--------------------------</strong>
										</center>
										</th>
										</tr>
									<tr>
										<td colspan="3" style="font-family: courier new; font-size: 0.7em">	
											<b>DOCUMENTO QUE MODIFICA</b><br>
											<b>Boleta Electrónica: <?php echo $comp_cab->serieDocModifica; ?></b>
											<br>
											DNI&nbsp;&nbsp;&nbsp; <?php echo "   : ".$comp_cab->numDocUsuario; ?>
											<br>
											NOMBRE <?php echo ": ".$comp_cab->rznSocialUsuario; ?>
											<br>
											MOTIVO <?php echo ": ".$comp_cab->descMotivo; ?>							
										</td>
									</tr>
								</table>				
								<div id="detalle_venta" <?php if(isset($_GET['con'])){ ?> style="display: none" <?php } ?>>
									<table style="font-family: courier new; font-size: 0.7em" cellspacing="1" cellpadding="1" border="1" width="100%">
										<thead class="thead-dark">
											<th>Cant.</th>							
											<th>Descripcion</th>
											<th>Imp.Unit.</th>
											<th>Imp.Total</th>
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
								</div>
								<div style="text-align: right;">
									<table style="text-align: center;font-family: courier new; font-size: 0.7em; float: right;" border="1" cellpadding="1" cellspacing="1">
										<tbody style="text-align: left;">
											<tr>
												<td>OP. GRATUITA</td>
												<td>S/ 0.00</td>
											</tr>
											<tr>
												<td>OP. EXONERADA</td>
												<td>S/ <?php echo number_format($total, 2, '.', ','); ?></td>
											</tr>
											<tr>
												<td>OP. INAFECTA</td>
												<td>S/ 0.00</td>
											</tr>
											<tr>
												<td>OP. GRAVADA</td>
												<td>S/ 0.00</td>
											</tr>
											<tr>
												<td>IGV</td>
												<td>S/ 0.00</td>
											</tr>
											<tr>
												<td>MONTO TOTAL</td>
												<td>S/ <?php echo number_format($total, 2, '.', ','); ?></td>
											</tr>						
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div><!--  fin col-md-6 -->
			</div><!-- /.card-body -->
		</div><!-- /.card -->
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
	    	imprimir();
	    });	    
	});  
</script>