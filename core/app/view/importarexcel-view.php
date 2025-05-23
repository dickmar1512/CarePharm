<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><i class='fas fa-file-excel text-success'></i> Importar Productos Excel</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
				    <li class="breadcrumb-item"><a href="#">Productos-Servicos</a></li>
					  <li class="breadcrumb-item active">Importar Productos Excel</li>
				</ol>
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
 <!-- Main content -->
<section class="content">
	<div class="container-fluid" style="display: flex; justify-content: center;">
		<div class="card card-default col-md-8">
			<div class="card-header">
				<h2 class="card-title">Importar Excel</h2>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <form class="form-horizontal" method="post" id="addproduct" action="./?view=addproductxls" role="form">
              <div class="form-group">
                <label for="image" class="control-label">Seleccione Archivo Excel</label>
                <div class="col-md-12 d-flex justify-content-between align-items-center">
                  <div class="flex-grow-1 mr-3"> <!-- El input ocupará el espacio disponible -->
                    <input type="file" name="image" id="image" class="custom-file-input">
                    <label class="btn btn-success" for="image">
												<i class="fas fa-upload"></i> Seleccionar archivo
											</label>
                  </div>
                  <button type="submit" class="btn btn-primary">Agregar Producto</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>  
  </div>
</section>
<!-- Agrega este script para mostrar el nombre del archivo -->
<script>
document.querySelector('.custom-file-input').addEventListener('change', function(e) {
  var fileName = document.getElementById("image").files[0].name;
  var nextSibling = e.target.nextElementSibling;
  //nextSibling.innerText = fileName;
   nextSibling.innerHTML = `<i class="fas fa-file-excel mr-2"></i>${fileName}`;
});
</script>