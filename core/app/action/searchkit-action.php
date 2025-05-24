<?php 
header("Content-Type: text/html;charset=utf-8");
  //$idpaquete = $_GET["idpaquete"];
if(isset($_GET["kit"]) && $_GET["kit"]!=""):?>
	<?php
$kits = KitData::getLike(htmlspecialchars($_GET["kit"],ENT_NOQUOTES,"UTF-8"));
if(count($kits)>0){
	?>
		<h3>Resultados de la Busqueda</h3>
<table class="table table-bordered table-hover">
	<thead>
		<th>Codigo</th>
		<th>Nombre</th>
		<th>Descripción</th>
		<th>
		<div class="label-group">
			<label style="width: 100px;">P.Uni. S/</label>
		</div>
		</th>
	</thead>
	<?php
	$kits_in_cero=0;
	foreach($kits as $kit):
	?>
	<tr>
		<td style="width:80px;  font-size: 18px;"><?php echo $kit->barcode; ?></td>
		<td style=" font-size: 18px;"><?php echo $kit->nombre; ?></td>
		<td style=" font-size: 18px;"><?php echo $kit->descripcion; ?></td>				
			<td>
			<form method="post" action="./?view=addtocartkit">
				<input type="hidden" name="idpaquete" value="<?php echo $kit->idpaquete; ?>">
				<div class="input-group">				
				<input type="number" step="any" class="form-control" required name="preciokit" placeholder="Precio Unitario" value="<?php echo $kit->precio?>" 
				style="width: 100px; font-size: 20px;" min="0">				
				<button type="submit" class="btn btn-primary">
					<i class="glyphicon glyphicon-plus-sign"></i> Agregar
				</button>
      			</div>
			</form>
			</td>
	</tr>
	
<?php $kits_in_cero++;
 endforeach;
}
endif;
?>
</table>
<?php if($kits_in_cero>0)
{ 
	echo "<p class='alert alert-warning'>Se omitieron <b>$products_in_cero productos</b> que no tienen existencias en el inventario. <a href='./?view=inventary'>Ir al Inventario</a></p>"; 
}
else
{
	echo "<br><p class='alert alert-danger'>No se encontro el Kit</p>";
}
?>
<hr>