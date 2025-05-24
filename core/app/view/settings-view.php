<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><i class="fa fa-gear"></i>  Configuracion</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Administración</a></li>
					<li class="breadcrumb-item active">Configuración</li>
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
				<div class="row">
					<div class="col-md-6">
						<h4>Datos de Empresa</h4>
					</div>
				</div>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<?php
							$empresa = EmpresaData::getDatos();
						?>
						<form action="./?view=updateempresa" method="post" class="form-horizontal">
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label class="control-label">RUC:</label>
											<input type="number" name="ruc" value="<?php echo $empresa->Emp_Ruc ?>" class="form-control">
										</div>
									</div>									
									<div class="col-md-9">
										<div class="form-group">
											<label class="control-label">Razón Social:</label>
											<input type="text" name="razon_social" value="<?php echo $empresa->Emp_RazonSocial ?>" class="form-control">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">Descripción:</label>
											<input type="text" name="descripcion" value="<?php echo $empresa->Emp_Descripcion ?>" class="form-control">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">Dirección:</label>
											<input type="text" name="direccion" value="<?php echo $empresa->Emp_Direccion ?>" class="form-control">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label class="control-label">Teléfono/Celular:</label>
											<input type="text" name="telefono" value="<?php echo $empresa->Emp_Telefono ?>" class="form-control">
										</div>
									</div>
									<div class="col-md-5">
										<div class="form-group">
											<label class="control-label">Correo:</label>
											<input type="text" name="celular" value="<?php echo $empresa->Emp_Celular ?>" class="form-control">
										</div>
									</div>
									<div class="col-md-4">
										<label for="image" style="display: flex; justify-content: left;">Logo Empresa*</label>
										<div class="custom-file" style="display: flex; justify-content: left;">
											<input type="file" name="image" id="image" class="custom-file-input">
											<label class="btn btn-success" for="image">
												<i class="fas fa-upload"></i> Seleccionar logo
											</label>
											<span id="file-name" class="ml-2"><?php echo $empresa && $empresa->Emp_Logo? $empresa->Emp_Logo : '';?></span>
											<?php echo $empresa && $empresa->Emp_Logo ? '<br><img src="storage/products/'.$empresa->Emp_Logo.'" class="img-responsive" style="max-width: 100px; margin-top: 10px;">' : ''?>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label class="control-label">Codigo de envío:</label>
											<input type="text" name="personaId" value="<?php echo $empresa->Emp_personaId ?>" class="form-control">
										</div>
									</div>
									<div class="col-md-8">
										<div class="form-group">
											<label class="control-label">Token de envío:</label>
											<input type="text" name="personaToken" value="<?php echo $empresa->Emp_personaToken ?>" class="form-control">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-10 text-center">
								<button type="submit" class="btn btn-success">Actualizar</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>				
