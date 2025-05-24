<div class="row">
	<div class="col-md-12">
		<h1><i class='glyphicon glyphicon-shopping-cart'></i> Registro de Ventas</h1>
		<form>
				<input type="hidden" name="view" value="sellsOrden">
				<div class="row">
					<div class="col-md-3">
					<input type="date" name="sd" value="<?php if(isset($_GET["sd"])){ echo $_GET["sd"]; } else { echo date("Y-m-d"); }?>" class="form-control">
					</div>
					<div class="col-md-3">
					   <input type="date" name="ed" value="<?php if(isset($_GET["ed"])){ echo $_GET["ed"]; } else { echo date("Y-m-d"); } ?>" class="form-control">
					</div>
					<div class="col-md-3">
					   <?php 
							$users = UserData::getAll();
				    	?>
				    	<select name="user_id" class="form-control">
				    		<option value="0">-- NINGUNO --</option>
				    		<?php foreach($users as $user):?>
				    		<option value="<?php echo $user->id;?>"><?php echo $user->username;?></option>
				    		<?php endforeach;?>
				    	</select>
					</div>
					<div class="col-md-3">
					   <input type="submit" class="btn btn-success btn-block" value="Procesar">
					</div>

				</div>
	        </form>
		<div class="clearfix"></div>

	<?php
	  $admin = UserData::getById($_SESSION["user_id"])->is_admin;
	  if(isset($_GET["sd"]) && isset($_GET["ed"]) ):
	  	if($_GET["user_id"]==0):
		$products = SellData::getSellsXfechaOv($_GET["sd"],$_GET["ed"]);
	    else:
	    $products = SellData::getSellsXfechaUsuarioOv($_GET["sd"],$_GET["ed"],$_GET["user_id"]);
	    endif;
	  else:
	  	$products = SellData::getSellsOv();
	  endif;	

		if(count($products)>0)
		{
			?>
				<br>
				<table class="table table-bordered table-hover	">
					<thead>
						<th></th>
						<th>Comprobante</th>
						<th>Cliente</th>
						<th>Total</th>
						<th>Fecha</th>
						<th>Usuario</th>
					</thead>
					<?php foreach($products as $sell):?>

					<tr>
						<td style="width:30px;">

							<?php
								if($sell->tipo_comprobante == 3)
								{
									if($sell->estado == 1)
									{
										?>
											<a href="./?view=onesellc&id=<?php echo $sell->id; ?>" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-eye-open"></i></a>
										<?php
									} 
									else
									{
										?>
											<a href="./?view=onesellf&id=<?php echo $sell->id; ?>" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-eye-open"></i></a>
										<?php
									}
								}
								else
								{
									if($sell->tipo_comprobante == 1)
									{
										if($sell->estado == 1)
										{
											?>
												<a href="./?view=onesell2c&id=<?php echo $sell->id; ?>" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-eye-open"></i></a>
											<?php
										} 
										else
										{
											?>
												<a href="./?view=onesell2f&id=<?php echo $sell->id; ?>" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-eye-open"></i></a>
											<?php
										}
									}
									else
									{ ?>
                                       <a href="./?view=onesellorden&id=<?php echo $sell->id; ?>" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-eye-open"></i></a>
							  <?php }
								}
								
							  $usuario = UserData::getById($sell->user_id);
							  $cliente = PersonData::getById($sell->person_id);	
							  $objOper = OperationData::getAllProductsBySellId($sell->id);
							  foreach($objOper as $oper)
							  {
							    $objProd = ProductData::getById($oper->product_id);							    
							    $tc += $oper->q*$objProd->price_in;
							   }
							?>
						</td>	
						<td>
							<?=$sell->serie."-".$sell->comprobante?>
						</td>
						<td><?=$cliente->name." ". $cliente->lastname?></td>
						<td>

				<?php
				$total = $sell->total;
						echo "<b>S/ ".number_format($total,2)."</b>";
				$tv +=	$total;	
				
				?>			

						</td>
						<td><?php echo $sell->created_at; ?></td>
						<td style="width:30px;">
							<!-- <a href="./?view=delsell&id=<?php echo $sell->id; ?>" class="btn btn-xs btn-danger">
								<i class="fa fa-trash"></i></a> -->
							<?=$usuario->username?>		
								</td>
					</tr>

				<?php endforeach; ?>
                 <tr>
                 	<th colspan="3" style="text-align: right;">Total de ventas:</th>
                 	<th>S/ <?=number_format($tv,2,'.',',')?></th>
                 	<th> Soles.</th>
                 </tr>
                 <?php if($admin==1){?>
                 <tr>
                 	<th colspan="3" style="text-align: right;">Total de capital:</th>
                 	<th>S/ <?=$tc?></th>
                 	<th> Soles.</th>
                 </tr>
                 <tr>
                 	<th colspan="3" style="text-align: right;">Total de Ganancia:</th>
                 	<th>S/ <?=$tv-$tc?></th>
                 	<th> Soles.</th>
                 </tr>
                 <?php }
                 ?>
				</table>

<div class="clearfix"></div>

	<?php
}else{
	?>
	<div class="jumbotron">
		<h2>No hay ventas</h2>
		<p>No se ha realizado ninguna venta.</p>
	</div>
	<?php
}

?>
<br>
	</div>
</div>