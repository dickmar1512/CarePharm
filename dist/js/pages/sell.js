// script modal sweetAlert2
$('#money').val($('#total').val());
$('#money2').val($('#total').val());
$('#money3').val($('#total').val());
$('#btnAgregarItem').on('click', agregarProducto);
$('#btnAgregarItem2').on('click', agregarProducto);
$('#btnAgregarItem3').on('click', agregarProducto);

function agregarProducto() {
    Swal.fire({
        title: '<h3>Buscar producto por nombre o por código</h3>',
        html: `
                <div style="text-align: left;">
                    <form id="searchp">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="view" value="sell">
                                <div class="input-icon-container">
                                    <input type="text" id="product_code2" autofocus name="product" class="form-control">
                                    <div class="icon-wrapper">
                                        <i class="icon fas fa-search" style="color: white;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <div id="show_search_results"></div>
                </div>
            `,
        showCloseButton: true,
        showConfirmButton: false,
        showCancelButton: true,
        //focusConfirm: false,
        //confirmButtonText: 'CERRAR',
        cancelButtonText: 'CANCELAR',
        customClass: {
            container: 'custom-swal-container',
            popup: 'custom-swal-popup',
            header: 'custom-swal-header',
            title: 'custom-swal-title',
            content: 'custom-swal-content',
            closeButton: 'custom-swal-close-button',
            actions: 'custom-swal-actions',
            //confirmButton: 'btn btn-secondary',
            cancelButton: 'btn btn-danger'
        },
        width: '70%',
        didOpen: () => {
            // Aquí puedes añadir lógica adicional cuando el modal se abre
            document.getElementById('product_code2').focus();

            $("#product_code2").on("input", function () {
                // Obtenemos el valor del input
                let searchTerm = $(this).val().trim();

                // Verificamos si el término de búsqueda está vacío
                if (searchTerm == "") {
                    // Si está vacío, limpiamos los resultados y no hacemos la búsqueda
                    $("#show_search_results").html("");
                    return; // Salimos de la función
                }

                // Verificamos si el término de búsqueda tiene al menos 3 caracteres
                if (searchTerm.length >= 3) {
                    // Realizamos la búsqueda
                    $.get("./?action=searchproduct", $("#searchp").serialize(), function (data) {
                        $("#show_search_results").html(data);
                    });
                } else {
                    // Si el término tiene menos de 3 caracteres, limpiamos los resultados
                    $("#show_search_results").html("");
                }
                //$("#product_code2").val("");
            });

            $("#searchp").on("submit", function (e) {
                e.preventDefault();

                $.get("./?action=searchproduct", $("#searchp").serialize(), function (data) {
                    $("#show_search_results").html(data);
                });
                $("#product_code2").val("");
            });
        }
    });
}

$("input[name=optTipoComprobante]").click(function () {
    var optTipoComprobante = $('input:radio[name=optTipoComprobante]:checked').val();

    if (optTipoComprobante == 3) {
        $("#comprobante_boleta").show("slow");
        $("#comprobante_factura").hide("slow");
        $("#comprobante_orden").hide("slow");

    }
    else if (optTipoComprobante == 1) {
        $("#comprobante_boleta").hide("slow");
        $("#comprobante_factura").show("slow");
        $("#comprobante_orden").hide("slow");
    }
    else if (optTipoComprobante == 0) {
        $("#comprobante_boleta").hide("slow");
        $("#comprobante_factura").hide("slow");
        $("#comprobante_orden").show("slow");
    }
});

