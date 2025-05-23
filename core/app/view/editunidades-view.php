<?php $user = UnidadMedidaData::getById($_GET["id"]);?>
<div class="row">
	<div class="col-md-12">
	<h1>Editar Unidad Medida</h1>
	<br>
		<form class="form-horizontal" method="post" id="addgrupounidades" action="index.php?view=updateunidades" role="form">
      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
        <div class="col-md-6">
          <input type="text" name="name" value="<?php echo $user->name;?>" class="form-control" id="name" placeholder="Nombre" required="">
        </div>
      </div>
      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Sigla*</label>
        <div class="col-md-6">
          <input type="text" name="sigla" value="<?php echo $user->sigla;?>" class="form-control" id="sigla" placeholder="Siglas" required="">
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
        <input type="hidden" name="id" value="<?php echo $user->id;?>">
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </div>
    </form>
	</div>
</div>