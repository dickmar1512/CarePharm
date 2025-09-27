<div class="row">
	<div class="col-md-12">
		<div class="btn-group  pull-right">
			<a href="./?view=newpaquete" class="btn btn-default">Agregar Kit de Productos</a>
			<?php
				$permiso = PermisoData::get_permiso_x_key('descargar');
				if($permiso->Pee_Valor == 1)
				{
					?>
						<div class="btn-group pull-right">
			  				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
			    				<i class="fa fa-download"></i> Descargar <span class="caret"></span>
			  				</button>
			  				<ul class="dropdown-menu" role="menu">
			    				<li><a href="report/products-word.php">Word 2007 (.docx)</a></li>
			    			</ul>
						</div>
					<?php
				}
			?>
		</div>
		<h1>Lista de Paquetes</h1>
		<div class="clearfix"></div>
<?php
$page = 1;
$fecha = date('Y-m-d h:i:s');
if(isset($_GET["page"])){
	$page=$_GET["page"];
}
$limit=50;

if(isset($_GET["limit"]) && $_GET["limit"]!="" && $_GET["limit"]!=$limit)
{
	$limit=$_GET["limit"];
}

$kits = KitData::getAll();
if(count($kits)>0)
{
	if($page==1)
	{
	 $curr_kits = KitData::getAllByPage($kits[0]->idpaquete,$limit);
	}
	else
	{
	 $curr_kits = KitData::getAllByPage($kits[($page-1)*$limit]->idpaquete,$limit);
	}
 
 $npaginas = floor(count($kits)/$limit);
 $spaginas = count($kits)%$limit;

if($spaginas>0){ $npaginas++;}

	?>

<h3>Pagina <?php echo $page." de ".$npaginas; ?></h3>
<div class="btn-group pull-right">
<?php
$px=$page-1;
if($px>0):
?>
<a class="btn btn-sm btn-default" href="<?php echo "./?view=paquetes&limit=$limit&page=".($px); ?>"><i class="glyphicon glyphicon-chevron-left"></i> Atras </a>
<?php endif; ?>
<?php 
$px=$page+1;
if($px<=$npaginas):
?>
<a class="btn btn-sm btn-default" href="<?php echo "./?view=paquetes&limit=$limit&page=".($px); ?>">Adelante <i class="glyphicon glyphicon-chevron-right"></i></a>
<?php endif; ?>
</div>
<div class="clearfix"></div>
<br><table class="table table-bordered table-hover">
	<thead>
		<th>Codigo</th>
		<th>Imagen</th>
		<th>Nombre</th>
		<th>Descripción</th>
		<th>Precio S/</th>
		<th>Activo</th>
		<th></th>
	</thead>
	<?php foreach($curr_kits as $kit):?>
		<?php //print_r($kit);?>
	<tr>
		<td><?php echo $kit->barcode; ?></td>
		<td>
			<?php if($kit->imagen!=""):?>
				<img src="storage/products/<?php echo $kit->imagen;?>" style="width:64px;">
			<?php endif;?>
		</td>
		<td><?php echo $kit->nombre; ?></td>
		<td><?php echo $kit->descripcion; ?></td>
		<td>S/ <?php echo number_format($kit->precio,2,'.',','); ?></td>
		<td><?php if($kit->estado): ?><i class="fa fa-check"></i><?php endif;?></td>
		

		<td style="width:70px;">
		<a href="./?view=editkit&id=<?php echo $kit->idpaquete; ?>" class="btn btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a>
		<?php if($kit->estado==1):?>
		<a href="./?view=delkit&id=<?php echo $kit->idpaquete; ?>&est=0&fecha=<?=$fecha?>" class="btn btn-xs btn-danger"><i class="fa fa-ban" placeholder="Desactivar"></i></a>
		<?php else:?>
		<a href="./?view=delkit&id=<?php echo $kit->idpaquete; ?>&est=1&fecha=" class="btn btn-xs btn-success"><i class="fa fa-check-circle"></i></a>	
		<?php endif;?>	
		</td>
	</tr>
	<?php endforeach;?>
</table>
<div class="btn-group pull-right">
<?php

for($i=0;$i<$npaginas;$i++){
	echo "<a href='./?view=paquetes&limit=$limit&page=".($i+1)."' class='btn btn-default btn-sm'>".($i+1)."</a> ";
}
?>
</div>
<form class="form-inline">
	<label for="limit">Limite</label>
	<input type="hidden" name="view" value="products">
	<input type="number" value=<?php echo $limit?> name="limit" style="width:60px;" class="form-control">
</form>

<div class="clearfix"></div>

	<?php
}else{
	?>
	<div class="jumbotron">
		<h2>No hay Paquetes</h2>
		<p>No se han agregado Paquetes a la base de datos, puedes agregar uno dando click en el boton <b>"Agregar Kit de Productos"</b>.</p>
	</div>
	<?php
}

?>
<br><br><br><br><br><br><br><br><br><br>
	</div>
</div>