async function enviado2(tip,event) {
    event.preventDefault(); // ¡Clave! Detiene el envío automático
    let discount = 0;
    let money = 0;
    const optTipoComprobante = $('input:radio[name=optTipoComprobante]:checked').val();
    const total = parseFloat($("#total").val());

    console.log("tip==>",tip);
    // Validación para FACTURA (tipo 1)
    if (optTipoComprobante == 1) {
        const ruc = $("#formfactura #ruc").val();
        const razonSocial = $("#formfactura #rznSocialUsuario").val();
        console.log("ruc==>",ruc);
        if (!ruc || ruc == "00000000000" || ruc.length !== 11) {
            await Swal.fire({
                title: 'Error en RUC',
                text: 'Ingrese un RUC válido (11 dígitos)',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
            $("#formfactura #ruc").focus();
            return false;
        }

        if (!razonSocial || razonSocial === "Cliente General") {
            await Swal.fire({
                title: 'Error en Razón Social',
                text: 'Ingrese la razón social del cliente',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
            $("#formfactura #rznSocialUsuario").focus();
            return false;
        }
    }

    // Obtener valores según el tipo de comprobante
    if (optTipoComprobante == 3) { // Boleta
        money = parseFloat($("#money").val()) || 0;
        discount = parseFloat($("#discount").val()) || 0;
    } 
    else if (optTipoComprobante == 1) { // Factura
        money = parseFloat($("#money2").val()) || 0;
        discount = parseFloat($("#discount2").val()) || 0;
    } 
    else if (optTipoComprobante == 0) { // Nota de Venta
        money = parseFloat($("#money3").val()) || 0;
        discount = 0;
    }

    // Validar monto entregado
    if (money < (total - discount)) {
        await Swal.fire({
            title: '¡Advertencia!',
            text: 'No se puede efectuar la operación. Ingrese monto entregado por el cliente',
            icon: 'warning',
            confirmButtonText: 'Aceptar'
        });
        return false;
    }

    // Confirmar cambio
    const result = await Swal.fire({
        title: '¿El cambio es correcto?',
        html: `Cambio S/: <b>${(money - (total - discount)).toFixed(2)}</b>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar',
        allowOutsideClick: false // Evita que se cierre haciendo clic fuera
    });

    if (result.isConfirmed) {
        // Prevenir envíos duplicados
        if (cuenta === 0) {
            cuenta++;
            
            // Envío del formulario según el tipo
            switch(tip) {
                case 1:
                    document.getElementById('formfactura').submit();
                    break;
                case 3:
                    document.getElementById('formboleta').submit();
                    break;
                default:
                    document.getElementById('formnotaventa').submit();
            }
            return true;
        } else {
            await Swal.fire({
                title: 'Información',
                text: 'El formulario ya está siendo enviado. Por favor aguarde.',
                icon: 'info',
                confirmButtonText: 'Aceptar'
            });
            return false;
        }
    }
    return false;
}

$(document).ready(function () {
    $("#searchk").on("submit", function (e) {
        e.preventDefault();

        $.get("./?action=searchkit", $("#searchk").serialize(), function (data) {
            $("#show_search_results").html(data);
        });
        $("#kit_code2").val("");
    });

    $("#product_code").keydown(function (e) {
        if (e.which == 17 || e.which == 74) {
            e.preventDefault();
        } else {
            console.log(e.which);
        }
    }); 
    //Initialize Select2 Elements
    $('.select2').select2();

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    });
});

$(document).on('keydown', function(event) {
    switch(event.key) {
        case 'F1':
            event.preventDefault();
        
            if ($('#optTipoComprobante1').is(':checked')) {
                $('#btnAgregarItem').trigger('click');
            } 
            else if ($('#optTipoComprobante2').is(':checked')) {
                $('#btnAgregarItem2').trigger('click');
            } 
            else if ($('#optTipoComprobante3').is(':checked')) {
                $('#btnAgregarItem3').trigger('click');
            }
            break;
        case 'F2':
            break;
        case 'F3':
            event.preventDefault();
            const $activeLabel = $('input:radio[name=optTipoComprobante]:checked + label');
        
            // Efecto visual
            $activeLabel.css('box-shadow', '0 0 10px #4CAF50');
            setTimeout(() => $activeLabel.css('box-shadow', 'none'), 300);

            const activeForm = $('input:radio[name=optTipoComprobante]:checked').val();
        
            if (activeForm == 3) enviado2(3,event); // Boleta
            else if (activeForm == 1) enviado2(1,event); // Factura
            else if (activeForm == 0) enviado2(0,event); // Nota de venta

            break;        
        case 'F4':
            event.preventDefault(); 
            document.getElementById('optTipoComprobante1').click();
            break;
        case 'F5':
            event.preventDefault();
            document.getElementById('optTipoComprobante2').click();
            break;
        case 'F6':
            event.preventDefault();
            document.getElementById('optTipoComprobante3').click();
            break;
    }
});
