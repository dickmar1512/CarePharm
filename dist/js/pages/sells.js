moment.updateLocale('es', {
    week: {
        dow: 0,  // Domingo como primer día
    }
});

var parTooltip = {
    today: 'Ir a hoy',
    clear: 'Limpiar selección',
    close: 'Cerrar',
    selectMonth: 'Seleccionar mes',
    prevMonth: 'Mes anterior',
    nextMonth: 'Mes siguiente',
    selectYear: 'Seleccionar año',
    prevYear: 'Año anterior',
    nextYear: 'Año siguiente',
    selectDecade: 'Seleccionar década',
    prevDecade: 'Década anterior',
    nextDecade: 'Década siguiente',
    prevCentury: 'Siglo anterior',
    nextCentury: 'Siglo siguiente'
};

$('#fechaini').datetimepicker({
    format: 'L',
    locale: 'es',
    buttons: {
        showToday: true,
        showClose: true
    },
    tooltips: parTooltip
});

$('#fechafin').datetimepicker({
    format: 'L',
    locale: 'es',
    buttons: {
        showToday: true,
        showClose: true
    },
    tooltips: parTooltip
});

// Inicializar select2
$('.select2').select2({
    theme: 'bootstrap4'
}); 

// $(document).ready(function() {     

//     // Manejar el envío del formulario
//     $('#filterForm').on('submit', function(e) {
//         e.preventDefault();
//         loadSellsData();
//     });

//     // Cargar datos iniciales
//     loadSellsData();
// });

// const fechaDefault = new Date();
// const opciones = {day: 'numeric', month: 'numeric', year: 'numeric'};

// // Función para cargar datos en la tabla
// function loadSellsData() {
//     // Mostrar carga
//     $('#loading').show();
    
//     // Obtener parámetros del formulario
//     const params = {
//         sd: ($('input[name="sd"]').val() == undefined) ? fechaDefault.toLocaleDateString('es-ES', opciones) : $('input[name="sd"]').val(),
//         ed: ($('input[name="ed"]').val() == undefined) ? fechaDefault.toLocaleDateString('es-ES', opciones) : $('input[name="ed"]').val(),
//         user_id: $('select[name="user_id"]').val()
//     };
    
//     // Validar fechas
//     if(!params.sd || !params.ed) {
//         console.log('Por favor seleccione ambas fechas');
//         $('#loading').hide();
//         return;
//     }

//     // Realizar petición AJAX
//     $.ajax({
//         url: './?action=getSells',
//         type: 'GET',
//         dataType: 'json',
//         data: params,
//         success: function(response) {            
//             // Agregar nuevos datos
//             if(response.data && response.data.length > 0) {
//                 // Destruye la tabla existente si ya está inicializada
//                 if ($.fn.DataTable.isDataTable('#sells')) {
//                     $('#sells').DataTable().destroy();
//                 }
                
//                 // Limpia el contenido del tbody
//                 $('#sells tbody').empty();
                
//                 $('#sells').DataTable({
//                     destroy: true,  // Destruye la tabla existente para evitar conflictos
//                     data: response.data,
//                     columns: [
//                         { 
//                             data: null,
//                             defaultContent: '<div class="btn-group"><a href="?view=onesell&id=<?= $sell->id ?>&tipodoc=<?= $sell->tipo_comprobante ?>" class="btn btn-xs btn-default"><i class="fas fa-eye"></i></a></div>',
//                             orderable: false
//                         },
//                         { data: 'comprobante' },
//                         { data: 'cliente' },
//                         { 
//                             data: 'importe',
//                             render: function(data, type, row) {
//                                 return  parseFloat(data).toFixed(2);
//                             }
//                         },
//                         { data: 'fecha' },
//                         { data: 'usuario' }
//                     ],
//                     responsive: true
//                 });
                
//                 // Actualizar totales
//                 $('#total-ventas').text('S/ ' + response.total_ventas);
                
//                 // Solo si es admin
//                 if(response.is_admin) {
//                     $('#total-capital').text('S/ ' + response.total_capital);
//                     $('#total-ganancia').text('S/ ' + response.total_ganancia);
//                     $('.admin-only').show();
//                 } else {
//                     $('.admin-only').hide();
//                 }
//             } else {
//                 // Mostrar mensaje si no hay datos
//                 $('#sells').DataTable().clear().draw();
//                 $('#sells').DataTable().row.add({
//                     "comprobante": "No hay datos",
//                     "cliente": "",
//                     "importe": "",
//                     "fecha": "",
//                     "usuario": ""
//                 }).draw();
//             }
//         },
//         error: function(xhr, status, error) {
//             console.error('Error al cargar datos:', error);
//            // alert('Error al cargar los datos');
//         },
//         complete: function() {
//             $('#loading').hide();
//         }
//     });
// }