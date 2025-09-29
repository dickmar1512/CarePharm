// Función para aplicar márgenes
function applyMargins(doc, marginLeft, marginTop, marginRight, marginBottom) {
    doc.margins = { left: marginLeft, top: marginTop, right: marginRight, bottom: marginBottom };
    return doc;
}

// Función para calcular la altura dinámica del PDF
function calcularAlturaDinamica(datosVenta) {
    let alturaEstimada = 80;
    alturaEstimada += (datosVenta.detalles.length * 5) + 20;
    alturaEstimada += 40;
    alturaEstimada += 45;
    return Math.max(200, alturaEstimada);
}

// Función para generar el ticket
$('#imprimirNV80mm').on('click', async () => {
    var datosVenta = JSON.parse($("#datosComprobante").val());
    const alturaCalculada = calcularAlturaDinamica(datosVenta);
    
    const doc = new jsPDF({
        orientation: 'portrait',
        unit: 'mm',
        format: [80, alturaCalculada]
    });

    applyMargins(doc, 2, 5, 2, 5);
    doc.setFont("helvetica");
    doc.setFontSize(6.5);

    const imgLogo = 'dist/img/logo2.jpg';
    doc.addImage(imgLogo, 'PNG', 15, 2, 50, 15);

    // Agregar datos empresa
    doc.text(datosVenta.empresa.Emp_RazonSocial, 40, 20, { align: 'center', fontStyle: 'bold' });
    doc.text(datosVenta.empresa.Emp_Direccion + " | Cel.: " + datosVenta.empresa.Emp_Telefono, 40, 23, { align: 'center', fontStyle: 'bold' });
    doc.text("Correo: " + datosVenta.empresa.Emp_Celular, 27, 26);
    
    var docLabel = "NOTA DE VENTA";

    // Tabla de encabezado del documento
    doc.autoTable({
        startY: 29,
        startX: 10,
        head: [],
        body: [
            ["RUC: " + datosVenta.empresa.Emp_Ruc],
            [docLabel],
            [datosVenta.venta.serie + " - " + datosVenta.venta.comprobante]
        ],
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
            0: { cellWidth: 60, halign: 'center' }
        },
        didDrawCell: (data) => {
            if (data.row.index === 1) {
                doc.setFillColor(0, 0, 0);
                doc.setTextColor(255, 255, 255);
                doc.rect(data.cell.x, data.cell.y, data.cell.width, data.cell.height, 'F');
                doc.text(data.cell.raw, data.cell.x + 30, data.cell.y + 4, { align: 'center', fontStyle: 'bold' });
            }
        },
        margin: { left: 10, right: 10 },
        tableWidth: 'wrap'
    });

    var nomLabel = (datosVenta.cliente.tipo_persona == 3) ? "SEÑOR(ES)        " : "RAZÓN SOCIAL  ";
    var docLabel = (datosVenta.cliente.tipo_persona == 3) ? "DNI N°" : "RUC";
    
    let arrFecha = (datosVenta.venta.created_at).split(' ');
    let fechaEmisionFormateada = formatearFecha(arrFecha[0]);

    doc.text("FECHA EMISION: " + fechaEmisionFormateada + "  " + arrFecha[1], 5, 55);
    doc.text(nomLabel + ": " + datosVenta.cliente.name + ' ' + datosVenta.cliente.lastname, 5, 58);
    doc.text(docLabel + "                     : " + datosVenta.cliente.numero_documento, 5, 61);
    doc.text("DIRECCIÓN        : " + datosVenta.cliente.address1, 5, 64);

    // Datos de la tabla - CORRECCIÓN PRINCIPAL AQUÍ
    const datosTabla = datosVenta.detalles;

    doc.autoTable({
        startY: doc.margins.top + 63,
        head: [['CANT.', 'DESCRIPCIÓN', 'IMPOR.', 'TOTAL']],
        body: datosTabla.map(item => [
            item.ctdUnidadItem, 
            item.desItem, 
            Number(item.mtoValorUnitario).toFixed(2), 
            Number(item.mtoValorVentaItem).toFixed(2)
        ]),
        theme: 'grid',
        styles: { 
            fontSize: 6.5, 
            fontStyle: 'bold',
            cellPadding: 1.5 // Reducido para ganar espacio
        },
        headStyles: { 
            fillColor: [0, 0, 0], 
            textColor: [255, 255, 255],
            halign: 'center'
        },
        columnStyles: {
            0: { cellWidth: 10, halign: 'center' },   // CANT: reducido de 10 a 8
            1: { cellWidth: 38, halign: 'left' },    // DESC: reducido de 35 a 38
            2: { cellWidth: 13, halign: 'right' },   // P.U.: reducido de 14 a 12
            3: { cellWidth: 13, halign: 'right' }    // TOTAL: reducido de 14 a 13
        },
        // Total: 8 + 38 + 12 + 13 = 71mm (deja espacio para márgenes y bordes)
        margin: { left: 2, right: 2 },
        tableWidth: 'auto' // Cambiado de 'wrap' a 'auto'
    });

    const finalY = doc.lastAutoTable.finalY + 5;

    // Generar el código QR
    const qrData1 = datosVenta.empresa.Emp_Ruc + "|" + datosVenta.venta.tipo_comprobante + "|" + 
                    datosVenta.venta.serie + "-" + datosVenta.venta.comprobante + "|0.00|" + 
                    datosVenta.venta.total + "|" + arrFecha[0] + " " + arrFecha[1] + "|";
    
    const qrCanvas1 = document.createElement('canvas');

    QRCode.toCanvas(qrCanvas1, qrData1, { width: 50, margin: 1 }, (error) => {
        if (error) {
            console.error("Error al generar el QR:", error);
            return;
        }

        const qrImage1 = qrCanvas1.toDataURL('image/png');

        const datosTablaTotales = [                
            { desc: "OPE.EXONERADA", imp: Number(datosVenta.venta.total).toFixed(2) },
            { desc: "OPE.INAFECTA", imp: 0.00 },
            { desc: "OPE.GRAVADA", imp: 0.00 },
            { desc: "IGV", imp: 0.00 },
            { desc: "IMPORTE TOTAL", imp: Number(datosVenta.venta.total).toFixed(2) }
        ];

        const datosTablaResumen = [
            [
                { content: '', rowSpan: 5, styles: { cellWidth: 32 } },
                "OPE.EXONERADA", 
                datosTablaTotales[0].imp
            ],
            ["OPE.INAFECTA", datosTablaTotales[1].imp.toFixed(2)],
            ["OPE.GRAVADA", datosTablaTotales[2].imp.toFixed(2)],
            ["IGV", datosTablaTotales[3].imp.toFixed(2)],
            ["IMPORTE TOTAL", datosTablaTotales[4].imp],
            [{ 
                content: "SON: " + datosVenta.numLetra, 
                colSpan: 3, 
                styles: { halign: 'left', fontStyle: 'bold' } 
            }]
        ];

        doc.autoTable({
            startY: finalY,
            body: datosTablaResumen,
            theme: 'grid',
            styles: {
                fontSize: 6.5,
                cellPadding: 1.5,
                textColor: [0, 0, 0]
            },
            bodyStyles: {
                fillColor: [255, 255, 255],
                textColor: [0, 0, 0],
                fontStyle: 'bold'
            },
            columnStyles: {
                0: { cellWidth: 32, halign: 'center' }, // QR: reducido de 35 a 32
                1: { cellWidth: 28, halign: 'left' },   // DESC: reducido de 25 a 28
                2: { cellWidth: 13, halign: 'right' }   // MONTO: reducido de 15 a 13
            },
            // Total: 32 + 28 + 13 = 73mm
            margin: { left: 2, right: 2 },
            /*didDrawCell: function (data) {
                if (data.row.index === 0 && data.column.index === 0) {
                    doc.addImage(qrImage1, 'PNG', data.cell.x + 1, data.cell.y + 1, 28, 28);
                }
            },*/
            tableWidth: 'auto'
        });

        const finalY2 = doc.lastAutoTable.finalY + 5;
        doc.setFont("helvetica", "bold");
        doc.setFontSize(7);
        doc.text("Este documento no tiene valor tributario", 
                 40, finalY2, { align: 'center', lineHeightFactor: 1.2 });
        
        doc.setFontSize(8);
        doc.text("CAJERO: " + (datosVenta.cajero).toUpperCase(), 2, finalY2 + 9);
        
        let medioPago = '';
        switch (datosVenta.venta.tipo_pago) {
            case '1': medioPago = "EFECTIVO"; break;
            case '2': medioPago = "PLIN"; break;
            case '3': medioPago = "YAPE"; break;
            case '4': medioPago = "TARJETA DEBITO"; break;
            case '5': medioPago = "TARJETA CREDITO"; break;    
            default: medioPago = "OTRO MEDIO DE PAGO"; break;
        }

        doc.text("PAGO " + medioPago + ": " + parseFloat(datosVenta.venta.total).toFixed(2), 2, finalY2 + 12);        

        const pdfData = doc.output('datauristring');

        Swal.fire({
            title: '<h5>Comprobante: ' + datosVenta.venta.serie + '-' + datosVenta.venta.comprobante + 
                   '  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha de Emisión: ' + fechaEmisionFormateada + ' '+ arrFecha[1] + '</h5>',
            html: `<iframe src="${pdfData}" width="100%" height="550px" style="border: none;"></iframe>`,
            showCloseButton: true,
            showConfirmButton: false,
            width: '60%'
        });
    });
});