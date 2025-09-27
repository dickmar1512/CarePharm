 // Botón para agregar nuevo cliente
$('#openModalNuevoCliente').on('click', function() {
    showClientModal();
});

// Evento para abrir el modal de edición de cliente
$('.edit-client').on('click', function () {
	const clientId = this.getAttribute('data-id');
            
    fetch(`./?action=editclient&id=${clientId}`)
        .then(response => response.json())
        .then(client => showClientModal(client))
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudieron cargar los datos del cliente.', 'error');
    });
});

//Evento para activar o desactivar clientes
$('.delete-client').on('click', function () {
	const arrDato = this.getAttribute('data-id').split('|');
    const clientId = arrDato[0];
    const action = arrDato[1];
            
    handleClientStatus(clientId, action);
});

// Función unificada para mostrar modal de cliente (agregar/editar)
function showClientModal(client = null) {
    const isEditMode = client !== null;
    const title = isEditMode ? 'Editar Cliente' : 'Nuevo Cliente';
    const confirmButtonText = isEditMode ? 'Actualizar Cliente' : 'Agregar Cliente';
    
    // Configuración inicial basada en el modo
    const initialType = client?.tipo_persona || 3;
    const initialDocType = initialType === 3 ? 1 : 6;
    
    // Plantilla del formulario
    const formHtml = `
        <hr>
        <form id="clientForm">
            <div class="row text-center">
                <div class="row col-md-6">
                    <div class="col-md-6">
                        <div class="icheck-primary d-inline">
                            <input type="radio" id="optTipoPersona1" name="optTipoPersona" 
                                ${initialType == 3 ? 'checked' : ''} 
                                ${isEditMode ? 'disabled' : ''} value="3">
                            <label for="optTipoPersona1"> Natural</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="icheck-success d-inline">    
                            <input type="radio" id="optTipoPersona2" name="optTipoPersona" 
                                ${initialType == 1 ? 'checked' : ''} 
                                ${isEditMode ? 'disabled' : ''} value="1"> 
                            <label for="optTipoPersona2">Jurídica</label>
                        </div>									
                        <input type="hidden" id="tipodoc" name="tipodoc" value="${initialDocType}">
                    </div>
                </div>    
            </div>
            <hr>
            <div id="natural" style="${initialType == 1 ? 'display: none;' : ''}">
                <div class="form-group">
                    <label for="dni" style="display: flex; justify-content: left;">DNI*</label>
                    <div class="col-md-4">
                        <input type="text" name="dni" class="form-control" id="dni" 
                            placeholder="DNI" ${initialType == 3 ? 'required' : ''}
                            value="${client?.numero_documento || ''}">
                    </div>											
                </div>
                <div class="form-group">
                    <label for="name" style="display: flex; justify-content: left;">Nombre*</label>
                    <input type="text" name="name" class="form-control" id="name" 
                        placeholder="Nombre" ${initialType == 3 ? 'required' : ''}
                        value="${client?.name || ''}">
                </div>
                <div class="form-group">
                    <label for="lastname" style="display: flex; justify-content: left;">Apellido*</label>
                    <input type="text" name="lastname" class="form-control" id="lastname" 
                        placeholder="Apellido" ${initialType == 3 ? 'required' : ''}
                        value="${client?.lastname || ''}">
                </div>
            </div>
            <div id="juridica" style="${initialType == 3 ? 'display: none;' : ''}">
                <div class="form-group">
                    <label for="ruc" style="display: flex; justify-content: left;">RUC*</label>
                    <div class="col-md-4">
                        <input type="text" name="ruc" class="form-control" id="ruc" 
                            placeholder="RUC" ${initialType == 1 ? 'required' : ''}
                            value="${initialType === 1 ? client?.numero_documento : ''}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="razon_social" style="display: flex; justify-content: left;">Razón Social*</label>
                    <input type="text" name="razon_social" class="form-control" id="razon_social" 
                        placeholder="Razón Social" ${initialType == 1 ? 'required' : ''}
                        value="${initialType === 1 ? client?.name : ''}">
                </div>
            </div>
            <div class="form-group">
                <label for="address1" style="display: flex; justify-content: left;">Dirección*</label>
                <input type="text" name="address1" class="form-control" required id="address1" 
                    placeholder="Dirección" value="${client?.address1 || ''}">
            </div>
            <div class="form-group">
                <label for="email1" style="display: flex; justify-content: left;">Email</label>
                <input type="email" name="email1" class="form-control" id="email1" 
                    placeholder="Email" value="${client?.email1 || ''}">
            </div>
            <div class="form-group">
                <label for="phone1" style="display: flex; justify-content: left;">Teléfono</label>
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="phone1" class="form-control" id="phone1" 
                            placeholder="Teléfono" value="${client?.phone1 || ''}">
                    </div>
                    <div class="col-md-6">    								
                        <p class="alert alert-info">* Campos obligatorios</p>
                        ${isEditMode ? `<input type="hidden" name="client_id" id="client_id" value="${client.id}">` : ''}
                    </div> 
                </div>         
            </div> 
        </form>
    `;

    Swal.fire({
        title: title,
        html: formHtml,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        cancelButtonText: 'Cancelar',
        customClass: {
            container: 'custom-swal-container',
            popup: 'custom-swal-popup',
            header: 'custom-swal-header',
            title: 'custom-swal-title',
            content: 'custom-swal-content',
            closeButton: 'custom-swal-close-button'
        },
        width: '40%',
        didOpen: () => {
            if (!isEditMode) {
                // Solo en modo agregar permitimos cambiar el tipo de persona
                $("input[name=optTipoPersona]").click(function() {
                    const optTipoPersona = $('input:radio[name=optTipoPersona]:checked').val();
                    
                    if (optTipoPersona == 3) {
                        $("#natural").show("slow");
                        $("#juridica").hide("slow");
                        
                        $("#dni").attr('required', true);
                        $("#name").attr('required', true);
                        $("#lastname").attr('required', true);
                        $("#ruc").attr('required', false);
                        $("#razon_social").attr('required', false);
                        $("#tipodoc").val(1);
                    } else if (optTipoPersona == 1) {
                        $("#natural").hide("slow");
                        $("#juridica").show("slow");
                        
                        $("#dni").attr('required', false);
                        $("#name").attr('required', false);
                        $("#lastname").attr('required', false);
                        $("#ruc").attr('required', true);
                        $("#razon_social").attr('required', true);
                        $("#tipodoc").val(6);
                    }
                });
            }
        },
        preConfirm: () => {
            const form = document.getElementById('clientForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            // Validación básica
            if (data.tipodoc == 1 && (!data.dni || !data.name || !data.lastname || !data.address1)) {
                Swal.showValidationMessage('Por favor, completa todos los campos obligatorios para persona natural');
                return false;
            } else if (data.tipodoc == 6 && (!data.ruc || !data.razon_social || !data.address1)) {
                Swal.showValidationMessage('Por favor, completa todos los campos obligatorios para persona jurídica');
                return false;
            }
            
            return data;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const endpoint = isEditMode ? './?action=updateclient' : './?action=addclient';
            const successMessage = isEditMode ? 'El cliente ha sido actualizado correctamente' : 'El cliente ha sido agregado correctamente';
            
            fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(result.value)
            })
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    Swal.fire('Éxito!', successMessage, 'success')
                        .then(() => window.location = './?view=clients');
                } else {
                    Swal.fire('Error!', response.message || 'Hubo un problema al procesar la solicitud', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Hubo un problema al procesar la solicitud', 'error');
            });
        }
    });
}

// Función para eliminar/activar cliente
function handleClientStatus(clientId, action) {
    const texto = (action == 'D') ? "¡Desea desactivar este Cliente!" : "¡Desea activar este Cliente!";
    const confirText = (action == 'D') ? "Sí, Desactivar" : "Sí, Activar";
    const estText1 = (action == 'D') ? "¡Desactivado!" : "¡Activado!";
    const estText2 = (action == 'D') ? "El cliente ha sido desactivado." : "El cliente ha sido activado.";
    const errorText = (action == 'D') ? "desactivar el cliente." : "activar el cliente.";

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
            fetch(`./?action=delclient&id=${clientId}&accion=${action}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(estText1, estText2, 'success')
                        .then(() => window.location = './?view=clients');
                } else {
                    Swal.fire('Error', 'No se pudo ' + errorText, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Hubo un problema al ' + errorText, 'error');
            });
        }
    });
}