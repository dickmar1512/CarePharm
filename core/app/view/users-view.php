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
										<thead>
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
										foreach ($users as $user) {
											?>
											<tr>
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
												<td>
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
<script>
	document.getElementById('newuser').addEventListener('click', function () {
		Swal.fire({
			title: 'Agregar Usuario',
			html:
				'<form id="adduserForm">' +
				'<div class="form-group">' +
				'  <label for="name" style="display: flex; justify-content: left;">Nombre*</label>' +
				'  <input type="text" name="name" class="form-control" id="name" placeholder="Nombre" required>' +
				'</div>' +
				'<div class="form-group">' +
				'  <label for="lastname" style="display: flex; justify-content: left;">Apellido*</label>' +
				'  <input type="text" name="lastname" class="form-control" id="lastname" placeholder="Apellido" required>' +
				'</div>' +
				'<div class="form-group">' +
				'  <label for="username" style="display: flex; justify-content: left;">Nombre de usuario*</label>' +
				'  <input type="text" name="username" class="form-control" id="username" placeholder="Nombre de usuario" required>' +
				'</div>' +
				'<div class="form-group">' +
				'  <label for="email" style="display: flex; justify-content: left;">Email*</label>' +
				'  <input type="email" name="email" class="form-control" id="email" placeholder="Email" required>' +
				'</div>' +
				'<div class="form-group">' +
				'  <label for="password" style="display: flex; justify-content: left;">Contraseña</label>' +
				'  <input type="password" name="password" class="form-control" id="password" placeholder="Contraseña">' +
				'</div>' +
				'<div class="form-group">' +
				'  <div class="checkbox">' +
				'    <label>' +
				'      <input type="checkbox" name="is_admin"> Es administrador' +
				'    </label>' +
				'  </div>' +
				'</div>' +
				'<p class="alert alert-info">* Campos obligatorios</p>' +
				'</form>',
			focusConfirm: false,
			showCancelButton: true,
			confirmButtonText: 'Agregar Usuario',
			cancelButtonText: 'Cancelar',
			preConfirm: () => {
				const form = document.getElementById('adduserForm');
				const formData = new FormData(form);
				const data = Object.fromEntries(formData.entries());

				// Validación básica
				if (!data.name || !data.lastname || !data.username || !data.email) {
					Swal.showValidationMessage('Por favor, completa todos los campos obligatorios');
					return false;
				}

				return data;
			}
		}).then((result) => {
			if (result.isConfirmed) {
				// Aquí puedes manejar los datos del formulario
				fetch('./?action=adduser', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(result.value),
				})
					.then(response => response.json())
					.then(response => {
						if (response.success) {
						Swal.fire('Éxito!', 'El usuario ha sido agregado correctamente.', 'success')
							.then(() => window.location = './?view=users'); // Redirigir tras éxito
						} else {
							Swal.fire('Error!', 'Hubo un problema al agregar el usuario.', 'error');
						}
					})
					.catch(error => {
						console.error('Error al agregar el usuario:', error);
						Swal.fire('Error', 'Hubo un problema al agregar el usuario2.', 'error');
					});
			}
		});
	});

	document.addEventListener('DOMContentLoaded', function () {
		// Selecciona todos los enlaces de edición
		const editButtons = document.querySelectorAll('.edit-user');

		editButtons.forEach(button => {
			button.addEventListener('click', function (e) {
				e.preventDefault(); // Evita que el enlace redirija

				const userId = this.getAttribute('data-id'); // Obtiene el ID del usuario

				// Hacer una solicitud AJAX para obtener los datos del usuario
				fetch(`./?action=edituser&id=${userId}`)
					.then(response => response.json())
					.then(user => {
						// Mostrar el modal de SweetAlert2 con los datos del usuario
						showEditUserModal(user);
					})
					.catch(error => {
						console.error('Error al obtener los datos del usuario:', error);
						Swal.fire('Error', 'No se pudieron cargar los datos del usuario.', 'error');
					});
			});
		});
	});

	function showEditUserModal(user) {
		Swal.fire({
			title: 'Editar Usuario',
			html:
				`<form id="edituserForm">
					<div class="form-group">
						<label for="name">Nombre*</label>
						<input type="text" name="name" class="form-control" id="name" placeholder="Nombre" value="${user.name}" required>
					</div>
					<div class="form-group">
						<label for="lastname">Apellido*</label>
						<input type="text" name="lastname" class="form-control" id="lastname" placeholder="Apellido" value="${user.lastname}" required>
					</div>
					<div class="form-group">
						<label for="username">Nombre de usuario*</label>
						<input type="text" name="username" class="form-control" id="username" placeholder="Nombre de usuario" value="${user.username}" required>
					</div>
					<div class="form-group">
						<label for="email">Email*</label>
						<input type="email" name="email" class="form-control" id="email" placeholder="Email" value="${user.email}" required>
					</div>
					<div class="form-group">
						<label for="password">Contraseña</label>
						<input type="password" name="password" class="form-control" id="password" placeholder="Contraseña">
						<p class="help-block">La contraseña solo se modificará si escribes algo, en caso contrario no se modifica.</p>
					</div>
					<div class="form-group">
						<label for="email">Monto maximo efectivo en caja</label>
						<input type="number" name="montomax" class="form-control" id="montomax" placeholder="Monto Maximo" value="${user.montomax}">
					</div>
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="is_active" ${user.is_active ? 'checked' : ''}> Está activo
							</label>
						</div>
					</div>
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="is_admin" ${user.is_admin == 1 ? 'checked' : ''}> Es usuario administrador
							</label>
						</div>
					</div>
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="is_dirtec" ${user.is_dirtec == 1 ? 'checked' : ''}> Es Usuario dirección técnica
							</label>
						</div>
					</div>
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="is_caja" ${user.is_caja == 1 ? 'checked' : ''}> Es usuario caja
							</label>
						</div>
					</div>
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="is_desc" ${user.is_desc ? 'checked' : ''}> Descuentos
							</label>
						</div>
					</div>
					<p class="alert alert-info">* Campos obligatorios</p>
					<input type="hidden" name="user_id" value="${user.id}">
				</form>`,
			focusConfirm: false,
			showCancelButton: true,
			confirmButtonText: 'Actualizar Usuario',
			cancelButtonText: 'Cancelar',
			preConfirm: () => {
				const form = document.getElementById('edituserForm');
				const formData = new FormData(form);
				const data = Object.fromEntries(formData.entries());

				// Validación básica
				if (!data.name || !data.lastname || !data.username || !data.email) {
					Swal.showValidationMessage('Por favor, completa todos los campos obligatorios');
					return false;
				}

				return data;
			}
		}).then((result) => {
			if (result.isConfirmed) {
				// Enviar los datos actualizados al servidor
				fetch('./?action=updateuser', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(result.value),
				})
				.then(response => response.json())
				.then(response => {
					if (response.success) {
						Swal.fire('Éxito', 'El usuario ha sido actualizado correctamente.', 'success')
						.then(() => window.location = './?view=users'); // Redirigir tras éxito;
					} else {
						Swal.fire('Error', 'Hubo un problema al actualizar el usuario.', 'error');
					}
				})
				.catch(error => {
					console.error('Error al actualizar el usuario:', error);
					Swal.fire('Error', 'Hubo un problema al actualizar el usuario.', 'error');
				});
			}
		});
	}
</script>