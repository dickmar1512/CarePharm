<style type="text/css">
	.seleccion::selection {
		background: yellow;
		border: 1px solid #39c !important;
	}

	/* Firefox */
	.seleccion::-moz-selection {
		background: yellow;
		border: 1px solid #39c !important;
	}
</style>
<?php
$is_desc = UserData::getById($_SESSION["user_id"])->is_desc;
if (isset($_GET["product"]) && $_GET["product"] != ""):
	//$products = ProductData::getLike(utf8_decode($_GET["product"]));
	$products = ProductData::getLike(htmlspecialchars($_GET["product"], ENT_NOQUOTES, "UTF-8"));
	if (count($products) > 0) {
		?>
		<table class="table table-bordered table-hover" style="font-size: 1em">
			<thead class="bg bg-info">
				<tr>
					<th colspan="4">
						RESULTADO DE LA BUSQUEDA
					</th>
				</tr>
				<tr>
					<!-- <th>CODIGO</th> -->
					<th>NOMBRE</th>
					<th>INVENTARIO</th>
					<th style="width:700px;">
						<div class="label-group">
							<label style="width: 300px;">DESCRIPCION ADICIONAL</label>
							<label style="width: 90px;">P.UNIT</label>
							<label style="width: 100px;">DESCUENTO</label>
							<label style="width: 100px;">CANTIDAD</label>
							<label style="width: 100px;">&nbsp;</label>
						</div>
					</th>
					<!-- <th class="hide"></th> -->
				</tr>
			</thead>
			<?php
			$products_in_cero = 0;
			foreach ($products as $product):
				$q = $product->stock;

				if ($q > 0 or $product->is_stock == 0): ?>

					<tr class="<?php if ($q <= $product->inventary_min) {
						echo "danger";
					} ?>" style="font-size: 1em">
						<!-- <td style="width:80px;"><?php echo $product->barcode; ?></td> -->
						<td><?php echo $product->name; ?></td>
						<td><?php if ($product->is_stock == 0) {
							echo "Ilimitado";
						} else {
							echo $q;
						} ?></td>
						<td style="width:700px;">
							<form method="post" action="index.php?view=addtocart">
								<input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
								<input type="hidden" name="idpaquete" value="X">
								<input type="hidden" name="f_price_in" value="<?php echo $product->price_in; ?>">
								<div class="input-group">
									<input type="descripcion" step="any" class="form-control seleccion" name="descripcion"
										placeholder="Descripcion" style="width: 285px;" min="0">
									<input type="number" step="any" class="form-control" required name="precio_unitario"
										placeholder="Precio Unitario" value="<?php echo $product->price_out ?>"
										style="width: 100px;" min="0">
									<?php if ($is_desc == 1): ?>
										<input type="number" step="any" class="form-control" required name="descuento"
											placeholder="Descuento" value="0.00" style="width: 100px;" min="0">
									<?php else: ?>
										<label class="form-control" style="width: 100px;">0.00<input type="hidden"
												name="descuento" value="0"></label>
									<?php endif ?>
									<?php $permiso = PermisoData::get_permiso_x_key('decimales');
									if ($permiso->Pee_Valor == 1) { ?>
										<input type="number" class="form-control" required name="q" placeholder="Cantidad ..." step='any'
											value="1" style="width: 90px; float: left;">

										<button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-plus-sign"></i>
											Agregar</button>
									<?php } else {
										?>

										<input type="number" class="form-control" required name="q" placeholder="Cantidad ..." value="1"
											style="width: 90px; float: left;">
										<button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-plus-sign"></i>
											Agregar</button>
									<?php } ?>
								</div>
								<?php ?>
							</form>
						</td>
						<!-- <td class="hide">
							<a href="index.php?view=editproduct&id=<?php echo $product->id; ?>" class="btn btn-xs btn-warning"><i
									class="glyphicon glyphicon-pencil"></i></a>
						</td> -->
					</tr>

				<?php else:
					$products_in_cero++;
					?>
				<?php endif; ?>
			<?php endforeach; ?>
		</table>
		<?php if ($products_in_cero > 0) {
			echo "<p class='alert alert-warning'>Se omitieron <b>$products_in_cero productos</b> que no tienen existencias en el inventario. <a href='index.php?view=inventary'>Ir al Inventario</a></p>";
		} ?>

		<?php
	} else {
		echo "<br><p class='alert alert-danger'>No se encontro el producto</p>";
	}
	?>
	<hr><br>
<?php else:
?>
<?php endif; ?>