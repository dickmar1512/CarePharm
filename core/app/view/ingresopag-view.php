<meta charset="UTF-8">
<div class="row">
	<div class="col-md-8">
	<h1>
		Ingresar los pagos a realizar<br>
		<small>(Todo pago ingresado restará al ingreso bruto del mes elegido)</small>
	</h1>
	<br>
	<form class="form-horizontal" method="post" id="addIng" enctype="multipart/form-data" action="index.php?view=addIng" role="form">
    <div class="form-group">
	    <label for="inputEmail1" class="col-lg-3 control-label">Ingresos del mes de:</label>
		        <div class="col-md-3">
		              <select name="idmes" class="form-control">
		                    <option value="">::Seleccione::</option>
		                    <option value="202101">Enero 2021</option>
		                    <option value="202102">Febrero 2021</option>
		                    <option value="202103">Marzo 2021</option>
		                    <option value="202104">Abril 2021</option>
		                    <option value="202105">Mayo 2021</option>
		                    <option value="202106">Junio 2021</option>
		                    <option value="202107">Julio 2021</option>
		                    <option value="202108">Agosto 2021</option>
		                    <option value="202109">Setiembre 2021</option>
		                    <option value="202110">Octubre 2021</option>
		                    <option value="202111">Noviembre 2021</option>
		                    <option value="202112">Diciembre 2021</option>
		              </select>
		        </div>   
	    <div class="col-md-3">
	      <button type="submit" class="btn btn-success">Buscar Ingresos</button>
	    </div>
	  </div>
</form>
<br>
</div>
</div>
<!---------------Aqui SE MUESTRA EN PRIMER LUGAR LOS INGRESOS BRUTOS----------------->
<div class="row">
  <div class="col-md-12">
 <!--Desde aqui -->   
   <div class="col-md-10">
    <h3></h3>
    <p><b>Agregar descripcion é Importe del Gasto:</b></p>
      <form id="searchp" accept-charset="utf-8">
        <div class="row">
          <div class="form-group">
	                    <label for="inputEmail1" class="col-lg-1 control-label">Descripcion:</label>
	                    <div class="col-md-5">
	                      <input type="text" name="descripcion" id="descripcion" class="form-control">
	                    </div>
	                    <label for="inputEmail1" class="col-lg-1 control-label">Importe S/:</label>
	                    <div class="col-md-2">
	                      <input type="text" name="importe" id="importe" class="form-control">
	                    </div>                      
                      <div class="col-md-2">
                      <button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Registrar Gasto</button>
                      </div>
	      </div>
        </div>
      </form>
    </div>
    <div id="show_search_results"></div>

<div class="clearfix"></div>
<!--p class="alert alert-info">La informacion del Kit se ha actualizado exitosamente.</p-->
    <script>
//jQuery.noConflict();

$(document).ready(function(){
  $("#searchp").on("submit",function(e){
    e.preventDefault();
    
    $.get("./?action=searchproduct3",$("#searchp").serialize(),function(data){
      $("#show_search_results").html(data);
    });
    $("#descripcion").val("");
    $("#importe").val("");
  });
});

$(document).ready(function(){
    $("#descripcion").keydown(function(e){
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
$detkit = Det_kit::getById(1);
if(count($detkit)>0){
  ?>
<div class="clearfix"></div>
<br><table class="table table-bordered table-hover">
  <thead>
    <th>#</th>
    <th>Descripcion</th>
    <th>Importe S/</th>
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