

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
//      // Obtener instancia de DataTable o inicializarla si no existe
//     let dataTable = $('#sellsnew').DataTable();
//     if (!$.fn.DataTable.isDataTable('#sellsnew')) {
//         dataTable = $('#sellsnew').DataTable({
//             "responsive": true, 
//                     "lengthChange": true, 
//                     "autoWidth": false,
//                     "dom": '<"row"<"col-md-3"l><"col-md-6 text-center"B><"col-md-3"f>>rtip',
//                     "buttons": [
//                         { extend: "copy", text: "Copiar" }, 
//                         "csv", 
//                         "excel", 
//                         "pdf", 
//                         "print", 
//                         { extend: "colvis", text: "Visible" }
//                     ],
//                     "language": {
//                         "sProcessing": "Procesando...",
//                         "sLengthMenu": "Mostrar _MENU_ registros",
//                         "sZeroRecords": "No se encontraron resultados",
//                         "sEmptyTable": "Ningún dato disponible en esta tabla",
//                         "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
//                         "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
//                         "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
//                         "sInfoPostFix": "",
//                         "sSearch": "Buscar:",
//                         "sUrl": "",
//                         "sInfoThousands": ",",
//                         "sLoadingRecords": "Cargando...",
//                         "oPaginate": {
//                             "sFirst": "Primero",
//                             "sLast": "Último",
//                             "sNext": "Siguiente",
//                             "sPrevious": "Anterior"
//                         },
//                         "oAria": {
//                             "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
//                             "sSortDescending": ": Activar para ordenar la columna de manera descendente"
//                         }
//                     }
//         });
//     }

//     // Realizar petición AJAX
//     $.ajax({
//         url: './?action=getSells',
//         type: 'GET',
//         dataType: 'json',
//         data: params,
//         beforeSend: function () {
//                 $('#sellsnew tbody').html('<tr><td colspan="9" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</td></tr>');
//                 $('#total-ventas').text('0.00');                    
//                 $('#total-capital').text('0.00');
//                 $('#total-ganancia').text('0.00');
//             },
//         success: function(response) {           
//             // Agregar nuevos datos
//             if(response.success){
//                 // Limpiar y agregar nuevos datos
//                 dataTable.clear();
                
//                 if(response.data && response.data.length > 0) {
//                     response.data.forEach(venta => {
//                         dataTable.row.add([
//                             venta.verComprobante || '',
//                             venta.comprobante || '',
//                             venta.cliente || '',
//                             venta.importe || '',
//                             venta.fecha || '',
//                             venta.fechaEnvio || '',
//                             venta.estado || '',
//                             venta.descargarXML || '',
//                             venta.usuario || ''
//                         ]).draw(false);
//                     });
//                 } else {
//                     dataTable.row.add(['No se encontraron resultados', '', '', '', '', '', '', '', '']).draw();
//                 }
                
//                 // Redibujar la tabla con los nuevos datos
//                 dataTable.draw();
                
//                 // Actualizar totales
//                 $('#total-ventas').text(response.total_ventas);                    
//                 $('#total-capital').text(response.total_capital);
//                 $('#total-ganancia').text(response.total_ganancia);
                 
//             }else{
//                 Swal.fire('Error', response.message, 'error');
//             }
//         },
//         error: function(xhr, status, error) {
//             Swal.fire('Error', 'Error al cargar los gastos', 'error');
//         },
//         complete: function() {
//             $('#loading').hide();
//         }
//     });
// }