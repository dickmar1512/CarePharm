// function to refresh the cart UI
function refreshCart() {
    $.ajax({
        url: "./?action=cart_table&t=" + new Date().getTime(),
        type: "GET",
        cache: false,
        success: function (data) {
            $(".cart-container").html(data);
            // Get total from the first hidden input with class js_total_val
            const newTotal = $(".js_total_val").first().val() || 0;
            $("#money, #money2, #money3").val(newTotal);
        },
        error: function() {
            console.error("Error refreshing cart");
        }
    });
}

// function to remove item
function removeItem(productId) {
    Swal.fire({
        title: '¿Eliminar producto?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.get("./?action=clearcart_ajax", { product_id: productId }, function (data) {
                refreshCart();
            });
        }
    });
}

// function to clear cart
function clearCart() {
    Swal.fire({
        title: '¿Vaciar carrito?',
        text: 'Se eliminarán todos los productos seleccionados',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, vaciar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.get("./?action=clearcart_ajax", function (data) {
                refreshCart();
            });
        }
    });
}

// script modal sweetAlert2
$(document).ready(function() {
    const initialTotal = $(".js_total_val").first().val() || 0;
    $('#money, #money2, #money3').val(initialTotal);
});

$(document).on('click', '#btnAgregarItem, #btnAgregarItem2, #btnAgregarItem3, #btnAgregarItemCentral', agregarProducto);

// Intercept form add to cart in the modal
$(document).on('submit', '.form-add-to-cart', function (e) {
    e.preventDefault();
    const form = $(this);
    const url = form.attr('action');
    const data = form.serialize();

    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Producto agregado',
                    showConfirmButton: false,
                    timer: 800,
                    position: 'top-end',
                    toast: true
                });
                refreshCart();
                Swal.close();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        }
    });
});

function agregarProducto() {
    Swal.fire({
        title: '<h4 style="margin: 0; font-size: 1.1em;">Buscar Producto</h4>',
        html: `
            <div style="text-align: left; margin-top: 10px;">
                <form id="searchp">
                    <input type="hidden" name="view" value="sell">
                    <div class="input-icon-container">
                        <input type="text" id="product_code2" name="product" 
                               class="form-control form-control-sm" 
                               placeholder="Nombre o código del producto..."
                               style="padding: 6px 12px; font-size: 0.9em;"
                               autocomplete="off">
                        <div class="icon-wrapper">
                            <i class="icon fas fa-search" style="color: #6c757d; font-size: 0.8em;"></i>
                        </div>
                    </div>
                </form>
                <div id="show_search_results" style="margin-top: 10px;"></div>
            </div>
        `,
        showCloseButton: true,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'Cerrar',
        customClass: {
            container: 'custom-swal-container',
            popup: 'custom-swal-popup compact-modal',
            header: 'custom-swal-header',
            title: 'custom-swal-title',
            content: 'custom-swal-content',
            closeButton: 'custom-swal-close-button',
            actions: 'custom-swal-actions',
            cancelButton: 'btn btn-sm btn-secondary'
        },
        width: '75%',
        padding: '15px',
        didOpen: () => {
            const style = document.createElement('style');
            style.textContent = `
                .compact-modal {
                    font-size: 0.9em !important;
                }
                .compact-modal .swal2-header {
                    padding: 10px 15px 5px !important;
                }
                .compact-modal .swal2-content {
                    padding: 5px 15px !important;
                }
                .compact-modal .swal2-actions {
                    padding: 10px 15px 15px !important;
                    margin: 0 !important;
                }
                .compact-modal hr {
                    margin: 8px 0 !important;
                }
            `;
            document.head.appendChild(style);
            
            // Focus manually after a short timeout to avoid focus collision
            setTimeout(() => {
                const input = document.getElementById('product_code2');
                if (input) input.focus();
            }, 100);

            $("#product_code2").on("input", function () {
                let searchTerm = $(this).val().trim();
                if (searchTerm == "") {
                    $("#show_search_results").html("");
                    return;
                }
                if (searchTerm.length >= 3) {
                    $.get("./?action=searchproduct", $("#searchp").serialize(), function (data) {
                        $("#show_search_results").html(data);
                    });
                } else {
                    $("#show_search_results").html("");
                }
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
    if(event) event.preventDefault(); 
    let discount = 0;
    let money = 0;
    const optTipoComprobante = $('input:radio[name=optTipoComprobante]:checked').val();
    const total = parseFloat($(".js_total_val").first().val() || 0);

    if (total <= 0) {
        Swal.fire('Error', 'El carrito está vacío', 'error');
        return false;
    }

    if (optTipoComprobante == 1) {
        const ruc = $("#formfactura #ruc").val();
        const razonSocial = $("#formfactura #rznSocialUsuario").val();
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

    if (optTipoComprobante == 3) {
        money = parseFloat($("#money").val()) || 0;
        discount = parseFloat($("#discount").val()) || 0;
    } 
    else if (optTipoComprobante == 1) {
        money = parseFloat($("#money2").val()) || 0;
        discount = parseFloat($("#discount2").val()) || 0;
    } 
    else if (optTipoComprobante == 0) {
        money = parseFloat($("#money3").val()) || 0;
        discount = 0;
    }

    if (money < (total - discount)) {
        await Swal.fire({
            title: '¡Advertencia!',
            text: 'No se puede efectuar la operación. Ingrese monto entregado por el cliente',
            icon: 'warning',
            confirmButtonText: 'Aceptar'
        });
        return false;
    }

    const result = await Swal.fire({
        title: '¿El cambio es correcto?',
        html: `Cambio S/: <b>${(money - (total - discount)).toFixed(2)}</b>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar',
        allowOutsideClick: false
    });

    if (result.isConfirmed) {
        if (optTipoComprobante == 3) document.getElementById('formboleta').submit();
        else if (optTipoComprobante == 1) document.getElementById('formfactura').submit();
        else document.getElementById('formnotaventa').submit();
        return true;
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
});

$(document).on('keydown', function(event) {
    switch(event.key) {
        case 'F1':
            event.preventDefault();
            agregarProducto();
            break;
        case 'F3':
            event.preventDefault();
            enviado2();
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