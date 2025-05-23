// Evento para abrir el modal de nuevo producto
$('#openModalNuevoProveedor').on('click', function () {
    openProviderModal('add');
});

// Evento para abrir el modal de edición de producto
$('.edit-provider').on('click', function () {
    const providerId = this.getAttribute('data-id');
    fetch(`./?action=getprovider&id=${providerId}`)
        .then(response => response.json())
        .then(providerData => {
            openProviderModal('edit', providerData);
        })
        .catch(error => {
            console.error('Error al obtener los datos del proveedor:', error);
        });
});

function openProviderModal(action, providerData = null) {
    Swal.fire({
        title: action === 'edit' ? 'Editar Proveedor' : 'Nuevo Proveedor',
        html: `     <hr>
                        <form id="providerForm">
                            <div class="row text-center">
                                <div class="row col-md-6">
                                    <div class="col-md-6">
                                        <div class="icheck-primary d-inline">
                                            <input type="radio" id="optTipoPersona1" name="optTipoPersona" value="3">
                                            <label for="optTipoPersona1">Natural</label>
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
                                    <label for="dni" style="display: flex; justify-content: left;">RUC*</label>
                                    <div class="col-md-4">
                                        <input type="text" name="dni" class="form-control" id="dni" placeholder="RUC" required="">
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
                                        <input type="hidden" name="provider_id" id="provider_id">
                                    </div> 
                                </div>         
                            </div> 
                        </form>
            `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: action === 'edit' ? 'Editar Proveedor' : 'Agregar Proveedor',
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
                tipoProveedor(providerData);

                $("input[name=optTipoPersona]").click(function () {
                    tipoProveedor(providerData);
                });
            });
        },
        preConfirm: () => {
            const form = document.getElementById('providerForm');
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
            const url = action === 'edit' ? './?action=updateprovider' : './?action=addprovider';
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(result.value),
            })
            .then(response => response.json())
            .then(response => { console.log("response==>",response);
                if (response.success) {
                    Swal.fire('Éxito!', `El proveedor ha sido ${action === 'edit' ? 'actualizado' : 'agregado'} correctamente.`, 'success')
                            .then(() => window.location = '?view=providers');
                } else {
                        Swal.fire('Error!', `Hubo un problema al ${action === 'edit' ? 'actualizar' : 'agregar'} el provedor.`, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', `Hubo un problema al ${action === 'edit' ? 'actualizar' : 'agregar'} el proveedor.`, 'error');
            });
        }
    });
}

function tipoProveedor(providerData){

    if(providerData!=null){ 
        console.log('providerData==>',JSON.stringify(providerData)+'('+typeof(providerData)+'-'+providerData.numero_documento+')');
        $('#optTipoPersona1')
            .prop('checked', providerData.tipo_persona == 3)
            .prop('disabled', providerData.tipo_persona != 3);
    
        $('#optTipoPersona2')
            .prop('checked', providerData.tipo_persona == 1)
            .prop('disabled', providerData.tipo_persona != 1);
    }

    
    var optTipoPersona = $('input:radio[name=optTipoPersona]:checked').val();

	if (optTipoPersona == 3) {
		$("#natural").show("slow");
		$("#juridica").hide("slow");

		$("#dni").attr('required', '');
		$("#name").attr('required', '');
		$("#lastname").attr('required', '');
        if(providerData!=null){
            $("#dni").val(providerData.numero_documento);
            $("#name").val(providerData.name);
            $("#lastname").val(providerData.lastname);
            $("#address1").val(providerData.address1);
            $("#email1").val(providerData.email1);
            $("#phone1").val(providerData.phone1);
            $("#provider_id").val(providerData.id);
        }
		$("#ruc").attr('required', false);
		$("#razon_social").attr('required', false);
		$("#tipodoc").val(1);
	} else if (optTipoPersona == 1) {
		$("#natural").hide("slow");
		$("#juridica").show("slow");

		$("#dni").attr('required', false);
		$("#name").attr('required', false);
		$("#lastname").attr('required', false);
        if(providerData!=null){
            $("#address1").val(providerData.address1);
            $("#email1").val(providerData.email1);
            $("#phone1").val(providerData.phone1);
            $("#ruc").val(providerData.numero_documento);
            $("#razon_social").val(providerData.name);
            $("#provider_id").val(providerData.id);
        }
		$("#ruc").attr('required', '');
		$("#razon_social").attr('required', '');
		$("#tipodoc").val(6);
	}
}