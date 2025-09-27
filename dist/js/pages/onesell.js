$(document).ready(function() {
	// Simular el clic automáticamente al cargar la página
	const botonImprimir = $('#imprimir80mm');
	if (botonImprimir.length) {
		botonImprimir.click();
	}

    $('#actualizarTipoPago').on('click', function() {
        $.ajax({
            url: './?action=updatePaymentType',
            type: 'POST',
            data: {
                paymentId: $('#sellid').val(),
                paymentType: $('#selTipoPago').val()
            },
            success: function(response) {
                console.log('Tipo de pago actualizado:', response);
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'El tipo de pago ha sido actualizado correctamente.'
                });
            },
            error: function(xhr, status, error) {
                console.error('Error al actualizar tipo de pago:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo actualizar el tipo de pago. Intente nuevamente.'
                });
            }
        });
    });
})