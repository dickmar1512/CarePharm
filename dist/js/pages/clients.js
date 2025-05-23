//ventanas modales
	$('#openModalNuevoCliente').on('click', function () {
		Swal.fire({
			title: 'Nuevo Cliente',
			html: `     <hr>
							<form id="addClientForm">
								<div class="row text-center">
                                    <div class="row col-md-6">
                                        <div class="col-md-6">
                                            <div class="icheck-primary d-inline">
                                                <input type="radio" id="optTipoPersona1" name="optTipoPersona" checked value="3">
                                                <label for="optTipoPersona1"> Natural</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="icheck-success d-inline">    
                                                <input type="radio" id="optTipoPersona2" name="optTipoPersona" value="1"> 
                                                <label for="optTipoPersona2">Jurídica</label>
                                            </div>									
                                            <input type="hidden" id="tipodoc" name="tipodoc" value="1">
                                        </div>
                                    </div>    
								</div>
                                <hr>
								<div id="natural">
									<div class="form-group" id="natural">
										<label for="dni" style="display: flex; justify-content: left;">DNI*</label>
										<div class="col-md-4">
											<input type="text" name="dni" class="form-control" id="dni" placeholder="DNI" required="">
										</div>											
									</div>
									<div class="form-group">
										<label for="name" style="display: flex; justify-content: left;">Nombre*</label>
										<input type="text" name="name" class="form-control" id="name" placeholder="Nombre" required="">
									</div>
									<div class="form-group">
										<label for="lastname" style="display: flex; justify-content: left;">Apellido*</label>
										<input type="text" name="lastname" required class="form-control" id="lastname" placeholder="Apellido" required="">
									</div>
								</div>
								<div id="juridica" style="display: none;">
									<div class="form-group">
										<label for="ruc" style="display: flex; justify-content: left;">RUC*</label>
										<div class="col-md-4">
										 <input type="text" name="ruc" class="form-control" id="ruc" placeholder="RUC">
										 </div>
									</div>
									<div class="form-group">
										<label for="razon_social" style="display: flex; justify-content: left;">Razón Social*</label>
										<input type="text" name="razon_social" class="form-control" id="razon_social" placeholder="Razón Social">
									</div>
								</div>
								<div class="form-group">
									<label for="address1" style="display: flex; justify-content: left;">Direccion*</label>
									<input type="text" name="address1" class="form-control" required id="address1" placeholder="Direccion">
								</div>
								<div class="form-group">
									<label for="email1" style="display: flex; justify-content: left;">Email</label>
									<input type="text" name="email1" class="form-control" id="email1" placeholder="Email">
								</div>
								<div class="form-group">
									<label for="phone1" style="display: flex; justify-content: left;">Telefono</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="text" name="phone1" class="form-control" id="phone1" placeholder="Telefono">
                                        </div>
                                        <div class="col-md-6">    								
                                            <p class="alert alert-info">* Campos obligatorios</p>
                                        </div> 
                                    </div>         
								</div> 
							</form>
				`,
			focusConfirm: false,
			showCancelButton: true,
			confirmButtonText: 'Agregar Cliente',
			cancelButtonText: 'Cancelar',
			customClass: {
				container: 'custom-swal-container',
				popup: 'custom-swal-popup',
				header: 'custom-swal-header',
				title: 'custom-swal-title',
				content: 'custom-swal-content',
				closeButton: 'custom-swal-close-button'
			},
			width: '40%', // Ajusta el ancho del modal
			didOpen: () => {
				// Script para manejar el cambio entre "Natural" y "Jurídica"
				$("input[name=optTipoPersona]").click(function () {
					var optTipoPersona = $('input:radio[name=optTipoPersona]:checked').val();

					if (optTipoPersona == 3) {
						$("#natural").show("slow");
						$("#juridica").hide("slow");

						$("#dni").attr('required', '');
						$("#name").attr('required', '');
						$("#lastname").attr('required', '');
						$("#ruc").attr('required', false);
						$("#razon_social").attr('required', false);
						$("#tipodoc").val(1);
					} else if (optTipoPersona == 1) {
						$("#natural").hide("slow");
						$("#juridica").show("slow");

						$("#dni").attr('required', false);
						$("#name").attr('required', false);
						$("#lastname").attr('required', false);
						$("#ruc").attr('required', '');
						$("#razon_social").attr('required', '');
						$("#tipodoc").val(6);
					}
				});
			},
			preConfirm: () => {
				const form = document.getElementById('addClientForm');
				const formData = new FormData(form);
				const data = Object.fromEntries(formData.entries());

				// Validación básica
				if (data.tipodoc == 1 & (!data.dni || !data.name || !data.lastname || !data.address1)) {
					Swal.showValidationMessage('Por favor, completa todos los campos obligatorios');
					return false;
				} else if (data.tipodoc == 6 & (!data.ruc || !data.razon_social || !data.address1)) {
					Swal.showValidationMessage('Por favor, completa todos los campos obligatorios');
					return false;
				}
				return data;
			}
		}).then((result) => {
			if (result.isConfirmed) {
				// Aquí puedes manejar los datos del formulario
				fetch('./?action=addclient', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(result.value),
				})
					.then(response => response.json())
					.then(response => {
						if (response.success) {
							Swal.fire('Éxito!', 'El el cliente ha sido agregado correctamente.', 'success')
								.then(() => window.location = 'index.php?view=clients'); // Redirigir tras éxito
						} else {
							Swal.fire('Error!', 'Hubo un problema al agregar el cliente.', 'error');
						}
					})
					.catch(error => {
						console.error('Error al agregar el cliente:', error);
						Swal.fire('Error', 'Hubo un problema al agregar el cliente.', 'error');
					});
			}
		});
	});

	document.addEventListener('DOMContentLoaded', function () {
		// Selecciona todos los enlaces de edición
		const editButtons = document.querySelectorAll('.edit-client');
		// Selecciona todos los botones de eliminar
		const deleteButtons = document.querySelectorAll('.delete-client');

		editButtons.forEach(button => {
			button.addEventListener('click', function (e) {
				e.preventDefault(); // Evita que el enlace redirija

				const clientId = this.getAttribute('data-id'); // Obtiene el ID del usuario

				// Hacer una solicitud AJAX para obtener los datos del usuario
				fetch(`./?action=editclient&id=${clientId}`)
					.then(response => response.json())
					.then(client => {
						// Mostrar el modal de SweetAlert2 con los datos del usuario
						showEditClientModal(client);
					})
					.catch(error => {
						console.error('Error al obtener los datos del cliente:', error);
						Swal.fire('Error', 'No se pudieron cargar los datos del cliente.', 'error');
					});
			});
		});

		deleteButtons.forEach(button => {
			button.addEventListener('click', function (e) {
				e.preventDefault(); // Evita que el enlace redirija

				const arrDato = this.getAttribute('data-id').split('|'); // Obtiene los datos en forma de array
				const clientId = arrDato[0]; // Obtiene el ID del cliente
				const accion = arrDato[1]; // Obtiene la accion a realizar
				const texto = (accion=='D') ? "¡Desea desactivar este Cliente!" : "¡Desea activar este Cliente!";
				const confirText = (accion=='D') ? "Sí, Desactivar" : "Sí, Activar";
				const estText1 = (accion=='D') ? "¡Desactivado!" : "¡Activado!";
				const estText2 = (accion=='D') ? "El cliente ha sido desactivado." : "El cliente ha sido activado.";
				const errorText = (accion=='D') ? "desactivar el cliente." : "activar el cliente.";

				// Mostrar el cuadro de diálogo de SweetAlert2 para confirmar la eliminación
				Swal.fire({
					title: '¿Estás seguro?',
					text: texto,
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: confirText,
					cancelButtonText: 'Cancelar'
				}).then((result) => {
					if (result.isConfirmed) {
						// Si el usuario confirma, hacer la solicitud AJAX para eliminar
						fetch(`./?action=delclient&id=${clientId}&accion=${accion}`, {
							method: 'PUT', // Método HTTP PUT
							headers: {
								'Content-Type': 'application/json'
							}
						})
							.then(response => response.json())
							.then(data => {
								if (data.success) {
									Swal.fire(estText1, estText2, 'success')
										.then(() => window.location = 'index.php?view=clients');// Recargar la página o actualizar la lista de clientes
								} else {
									Swal.fire('Error', 'No se pudo '+errorText, 'error');
								}
							})
							.catch(error => {
								Swal.fire('Error','Hubo un problema al '+errorText,'error');
							});
					}
				});
			});
		});
	});

	function showEditClientModal(client) {
		Swal.fire({
			title: 'Editar Cliente',
			html:`      <hr>
				           <form id="updateClientForm">
                                <div class="row text-center">
                                    <div class="row col-md-6">
                                        <div class="col-md-6">
                                            <div class="icheck-primary d-inline">
                                                <input type="radio" id="optTipoPersona1" name="optTipoPersona" ${(client.tipo_persona == 3) ? 'checked' : 'disabled'} value="3">
                                                <label for="optTipoPersona1"> Natural</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="icheck-success d-inline">    
                                                <input type="radio" id="optTipoPersona2" name="optTipoPersona" ${(client.tipo_persona == 1) ? 'checked' : 'disabled'} value="1"> 
                                                <label for="optTipoPersona2">Jurídica</label>
                                            </div>									
                                            <input type="hidden" id="tipodoc" name="tipodoc" value="1">
                                        </div>
                                    </div>    
								</div>
                                <hr>
								<div id="natural">
									<div class="form-group" id="natural">
										<label for="dni" style="display: flex; justify-content: left;">DNI*</label>
										<div class="col-md-4">
											<input type="text" name="dni" class="form-control" id="dni" placeholder="DNI" required="">
										</div>											
									</div>
									<div class="form-group">
										<label for="name" style="display: flex; justify-content: left;">Nombre*</label>
										<input type="text" name="name" class="form-control" id="name" placeholder="Nombre" required="">
									</div>
									<div class="form-group">
										<label for="lastname" style="display: flex; justify-content: left;">Apellido*</label>
										<input type="text" name="lastname" required class="form-control" id="lastname" placeholder="Apellido" required="">
									</div>
								</div>
								<div id="juridica" style="display: none;">
									<div class="form-group">
										<label for="ruc" style="display: flex; justify-content: left;">RUC*</label>
										<div class="col-md-4">
										 <input type="text" name="ruc" class="form-control" id="ruc" placeholder="RUC">
										 </div>
									</div>
									<div class="form-group">
										<label for="razon_social" style="display: flex; justify-content: left;">Razón Social*</label>
										<input type="text" name="razon_social" class="form-control" id="razon_social" placeholder="Razón Social">
									</div>
								</div>
								<div class="form-group">
									<label for="address1" style="display: flex; justify-content: left;">Direccion*</label>
									<input type="text" name="address1" class="form-control" required id="address1" placeholder="Direccion">
								</div>
								<div class="form-group">
									<label for="email1" style="display: flex; justify-content: left;">Email</label>
									<input type="text" name="email1" class="form-control" id="email1" placeholder="Email">
								</div>
								<div class="form-group">
									<label for="phone1" style="display: flex; justify-content: left;">Telefono</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="text" name="phone1" class="form-control" id="phone1" placeholder="Telefono">
                                        </div>
                                        <div class="col-md-6">									
                                            <p class="alert alert-info">* Campos obligatorios</p> 
                                            <input type="hidden" name="client_id" id="client_id" value="${client.id}">
                                        </div>
                                    </div>	 
								</div>  
							</form>`,
			focusConfirm: false,
			showCancelButton: true,
			confirmButtonText: 'Actualizar Cliente',
			cancelButtonText: 'Cancelar',
			customClass: {
				container: 'custom-swal-container',
				popup: 'custom-swal-popup',
				header: 'custom-swal-header',
				title: 'custom-swal-title',
				content: 'custom-swal-content',
				closeButton: 'custom-swal-close-button'
			},
			width: '40%', // Ajusta el ancho del modal
			didOpen: () => {
				// Script para manejar el cambio entre "Natural" y "Jurídica"
				$(document).ready(function () {
					var optTipoPersona = $('input:radio[name=optTipoPersona]:checked').val();

					if (optTipoPersona == 3) {
						$("#natural").show("slow");
						$("#juridica").hide("slow");

						$("#dni").attr('required', '');
						$("#name").attr('required', '');
						$("#lastname").attr('required', '');
						$("#dni").val(client.numero_documento);
						$("#name").val(client.name);
						$("#lastname").val(client.lastname);
						$("#address1").val(client.address1);
						$("#email1").val(client.email1);
						$("#phone1").val(client.phone1);
						$("#ruc").attr('required', false);
						$("#razon_social").attr('required', false);
						$("#tipodoc").val(1);
					} else if (optTipoPersona == 1) {
						$("#natural").hide("slow");
						$("#juridica").show("slow");

						$("#dni").attr('required', false);
						$("#name").attr('required', false);
						$("#lastname").attr('required', false);
						$("#address1").val(client.address1);
						$("#email1").val(client.email1);
						$("#phone1").val(client.phone1);
						$("#ruc").attr('required', '');
						$("#razon_social").attr('required', '');
						$("#ruc").val(client.numero_documento);
						$("#razon_social").val(client.name);
						$("#tipodoc").val(6);
					}
				});
			},
			preConfirm: () => {
				const form = document.getElementById('updateClientForm');
				const formData = new FormData(form);
				const data = Object.fromEntries(formData.entries());

				// Validación básica
				if (data.tipodoc == 1 & (!data.dni || !data.name || !data.lastname || !data.address1)) {
					Swal.showValidationMessage('Por favor, completa todos los campos obligatorios');
					return false;
				} else if (data.tipodoc == 6 & (!data.ruc || !data.razon_social || !data.address1)) {
					Swal.showValidationMessage('Por favor, completa todos los campos obligatorios');
					return false;
				}

				return data;
			}
		})
			.then((result) => {
				if (result.isConfirmed) {
					// Enviar los datos actualizados al servidor
					fetch('./?action=updateclient', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
						},
						body: JSON.stringify(result.value),
					})
						.then(response => response.json())
						.then(response => {
							if (response.success) {
								Swal.fire('Éxito', 'El cliente ha sido actualizado correctamente.', 'success')
									.then(() => window.location = 'index.php?view=clients'); // Redirigir tras éxito;
							} else {
								Swal.fire('Error', 'Hubo un problema al actualizar el cliente.', 'error');
							}
						})
						.catch(error => {
							console.error('Error al actualizar el cliente:', error);
							Swal.fire('Error', 'Hubo un problema al actualizar el cliente.', 'error');
						});
				}
			});
	}