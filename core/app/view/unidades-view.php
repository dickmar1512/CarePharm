<?php
$user = UserData::getById(Session::getUID());
$grades = UnidadMedidaData::getAll();
?>
<section class="content-header">
<a href='index.php?view=newunidades' class='btn btn-default pull-right'><i class='glyphicon glyphicon-plus-sign'></i> Agregar</a>
			<h1><i class="fa fa-th-list"></i> Unidades de Medida</h1>
</section>
<section class="content">
<div class="row">

	<div class="col-md-12">
	<?php if(isset($_COOKIE["gradeupdated"])):?>
			<p class="alert alert-success"><i class='glyphicon glyphicon-ok-sign'></i> La forma de pago <b><?php echo $_COOKIE["gradeupdated"]; ?></b> ha sido actualizada exitosamente.</p>
		<?php 
		setcookie("gradeupdated","",time()-18600);
		endif; ?>

	<?php if(isset($_COOKIE["gradedeleted"])):?>
			<p class="alert alert-danger"><i class='glyphicon glyphicon-minus-sign'></i> La forma de pago <b><?php echo $_COOKIE["gradedeleted"]; ?></b> ha sido eliminada exitosamente.</p>
		<?php 
		setcookie("gradedeleted","",time()-18600);
		endif; ?>
		<?php if(isset($_COOKIE["gradeadded"])):?>
			<p class="alert alert-info"><i class='glyphicon glyphicon-ok-sign'></i> La forma de pago <b><?php echo $_COOKIE["gradeadded"]; ?></b> ha sido agregada exitosamente.</p>
		<?php 
		setcookie("gradeadded","",time()-18600);
		endif; ?>
<?php if(count($grades)>0):?>

<div class="box box-solid box-primary">
                                <div class="box-header">
                                    <h3 class="box-title"></h3>
                                </div>
                                <div class="box-body table-responsive">
<table class="table table-bordered table-hover datatable">
<thead>
	<th>Nombre</th>
	<th>Sigla</th>
	<th></th>
</thead>
<?php foreach($grades as $career):?>
<tr>
	<td><b><?php echo $career->name; ?></b></td>
	<td><b><?php echo $career->sigla; ?></b></td>
	<td style="width:90px;">

		
		<a href="index.php?view=editunidades&id=<?php echo $career->id; ?>" class="btn btn-warning btn-sm"><i class="glyphicon glyphicon-pencil"></i></a>
		<a href="#" id="del-<?php echo $career->id; ?>" class="btn btn-sm btn-danger hide"><i class='glyphicon glyphicon-trash'></i></a>
<!--		<a href="index.php?view=hidecategory&id=<?php echo $career->id; ?>" class="btn btn-sm btn-default tip" title="Desactivar Categoria"><i class='glyphicon glyphicon-eye-close'></i></a> -->
<script>
	$("#del-<?php echo $career->id?>").click(function(){
		c = confirm("Seguro quieres eliminar ??");
		if(c==true){
			window.location = "index.php?view=delgrupounidades&id=<?php echo $career->id; ?>";
		}
	});
</script>
	</td>
</tr>
<?php endforeach; ?>
</table>
</div>
</div>
<?php else: // no careers ?>
<div class="jumbotron">
	<h2><i class="glyphicon glyphicon-minus-sign"></i> No hay grupo de unidades registrados </h2>
</div>
<?php endif; ?>
	</div>
</div>
</section>