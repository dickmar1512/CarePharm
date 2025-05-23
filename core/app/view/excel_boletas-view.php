<?php 

header('Content-Type: application/vnd.ms-excel');

header('Expires: 0');

header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

header('content-disposition: attachment;filename=BOLETAS.xls');

?>

<section class="content">
		<h1>Reporte de Boleta</h1>
		
<!--- -->
<div class="row">	
	<div class="col-md-12">
		<?php if(isset($_GET["ini"]) && isset($_GET["fin"])):?>
			<?php if($_GET["ini"]!=""&&$_GET["fin"]!=""):?>
			<?php 
				$boletas = array();
				$boletas = Boleta2Data::get_boletas_x_fecha($_GET["ini"],$_GET["fin"], 0, "");
			?>
		<?php if(count($boletas) > 0):?>
			
			
		<div class="div-imprimir">
		<table border="1">
			<?php 
				$subtotal = 0;
				$descuento = 0;
				$total = 0;
				$nro = 0;
			?>


				<?php 
						$permiso1 = PermisoData::get_permiso_x_key('institucion'); 
				?>
			<thead>
				<th>N°</th>
				<th>SERIE</th>
				<th>COMPROBANTE</th>
				<th>FECHA EMISIÓN</th>
				<th>N° DOC USUARIO</th>
				<th>NOMBRES Y APELLIDOS</th>
				<th class="<?php if($permiso1->Pee_Valor==0){echo 'hide';} ?>">GRADO Y SECC</th>
				<th>DETALLE</th>
				<th>PRECIO VENTA</th>
				<th></th>
				<!-- <th class="boton">ESTADO SUNAT</th> -->
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
						} ?>
			"
			>
				<td><?php echo $nro ?></td>
				<td><?php echo $bol->SERIE ?></td>
				<td><?php echo $bol->COMPROBANTE ?></td>
				<td><?php echo $bol->fecEmision ?></td>
				<td><?php echo $bol->numDocUsuario ?></td>
				<td><?php echo $bol->rznSocialUsuario ?></td>
				<td class="<?php if($permiso1->Pee_Valor==0){echo 'hide';} ?>">
					<?php
					if($permiso1->Pee_Valor==1){
						$sell = SellData::getById($bol->EXTRA1);
						$person = PersonData::getById($sell->person_id); 
						echo $person->company;
					}
						 ?>
						
					</td>
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

							}
							elseif ($probar->TIPO_DOC==7) {	$valor=0; }
							else {
									$valor = $bol->sumPrecioVenta;
							}
				$total += $valor;
				}else {
									$valor = $bol->sumPrecioVenta;
									$total += $valor;
							}
				echo 'S/ '.$valor; ?></td>
				<td><?php if ($valor == 0) {
					echo "Con Nota de Crédito Emitido";
				}else{
					echo "";
				} ?></td>

				<!-- <td style="text-align: center;" class="<?php if($bol->ESTADO==1){ echo "btn-success"; } else{ echo "btn-danger"; } ?>">
					<?php if($bol->ESTADO==1){ echo "GENERADO"; } else{ echo "RECHAZADA"; } ?>
				</td> -->

				</td>


				
			</tr>
		<?php
			endforeach; 
			?>
			<tfoot>
				<td colspan="7"></td>
				<td><?php echo 'S/ '. number_format($total,2,'.',',');?></td>
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