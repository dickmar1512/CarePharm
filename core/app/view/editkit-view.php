<meta charset="UTF-8">
<?php
$kit = KitData::getById($_GET["id"]);

if($kit!=null):
?>
<div class="row">
	<div class="col-md-8">
	<h1><?php echo $kit->nombre;?> <small>Editar Kit de Productos</small></h1>
  <?php if(isset($_COOKIE["kitupd"])):?>
    <p class="alert alert-info">La informacion del Kit se ha actualizado exitosamente.</p>
  <?php setcookie("kitupd","",time()-18600); endif; ?>
	<br><br>
		<form class="form-horizontal" method="post" id="addkitt" enctype="multipart/form-data" action="index.php?view=updatekit" role="form">

  <div class="form-group">
    <label for="inputEmail1" class="col-lg-3 control-label">Imagen*</label>
    <div class="col-md-8">
      <input type="file" name="image" id="name" placeholder="">
       <?php if($kit->imagen!=""):?>
       <br>
        <img src="storage/products/<?php echo $kit->image;?>" class="img-responsive">
<?php endif;?>
    </div>
  </div>

  <div class="form-group">
    <label for="inputEmail1" class="col-lg-3 control-label">Codigo de barras*</label>
    <div class="col-md-8">
      <input type="text" name="barcode" class="form-control" id="barcode" value="<?php echo $kit->barcode; ?>" placeholder="Codigo de barras del Producto">
    </div>
  </div>
    <div class="form-group">
    <label for="inputEmail1" class="col-lg-3 control-label">Nombre*</label>
    <div class="col-md-8">
      <input type="text" name="name" class="form-control" id="name" value="<?php echo $kit->nombre; ?>" placeholder="Nombre del Producto">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-3 control-label">Descripcion</label>
    <div class="col-md-8">
      <textarea name="description" class="form-control" id="description" placeholder="Descripcion del Producto"><?php echo $kit->descripcion;?></textarea>
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-3 control-label">Precio de Salida*</label>
    <div class="col-md-8">
      <input type="text" name="price_out" class="form-control" id="price_out" value="<?php echo $kit->precio; ?>" placeholder="Precio de salida" autofocus>
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-3 control-label" >Esta activo</label>
    <div class="col-md-8">
<div class="checkbox">
    <label>
      <input type="checkbox" name="is_active" <?php if($kit->estado){ echo "checked";}?>> 
    </label>
  </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-lg-offset-3 col-lg-8">
    <input type="hidden" name="kit_id" value="<?php echo $kit->idpaquete; ?>">
      <button type="submit" class="btn btn-success">Actualizar Producto</button>
    </div>
  </div>
</form>
<br><br>
	</div>
</div>
<?php endif; ?>
<!---------------Aqui van los productos del paquete o kit----------------->
<div class="row">
  <div class="col-md-12">
 <!--Desde aqui formulario de busqueda de producto para agregar al kit-->   
   <div class="col-md-8">
    <h3>Lista de Productos del Kit</h3>
    <p><b>Buscar producto por nombre o por codigo para agregar a la lista:</b></p>
      <form id="searchp" accept-charset="utf-8">
        <div class="row">
          <div class="col-md-8">
            <input type="hidden" name="view" value="sell">
            <input type="hidden" name="idpaquete" value="<?=$kit->idpaquete?>">
            <input type="text" id="product_code2" autofocus name="product" class="form-control">
          </div>
          <div class="col-md-3">
          <button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Buscar</button>
          </div>
        </div>
      </form>
    </div>
    <div id="show_search_results"></div>

<div class="clearfix"></div>
    <script>
//jQuery.noConflict();

$(document).ready(function(){
  $("#searchp").on("submit",function(e){
    e.preventDefault();
    
    $.get("./?action=searchproduct3",$("#searchp").serialize(),function(data){
      $("#show_search_results").html(data);
    });
    $("#product_code2").val("");
  });
});

$(document).ready(function(){
    $("#product_code").keydown(function(e){
        if(e.which==17 || e.which==74){
            e.preventDefault();
        }else{
            console.log(e.which);
        }
    })
});
</script>
<!--hasta aqui-->
<?php
$detkit = Det_kit::getById($_GET["id"]);
//var_dump($detkit);
if(count($detkit)>0){
  ?>
<div class="clearfix"></div>
<br><table class="table table-bordered table-hover">
  <thead>
    <th>Codigo</th>
    <th>Cantidad</th>
    <th>Nombre</th>
    <th>Precio S/</th>
    <th>Descuento S/</th>
  </thead>
  <?php foreach($detkit as $detalle): ?>
  <tr>
    <td><?php echo $detalle->barcode; ?></td>
    <td><?php echo $detalle->cantidad?></td>
    <td><?php echo $detalle->name; ?></td>
    <td>S/ <?php echo number_format($detalle->precio,2,'.',','); ?></td>
    <td>S/ <?php echo number_format($detalle->descuento,2,'.',','); ?></td>
    <td style="width:70px;">
    <a href="index.php?view=deldetkit&id=<?=$kit->idpaquete?>&iddet=<?=$detalle->iddetalle?>" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>
    </td>
  </tr>
  <?php endforeach;?>
</table>
<div class="clearfix"></div>

  <?php
}else{
  ?>
  <div class="jumbotron">
    <h2>No hay productos en el Paquete</h2>
    <p>No se han agregado productos al Paquete en la base de datos, puedes agregar uno dando click en buscar y luego en el boton <b>"Agregar"</b>.</p>
  </div>
  <?php
}
?>
<br><br>
  </div>
</div>