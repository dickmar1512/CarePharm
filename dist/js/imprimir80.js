 // Inicializar jsPDF
 const { jsPDF } = window.jspdf;

 // Función para aplicar márgenes
 function applyMargins(doc, marginLeft, marginTop, marginRight, marginBottom) {
     // Guardar los márgenes en el objeto doc para usarlos luego
     doc.margins = { left: marginLeft, top: marginTop, right: marginRight, bottom: marginBottom };
     return doc;
 }

 // Función para generar el ticket
 $('#imprimir80mm').on('click', async () => {
    var dstosVenta = JSON.parse($("#datosComprobante").val());
     // Crear un nuevo documento PDF
     const doc = new jsPDF({
         orientation: 'portrait', // Orientación vertical
         unit: 'mm', // Unidad en milímetros
         format: [80, 200] // Tamaño del ticket: 80mm de ancho y 200mm de alto
     });

     // Aplicar márgenes personalizados
     applyMargins(doc, 2, 5, 2, 5); // Márgenes: izquierdo 5mm, superior 10mm, derecho 5mm, inferior 10mm
     //doc.margins = { left: 2, top: 5, right: 2, bottom: 5 };

     // Configurar la fuente y el tamaño
     doc.setFont("helvetica"); // Fuente
     doc.setFontSize(6.5); // Tamaño de la fuente

     const imgLogo = 'dist/img/logo2.jpg';
     doc.addImage(imgLogo, 'PNG', 15, 2, 50, 15); // (imagen, formato, x, y, ancho, alto) 30mm de ancho y 10mm de alto

     // Agregar datos empresa
     doc.text(dstosVenta.empresa.Emp_RazonSocial, 40, 20,{ align: 'center', fontStyle: 'bold'}); // 40x 15y 
     doc.text(dstosVenta.empresa.Emp_Direccion+" | Cel.: "+dstosVenta.empresa.Emp_Telefono, 40, 23, { align: 'center', fontStyle: 'bold'}); //40 18
     doc.text("Correo: "+dstosVenta.empresa.Emp_Celular, 27, 26); // 27x 21y
     
     //Tipo documento
     var docLabel = (dstosVenta.comp_cab.TIPO_DOC== 3) ? "BOLETA ELECTRONICA" : "FACTURA ELECTRONICA";

     // Crear una tabla con 3 filas
     doc.autoTable({
         startY: 29, // Posición inicial de la tabla  24
         startX: 10,
         head: [], // Sin encabezado
         body: [
             ["RUC: "+dstosVenta.empresa.Emp_Ruc], // Primera fila
             [docLabel], // Segunda fila (fondo negro y texto blanco)
             [dstosVenta.venta.SERIE+" - "+dstosVenta.venta.COMPROBANTE] // Tercera fila
         ],
         theme: 'grid', // Estilo de la tabla
         styles: { 
             fontSize: 6.5, // Tamaño de la fuente
             cellPadding: 2, // Espaciado interno de las celdas
             textColor: [0, 0, 0] // Color del texto (negro por defecto)
         },
         bodyStyles: {
             fillColor: [255, 255, 255], // Fondo blanco para todas las celdas
             textColor: [0, 0, 0], // Texto negro para todas las celdas
             fontStyle: 'bold'
         },
         columnStyles: {
             0: { cellWidth: 60, halign: 'center' } // Ancho de la columna
         },
         didDrawCell: (data) => {
             // Personalizar la segunda fila (fondo negro y texto blanco)
             if (data.row.index === 1) { // Segunda fila (índice 1)
                 doc.setFillColor(0, 0, 0); // Fondo negro
                 doc.setTextColor(255, 255, 255); // Texto blanco
                 doc.rect(data.cell.x, data.cell.y, data.cell.width, data.cell.height, 'F'); // Rellenar celda
                 doc.text(data.cell.raw, data.cell.x + 30, data.cell.y + 4,{ align: 'center', fontStyle: 'bold'}); // Agregar texto
             }
         },
         margin: { left: 10, right: 10 }, // Márgenes para centrar la tabla
         tableWidth: 'wrap'
     });

     var nomLabel = (dstosVenta.comp_cab.TIPO_DOC== 3) ? "SEÑOR(ES)        " : "RAZÓN SOCIAL  ";
     var docLabel = (dstosVenta.comp_cab.TIPO_DOC== 3) ? "DNI N°" : "RUC";

     let fechaEmisionFormateada = formatearFecha(dstosVenta.comp_cab.fecEmision);

     doc.text("FECHA EMISION: " + fechaEmisionFormateada+"  "+dstosVenta.comp_cab.horEmision, 5, 55); //5 50
     doc.text(nomLabel + ": " + dstosVenta.comp_cab.rznSocialUsuario, 5, 58);// 5 53
     doc.text(docLabel + "                     : " + dstosVenta.comp_cab.numDocUsuario, 5, 61); // 5 56
     doc.text("DIRECCIÓN        : " + dstosVenta.comp_aca.desDireccionCliente, 5, 64); // 5 59

     // Datos de la tabla
     const datosTabla = dstosVenta.detalles;

     // Crear la tabla con jspdf-autotable
     doc.autoTable({
         startY: doc.margins.top + 63, // Posición inicial de la tabla 56
         startX: doc.margins.left,
         head: [['CANT.', 'DESCRIPCIÓN', 'IMPORTE', 'TOTAL']], // Encabezados de la tabla
         body: datosTabla.map(item => [item.ctdUnidadItem, item.desItem, Number(item.mtoValorUnitario).toFixed(2), item.mtoValorVentaItem]), // Cuerpo de la tabla
         theme: 'grid', // Estilo de la tabla
         styles: { fontSize: 6.5, fontStyle: 'bold' }, // Tamaño de la fuente
         headStyles: { fillColor: [0, 0, 0], textColor: [255, 255, 255] }, // Estilo del encabezado
         columnStyles: {
             0: { cellWidth: 10 }, // Ancho de la columna "Cant."
             1: { cellWidth: 35 }, // Ancho de la columna "Descripción"
             2: { cellWidth: 14, halign: 'right' }, // Ancho de la columna "Importe"
             3: { cellWidth: 14, halign: 'right' }  // Ancho de la columna "SubTot."
         },
         margin: { left: 3, right: 3 }, // Márgenes para centrar la tabla
         tableWidth: 'wrap'
     }); 

     // doc.text("Son: ciento sesenta y uno con 00/100 Soles.", 10, finalY + 25);
     
     const finalY = doc.lastAutoTable.finalY + 5; // Posición después de la tabla

     // Generar el código QR
     const qrData = dstosVenta.empresa.Emp_Ruc + "|" + dstosVenta.venta.TIPO + "|" + dstosVenta.venta.SERIE + "-"+dstosVenta.venta.COMPROBANTE + "|0.00|" + dstosVenta.comp_cab.sumImpVenta + "|" + dstosVenta.comp_cab.fecEmision+" "+ dstosVenta.comp_cab.fecEmision + "|";
     const qrCanvas = document.createElement('canvas');

     QRCode.toCanvas(qrCanvas, qrData, { width: 50, margin: 1 }, (error) => {
         if (error) {
             console.error("Error al generar el QR:", error);
             return;
         }

         // Convertir el canvas a una imagen
         const qrImage = qrCanvas.toDataURL('image/png');

         // Totales
         const datosTablaTotales = [                
             {desc:"OPE.EXONERADA", imp: dstosVenta.comp_cab.sumTotValVenta},
             {desc:"OPE.INAFECTA", imp:0.00},
             {desc:"OPE.GRAVADA", imp:0.00},
             {desc:"IGV", imp:0.00},
             {desc:"IMPORTE TOTAL",imp: dstosVenta.comp_cab.sumImpVenta}
         ];

         // Definir la estructura de la tabla con el QR en una celda y los totales en otra
         const datosTablaResumen = [
             [
                 { content: '', rowSpan: 5, styles: { cellWidth: 35 } }, // Espacio para el QR
                 "OPE.EXONERADA", datosTablaTotales[0].imp
             ],
             ["OPE.INAFECTA", datosTablaTotales[1].imp.toFixed(2)],
             ["OPE.GRAVADA", datosTablaTotales[2].imp.toFixed(2)],
             ["IGV", datosTablaTotales[3].imp.toFixed(2)],
             ["IMPORTE TOTAL", datosTablaTotales[4].imp],
             [{ content: "SON: " + dstosVenta.numLetra, colSpan: 3, styles: { halign: 'left', fontStyle: 'bold' } }]
         ];

         doc.autoTable({
             startY: finalY,
             startX: doc.margins.left,
             body: datosTablaResumen,
             theme: 'grid',
             styles: {
                 fontSize: 6.5,
                 cellPadding: 2,
                 textColor: [0, 0, 0]
             },
             bodyStyles: {
                 fillColor: [255, 255, 255],
                 textColor: [0, 0, 0],
                 fontStyle: 'bold'
             },
             columnStyles: {
                 0: { cellWidth: 35, halign: 'center' }, // Columna del QR
                 1: { cellWidth: 25, halign: 'left' }, // Descripción
                 2: { cellWidth: 15, halign: 'right' } // Monto
             },
             margin: { left: doc.margins.left, right: doc.margins.right }, // Márgenes para centrar la tabla,
             didDrawCell: function (data) {
                 if (data.row.index === 0 && data.column.index === 0) {
                     doc.addImage(qrImage, 'PNG', data.cell.x + 2, data.cell.y + 2, 30, 30); // Agregar el QR al PDF
                 }
             },
             tableWidth: 'wrap'
         });                

         const finalY2 = doc.lastAutoTable.finalY + 5;
         doc.text("Consulte y/o descargue su comprobante electrónico en \n www.sunat.gob.pe, utilizando su clave SOL", 40, finalY2,{ align: 'center', fontStyle: 'bold', lineHeightFactor: 1, fontSize: 7 });
         doc.text("CAJERO: " + dstosVenta.cajero, 2, finalY2+7,{ align: 'left', fontStyle: 'bold', fontSize: 10 });    

         // Generar el PDF como una URL de datos
         const pdfData = doc.output('datauristring');

         // Abrir el modal;
        Swal.fire({
            title: '<h3>Comprobante: '+ dstosVenta.venta.SERIE + '-'+dstosVenta.venta.COMPROBANTE + '  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha de Emisión: ' + fechaEmisionFormateada + ' ' + dstosVenta.comp_cab.horEmision + '</h3>', // Título de la ventana modal
            html: `
                <iframe 
                    src="${pdfData}" 
                    width="100%" 
                    height="500px" 
                    style="border: none;"
                ></iframe>
            `,
            showCloseButton: true, // Muestra el botón de cerrar
            showConfirmButton: false, // Oculta el botón de confirmación
            width: '60%', // Ancho de la ventana modal
            //padding: '0', // Elimina el padding para que el iframe ocupe todo el espacio
        });
     }); 
 });