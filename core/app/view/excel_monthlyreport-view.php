<?php 
header('Content-Type: application/vnd.ms-excel');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('content-disposition: attachment;filename=REPORTE_VENTAS_MENSUALES.xls');

$sd = isset($_GET['sd']) && $_GET['sd'] != "" ? $_GET['sd'] : null;
$ed = isset($_GET['ed']) && $_GET['ed'] != "" ? $_GET['ed'] : null;

$reports = OperationData::getMonthlySalesSummary($sd, $ed);
$details = OperationData::getMonthlySalesDetails($sd, $ed);
$meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
?>

<h3>RESUMEN DE VENTAS POR PRODUCTO (SOLO CON MOVIMIENTOS)</h3>
<?php if($sd && $ed): ?>
    <p>Rango de fechas: <?php echo $sd; ?> al <?php echo $ed; ?></p>
<?php endif; ?>

<table border="1">
    <thead>
        <tr style="background-color: #4f81bd; color: white;">
            <th>N°</th>
            <th>Producto</th>
            <th>Stock Actual</th>
            <th>Cantidad Vendida</th>
            <th>Monto Total (S/)</th>
            <th>Meses con Ventas</th>
            <th>Promedio Mensual (Cantidad)</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $nro = 1;
        foreach($reports as $report): 
        ?>
        <tr>
            <td><?php echo $nro++; ?></td>
            <td><?php echo $report->name; ?></td>
            <td><?php echo (int)$report->stock; ?></td>
            <td><?php echo (int)$report->total_qty; ?></td>
            <td><?php echo number_format($report->total_amount, 2, '.', ''); ?></td>
            <td><?php echo $report->months_list; ?> (<?php echo $report->total_months; ?>)</td>
            <td><?php echo (int)round($report->avg_qty_month); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<br>
<h3>DETALLE MENSUAL POR PRODUCTO</h3>
<table border="1">
    <thead>
        <tr style="background-color: #4f81bd; color: white;">
            <th>Año</th>
            <th>Mes</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Venta Total (S/)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($details as $detail): ?>
        <tr>
            <td><?php echo $detail->anio; ?></td>
            <td><?php echo $meses[$detail->mes]; ?></td>
            <td><?php echo $detail->producto; ?></td>
            <td><?php echo number_format($detail->cantidad_total, 2, '.', ''); ?></td>
            <td><?php echo number_format($detail->total_venta, 2, '.', ''); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
