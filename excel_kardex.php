<?php
    include('funciones.php'); 
    //echo "Aqui==>".json_encode( $_POST);
    $id_producto = $_POST["id_producto"];
    $selMes = $_POST["selMes"];
    $selAnio = $_POST["selAnio"];
    $producto = get_producto($enlace, $id_producto);

    require_once 'lib/PHPExcel/PHPExcel.php';

   // header('Content-Type: application/vnd.ms-excel');
   // header('Content-Disposition: attachment;filename="kardex.xls"');

    $excel = new PHPExcel();

    $excel->getProperties()->setCreator('TARO')->setLastModifiedBy('Admin')->setTitle('kardex');

    $excel->setActiveSheetIndex(0);

    $pagina = $excel->getActiveSheet();

    $pagina->setTitle('REGISTRO DE INVENTARIO');

    $pagina->setCellValue('D1', 'FORMATO 13.1: REGISTRO DE INVENTARIO PERMANENTE VALORIZADO - DETALLE DEL INVENTARIO VALORIZADO');
    $pagina->mergeCells('D1:L1');

    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

    $data['meses'] = array('', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre');

    $pagina->setCellValue('A3', 'PERIODO:');
    $pagina->setCellValue('B3', $data['meses'][$selMes].'-'.$selAnio);

    $pagina->setCellValue('A4', 'RUC:');
    $pagina->setCellValue('B4', '20603463472');

    $pagina->setCellValue('A5', 'NOMBRE COMERCIAL:');
    $pagina->setCellValue('B5', "D' TODO CERÁMICA");

    $pagina->setCellValue('A6', 'ESTABLECIMIENTO:');
    $pagina->setCellValue('B6', '');

    $pagina->setCellValue('A7', 'CÓDIGO DEL PRODUCTO:');
    $pagina->setCellValue('B7', $id_producto);

    $pagina->setCellValue('A7', 'TIPO:');
    $pagina->setCellValue('B7', '01 MERCADERÍA');

    $pagina->setCellValue('A8', 'DESCRIPCIÓN:');
    $pagina->setCellValue('B8', $producto['name']);

    $pagina->setCellValue('A9', 'METODO DE VALUACIÓN:');
    $pagina->setCellValue('B9', "Promedio");

    $pagina->setCellValue('A13', 'DOCUMENTO DE TRASLADO, COMPROBANTE DE PAGO, DOCUMENTO INTERNO O SIMILAR');
    $pagina->mergeCells('A13:D13');
    $pagina->setCellValue('A14', "FECHA");
    $pagina->setCellValue('B14', "TIPO");
    $pagina->setCellValue('C14', "SERIE");
    $pagina->setCellValue('D14', "NÚMERO");

    $pagina->setCellValue('E13', "TIPO DE OPERACIÓN");
    $pagina->mergeCells('E13:E14');

    $pagina->setCellValue('F13', "ENTRADAS");
    $pagina->mergeCells('F13:H13');
    $pagina->setCellValue('F14', "CANTIDAD");
    $pagina->setCellValue('G14', "COSTO UNITARIO");
    $pagina->setCellValue('H14', "COSTO TOTAL");

    $pagina->setCellValue('I13', "SALIDAS");
    $pagina->mergeCells('I13:K13');
    $pagina->setCellValue('I14', "CANTIDAD");
    $pagina->setCellValue('J14', "COSTO UNITARIO");
    $pagina->setCellValue('K14', "COSTO TOTAL");

    $pagina->setCellValue('L13', "SALDO FINAL");
    $pagina->mergeCells('L13:N13');
    $pagina->setCellValue('L14', "CANTIDAD");
    $pagina->setCellValue('M14', "COSTO UNITARIO");
    $pagina->setCellValue('N14', "COSTO TOTAL");

    $condicion_inventario_inicial = "AND MONTH(ope.created_at) = $selMes and YEAR(ope.created_at) = $selAnio";

    //inventario inicial
    $inventario_inicial = inventario_inicial($enlace, $id_producto, $condicion_inventario_inicial);
    
    $cantidad_inventario_inicial = $inventario_inicial[0]['suma'] - $inventario_inicial[1]['suma'];

    $excel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, 15, $cantidad_inventario_inicial);
    $excel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12, 15, $producto['price_out']);
    $excel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13, 15, $producto['price_out']*$cantidad_inventario_inicial);

    $fila = 16;

    $condicion_operaciones = "AND MONTH(ope.created_at) = $selMes AND YEAR(ope.created_at) = $selAnio";

    $operaciones = get_operaciones($enlace, $id_producto, $condicion_operaciones);

    $cantidad_sf = $cantidad_inventario_inicial;

    foreach ($operaciones as $ope)
    {
        $cantidad = $ope['q'];
        $precio_unitario = $ope['prec_alt'];
        $precio_total = $cantidad*$precio_unitario;

        $excel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, $fila, convertir_fecha($ope['created_at']));
        $excel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, '0'.$ope['tipo_comprobante']);
        $excel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, $ope['serie']);
        $excel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, $ope['comprobante']);

        if ($ope['operation_type_id'] == 1) 
        {
            $excel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, '02');
        }
        else
        {
            $excel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, '01');
        }

        if ($ope['operation_type_id'] == 1)
        {
            $excel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, $cantidad);
            $excel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, $precio_unitario);
            $excel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $fila, $precio_total);

            
            $cantidad_sf = $cantidad_sf + $cantidad;
            
        }
        else if($ope['operation_type_id'] == 2)
        {
            $excel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, $fila, $cantidad);
            $excel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, $fila, $precio_unitario);
            $excel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, $fila, $precio_total);

            $cantidad_sf = $cantidad_sf - $cantidad;
        }

        $excel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, $fila, $cantidad_sf);        
        $excel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12, $fila, $ope['prec_alt']);        
        $excel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13, $fila, $cantidad_sf*$ope['prec_alt']);        
        

        $fila++;
    }

    $objWriter->save('php://output');

?>