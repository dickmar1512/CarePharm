$(document).ready(function() {
    // Obtener elementos del DOM
    const $denominationInputs = $('#denominations input[type="number"]');
    const $processButton = $('#procesarVentasBtn');
    const totalVentas = parseFloat($('#totalVentas').val()) || 0;
    
    // Crear el elemento para mostrar la diferencia
    const $differenceContainer = $('<div>', {
        class: 'difference-container',
        css: {
            marginTop: '20px',
            textAlign: 'center'
        }
    });
    
    const $differenceLabel = $('<label>', {
        text: 'Diferencia: ',
        css: {
            fontWeight: 'bold',
            marginRight: '10px'
        }
    });
    
    const $differenceInput = $('<input>', {
        type: 'text',
        id: 'difference',
        class: 'form-control-sm',
        css: {
            textAlign: 'center',
            fontWeight: 'bold',
            width: '100px',
            border: '2px solid #dc3545',
            color: '#dc3545',
            marginRight: '10px'
        }
    }).val('-' + totalVentas.toFixed(2)).prop('readOnly', true);

    // Label para mostrar Faltante/Sobrante
    const $statusLabel = $('<span>', {
        id: 'difference-status',
        css: {
            fontWeight: 'bold',
            fontSize: '16px'
        }
    });
    
    $differenceContainer.append($differenceLabel, $differenceInput, $statusLabel);
    
    // Insertar el elemento después de la tabla de billetes y monedas
    $('.col-md-3 table').after($differenceContainer);
    
    // Desactivar el botón al inicio
    $processButton.addClass('disabled');
    
    // Valores de las denominaciones
    const denominations = {
        'b200': 200,
        'b100': 100,
        'b50': 50,
        'b20': 20,
        'b10': 10,
        'm5': 5,
        'm2': 2,
        'm1': 1,
        'c50': 0.5,
        'c20': 0.2,
        'c10': 0.1,
        'yape': 1, // Placeholder for Yape
        'plin': 1, // Placeholder for Plin
        'tcredito': 1 // Placeholder for Tarjeta de Crédito
    };
    
    // Función para calcular el total de efectivo
    function calculateCashTotal() {
        let total = 0;
        
        $denominationInputs.each(function() {
            const value = parseFloat($(this).val()) || 0;
            console.log($(this).attr('id'), value);
            const denominationValue = denominations[$(this).attr('id')];
            console.log('Denomination Value:', denominationValue);
            total += value * denominationValue;
        });
        return total;
    }
    
    // Función para actualizar la diferencia
    function updateDifference() {
        const cashTotal = calculateCashTotal();
        const difference = (cashTotal - totalVentas).toFixed(2);
        const absoluteDifference = Math.abs(difference);
        
        $differenceInput.val(difference);
        
        // Cambiar color según la diferencia
        if (difference < 0) {
            $differenceInput.css({
                'border': '2px solid #dc3545',
                'color': '#dc3545'
            });
            $statusLabel.text('Faltante: ' + absoluteDifference.toFixed(2))
                     .css('color', '#dc3545');
        } else if (difference > 0) {
            $differenceInput.css({
                'border': '2px solid #ffc107',
                'color': '#ffc107'
            });
            $statusLabel.text('Sobrante: ' + absoluteDifference.toFixed(2))
                     .css('color', '#ffc107');
        } else {
            $differenceInput.css({
                'border': '2px solid #28a745',
                'color': '#28a745'
            });
            $statusLabel.text('Correcto')
                     .css('color', '#28a745');
        }
        
        // Habilitar o deshabilitar el botón
        if (difference === 0) {
            $processButton.removeClass('disabled');
        } else {
            $processButton.addClass('disabled');
        }
    }

    // Función para recolectar datos de denominaciones
    function getDenominationsData() {
        const data = {};
        $denominationInputs.each(function() {
            const id = $(this).attr('id');
            const value = parseFloat($(this).val()) || 0;
            data[id] = value;
        });
        return data;
    }
    
    // Manejador del click para procesar ventas
    $('#procesarVentasBtn').click(function(e) {
        e.preventDefault();
        
        if ($(this).hasClass('disabled')) return;
        
        // Mostrar loading
        const originalText = $(this).html();
        $(this).html('<i class="fa fa-spinner fa-spin"></i> Procesando...');
        $(this).prop('disabled', true);
        
        // Recolectar datos
        const postData = {
            denominations: getDenominationsData(),
            action: 'process_box'
        };
        
        // Enviar por AJAX
        $.ajax({
            url: './?action=processbox',
            type: 'POST',
            dataType: 'json',
            data: postData,
            success: function(response) {
                if (response.success) {
                    // Redirigir si todo está bien
                    console.log(response);
                    window.location.href = './?view=b&id=' + response.box_id;
                } else {
                    // Mostrar error
                    alert('Error: ' + response.message);
                    $('#procesarVentasBtn').html(originalText).prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                alert('Error al procesar: ' + error);
                $('#procesarVentasBtn').html(originalText).prop('disabled', false);
            }
        });
    });
    
    // Event listeners para los inputs
    $denominationInputs.on('input', updateDifference);
    
    // Actualizar al cargar la página
    updateDifference();
});