    $('#newuser').on('click', function () {
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
                '<div class="form-group"> ' +
				'	<div class="checkbox"> ' +
				'		<label> ' + 
				'       <input type="checkbox" name="is_dirtec"> Es Usuario dirección técnica ' +
				'		</label> ' +
				'	</div> ' +
				'</div> ' +
				'<div class="form-group"> ' +
				'	<div class="checkbox"> ' +
				'		<label> ' +
				'           <input type="checkbox" name="is_caja"> Es usuario caja ' +
				'       </label> ' +
				'	</div> ' +
				'</div> '+
                '</div>' +
                '<p class="alert alert-info">* Campos obligatorios</p>' +
                '</form>',
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Agregar Usuario',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                const form = $('#adduserForm')[0];
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
                // Hacer la petición AJAX con jQuery
                $.ajax({
                    url: './?action=adduser',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(result.value),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Éxito!', 'El usuario ha sido agregado correctamente.', 'success')
                                .then(() => window.location = './?view=users'); // Redirigir tras éxito
                        } else {
                            Swal.fire('Error!', 'Hubo un problema al agregar el usuario.', 'error');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error al agregar el usuario:', error);
                        Swal.fire('Error', 'Hubo un problema al agregar el usuario.', 'error');
                    }
                });
            }
        });
    });

    $(document).ready(function () {
        // Selecciona todos los enlaces de edición
        $('.edit-user').on('click', function (e) {
            e.preventDefault(); // Evita que el enlace redirija
            
            const userId = $(this).data('id'); // Obtiene el ID del usuario
            
            // Hacer una solicitud AJAX para obtener los datos del usuario
            $.ajax({
                url: `./?action=edituser&id=${userId}`,
                type: 'GET',
                dataType: 'json',
                success: function (user) {
                    // Mostrar el modal de SweetAlert2 con los datos del usuario
                    showEditUserModal(user);
                },
                error: function (xhr, status, error) {
                    console.error('Error al obtener los datos del usuario:', error);
                    Swal.fire('Error', 'No se pudieron cargar los datos del usuario.', 'error');
                }
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