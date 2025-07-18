<div class="row">
	<div class="col-md-12">
		<h1><i class='glyphicon glyphicon-shopping-cart'></i> Lista de Ventas</h1>
		<div class="clearfix"></div>

	<?php
		$products = SellData::getProformas();

		if(count($products)>0)
		{
			?>
				<br>
				<table class="table table-bordered table-hover	">
					<thead>
						<th></th>
						<th>Producto</th>
						<th>Total</th>
						<th>Fecha</th>
						<th></th>
					</thead>
					<?php foreach($products as $sell):?>

					<tr>
						<td style="width:30px;">
							<a href="./?view=proforma&id=<?php echo $sell->id; ?>" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-eye-open"></i></a>
						</td>
						<td>
							<?php
								$operations = OperationData::getAllProductsBySellId($sell->id);
								echo count($operations);
							?>
						<td>
							<?php
								$total= $sell->total-$sell->discount;
								echo "<b>S/ ".number_format($total)."</b>";
							?>
						</td>
						<td><?php echo $sell->created_at; ?></td>
						<td style="width:30px;"><a href="./?view=delsell&id=<?php echo $sell->id; ?>" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a></td>
					</tr>
				<?php endforeach; ?>

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
<br><br><br><br><br><br><br><br><br><br>
	</div>
</div>