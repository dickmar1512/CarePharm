<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><i class='fas fa-user-secret'></i> Usuarios</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
				    <li class="breadcrumb-item"><a href="#">Administración</a></li>
					  <li class="breadcrumb-item active">Usuarios</li>
				</ol>
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<!-- Main content -->
<section class="content">
	<div class="container-fluid">
		<div class="card card-default">
			<div class="card-header">				
				<h3 class="card-title"><i class="fas fa-users mr-2"></i>Lista de Usuarios</h3>				
				<div class="btn-group float-sm-right">
					<button id="newuser" class="btn btn-primary">
						<i class='fas fa-user-plus mr-2'></i> Nuevo Usuario
					</button>
				</div>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="row" style="display: flex; justify-content: center;">
					<div class="col-md-12">
						<div class="row" style="display: flex; justify-content: center;">
							<div class="col-md-12">
								<?php
								$users = UserData::getAll();
								if (count($users) > 0) {
									// si hay usuarios
									?>
									<table class="table table-bordered table-hover">
										<thead class="thead-dark">
											<th style="width: 10px;">#</th>
											<th>Nombre</th>
											<th>usuario</th>
											<th>Email</th>
											<th>Activo</th>
											<th>Rol Admin</th>
											<th>Rol D.T</th>
											<th>Rol Caja</th>
											<th>Descuentos</th>
											<th>Monto Max.</th>
											<th></th>
										</thead>
										<?php
										$cont = 0;
										foreach ($users as $user) {
											$cont++;
											?>
											<tr>
												<td><?php echo $cont; ?></td>
												<td><?php echo $user->name . " " . $user->lastname; ?></td>
												<td><?php echo $user->username; ?></td>
												<td><?php echo $user->email; ?></td>
												<td style="width:30px; text-align: center;">
													<?php if ($user->is_active): ?>
														<i class="fas fa-check text-success"></i>
													<?php else: ?>
														<i class="fas fa-ban text-warning"></i>
													<?php endif; ?>
												</td>
												<td style="width:30px; text-align: center;">
													<?php if ($user->is_admin): ?>
														<i class="fas fa-check text-success"></i>
													<?php else: ?>
														<i class="fas fa-ban text-warning"></i>
													<?php endif; ?>
												</td>
												<td style="width:30px; text-align: center;">
													<?php if ($user->is_dirtec): ?>
														<i class="fas fa-check text-success"></i>
													<?php else: ?>
														<i class="fas fa-ban text-warning"></i>
													<?php endif; ?>
												</td>
												<td style="width:30px; text-align: center;">
													<?php if ($user->is_caja): ?>
														<i class="fas fa-check text-success"></i>
													<?php else: ?>
														<i class="fas fa-ban text-warning"></i>
													<?php endif; ?>
												</td>
												<td style="width:30px; text-align: center;">
													<?php if ($user->is_desc): ?>
														<i class="fas fa-check text-success"></i>
													<?php else: ?>
														<i class="fas fa-ban text-warning"></i>
													<?php endif; ?>
												</td>
												<td style="width:100px; text-align: center;">
													<?php if ($user->montomax == 0): ?>
														<i class="fas fa-ban text-warning"></i>
													<?php else: ?>
														<i class="fas fa-check text-success"></i>
													<?php endif; ?>													
													<?php echo $user->montomax; ?>
												</td>
												<td style="width:30px;">
													<a href="#" class="btn btn-warning btn-xs edit-user" data-id="<?php echo $user->id; ?>">Editar</a>
												</td>
											</tr>
											<?php

										} ?>
									</table>


								<?php } else {

								}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>	
<!-- <script>
</script> -->