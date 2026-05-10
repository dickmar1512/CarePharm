<?php
/**
 * CRON: Reporte Semanal de Ventas (7 Días + Gráfico)
 * Ejecutar: Todos los lunes a las 10:00 AM
 */

define("ROOT", dirname(__FILE__));
date_default_timezone_set("America/Lima");

include_once "core/autoload.php";
include_once "core/app/model/ProductData.php";
include_once "core/app/model/OperationData.php";
include_once "core/app/model/SellData.php";
include_once "core/app/model/UserData.php";
include_once "core/app/model/CLSPHPMailer.php";

Core::$root = "";

// Fechas: De Lunes a Domingo de la semana pasada
$sd = date('Y-m-d', strtotime('monday last week'));
$ed = date('Y-m-d', strtotime('sunday last week'));

// 1. Extraer los datos de la BD de los 7 días
$sql = "SELECT DATE(created_at) as dia, SUM(total) as total
        FROM sell
        WHERE operation_type_id = 2 AND tipo_comprobante != 70 AND estado = 1
        AND DATE(created_at) BETWEEN '$sd' AND '$ed'
        GROUP BY DATE(created_at)";

$query = Executor::doit($sql);
$data = [];
while($r = $query[0]->fetch_array()){
    $data[$r['dia']] = floatval($r['total']);
}

// Para calcular el crecimiento del primer día (Lunes), necesitamos el día anterior (Domingo previo)
$prevDay = date('Y-m-d', strtotime('-1 day', strtotime($sd)));
$sqlPrev = "SELECT SUM(total) as t FROM sell WHERE operation_type_id=2 AND tipo_comprobante!=70 AND estado=1 AND DATE(created_at) = '$prevDay'";
$qP = Executor::doit($sqlPrev);
$venta_anterior = 0;
if($rP = $qP[0]->fetch_array()){
    $venta_anterior = floatval($rP['t']);
}

$start = new DateTime($sd);
$end = new DateTime($ed);
$period = new DatePeriod($start, new DateInterval('P1D'), $end->modify('+1 day'));

$diasNombres = ['1'=>'Lunes', '2'=>'Martes', '3'=>'Miércoles', '4'=>'Jueves', '5'=>'Viernes', '6'=>'Sábado', '7'=>'Domingo'];

$cuadro = [];
$labels = [];
$ventasData = [];

$totalVentas = 0;
$totalCrecimiento = 0;
$sumaPorcentajes = 0;
$diasConCrec = 0;

foreach($period as $dt){
    $d = $dt->format('Y-m-d');
    $label = $diasNombres[$dt->format('N')] . ' ' . $dt->format('d/m');
    $venta = isset($data[$d]) ? $data[$d] : 0;
    
    $crec_soles = $venta_anterior > 0 ? $venta - $venta_anterior : 0;
    $crec_porc = 0;
    if($venta_anterior > 0) {
        $crec_porc = (($venta - $venta_anterior) / $venta_anterior) * 100;
    }
    
    $cuadro[] = [
        'dia' => $label,
        'venta' => $venta,
        'crec_soles' => $crec_soles,
        'crec_porc' => $crec_porc,
        'venta_ant' => $venta_anterior
    ];
    
    $labels[] = $label;
    $ventasData[] = round($venta, 2);
    
    $totalVentas += $venta;
    if ($crec_soles != 0 && $venta_anterior > 0) {
        $totalCrecimiento += $crec_soles;
        $sumaPorcentajes += $crec_porc;
        $diasConCrec++;
    }
    
    $venta_anterior = $venta;
}

$promedioPorc = $diasConCrec > 0 ? ($sumaPorcentajes / $diasConCrec) : 0;

// 2. Generar el Gráfico de Barras vía QuickChart (se adjuntará como imagen remota)
$chartConfig = [
    'type' => 'line',
    'data' => [
        'labels' => $labels,
        'datasets' => [
            [
                'label' => 'Ventas en Soles',
                'data' => $ventasData,
                'backgroundColor' => 'rgba(75, 192, 192, 0.4)',
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'borderWidth' => 2,
                'fill' => true,
                'tension' => 0.4
            ]
        ]
    ],
    'options' => [
        'title' => [ 'display' => true, 'text' => 'Ventas Diarias (Últimos 7 días)' ],
        'plugins' => [
            'datalabels' => [
                'display' => true,
                'anchor' => 'end',
                'align' => 'top'
            ]
        ]
    ]
];
$chartUrl = "https://quickchart.io/chart?w=600&h=300&c=" . urlencode(json_encode($chartConfig));

// 3. Preparar el HTML del correo
$arraddress = array('juan.irene@kalpg.com');
$arrAddcc = array('sagitatario.1982@gmail.com', 'mayaya.ocampo@gmail.com');
$asunto = "REPORTE DE VENTAS SEMANAL (" . date('d/m/Y', strtotime($sd)) . " - " . date('d/m/Y', strtotime($ed)) . ")";

$cuerpo = "<head><style>
    .mi-tabla { width: 100%; border-collapse: collapse; border: 1px solid #000; font-family: Arial, sans-serif; font-size: 13px; }
    .mi-tabla thead th { background-color: #343a40; color: #fff; font-weight: bold; padding: 8px; text-align: left; border: 1px solid #000; }
    .mi-tabla td { padding: 8px; border: 1px solid #000; }
    .mi-tabla tr:nth-child(even) { background-color: #f2f2f2; }
    .text-success { color: #28a745; font-weight: bold; }
    .text-danger { color: #dc3545; font-weight: bold; }
    .text-muted { color: #6c757d; }
    .text-right { text-align: right; }
    .font-weight-bold { font-weight: bold; }
    </style></head>";

$cuerpo .= "<h3>Reporte Comparativo Semanal</h3>";
$cuerpo .= "<p>Análisis de crecimiento de ventas día por día (Última semana).</p>";

// Gráfico
$cuerpo .= "<div style='text-align: center; margin-bottom: 20px;'>";
$cuerpo .= "<img src='{$chartUrl}' alt='Gráfico de Ventas' style='max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 4px; padding: 5px;'>";
$cuerpo .= "</div>";

// Tabla
$cuerpo .= "<table class='mi-tabla'>
    <thead>
        <tr>
            <th width='25%'>DÍA</th>
            <th width='25%' class='text-right'>VENTAS (SOLES)</th>
            <th width='25%' class='text-right'>CRECIMIENTO (SOLES)</th>
            <th width='25%' class='text-right'>CRECIMIENTO (%)</th>
        </tr>
    </thead>
    <tbody>";

foreach ($cuadro as $m) {
    // Formato de venta
    $venta_str = "S/ " . number_format($m['venta'], 2);
    
    // Formato Crecimiento Soles
    $crec_soles_str = "S/ -";
    if ($m['venta_ant'] > 0) {
        if ($m['crec_soles'] > 0) {
            $crec_soles_str = "<span class='text-success'>S/ " . number_format($m['crec_soles'], 2) . " ▲</span>";
        } else if ($m['crec_soles'] < 0) {
            $crec_soles_str = "<span class='text-danger'>S/ " . number_format($m['crec_soles'], 2) . " ▼</span>";
        } else {
            $crec_soles_str = "<span class='text-muted'>S/ 0.00</span>";
        }
    }

    // Formato Crecimiento Porcentaje
    $crec_porc_str = "0 %";
    if ($m['venta_ant'] > 0) {
        if ($m['crec_porc'] > 0) {
            $crec_porc_str = "<span class='text-success'>" . round($m['crec_porc'], 2) . "% ▲</span>";
        } else if ($m['crec_porc'] < 0) {
            $crec_porc_str = "<span class='text-danger'>" . round($m['crec_porc'], 2) . "% ▼</span>";
        }
    }

    $cuerpo .= "<tr>
        <td class='font-weight-bold'>{$m['dia']}</td>
        <td class='text-success text-right'>{$venta_str}</td>
        <td class='text-right'>{$crec_soles_str}</td>
        <td class='text-right'>{$crec_porc_str}</td>
    </tr>";
}

// Totales Footer
$cuerpo .= "</tbody>
    <tfoot>
        <tr style='background-color: #e9ecef;'>
            <td class='font-weight-bold'>TOTAL SEMANAL / PROMEDIO</td>
            <td class='font-weight-bold text-success text-right'>S/ " . number_format($totalVentas, 2) . "</td>
            <td class='font-weight-bold text-right'>";

if ($totalCrecimiento > 0) {
    $cuerpo .= "<span class='text-success'>S/ " . number_format($totalCrecimiento, 2) . " ▲</span>";
} else if ($totalCrecimiento < 0) {
    $cuerpo .= "<span class='text-danger'>S/ " . number_format($totalCrecimiento, 2) . " ▼</span>";
} else {
    $cuerpo .= "<span class='text-muted'>S/ -</span>";
}

$cuerpo .= "</td><td class='font-weight-bold text-right'>";

if ($promedioPorc > 0) {
    $cuerpo .= "<span class='text-success'>" . round($promedioPorc, 2) . "% ▲</span>";
} else if ($promedioPorc < 0) {
    $cuerpo .= "<span class='text-danger'>" . round($promedioPorc, 2) . "% ▼</span>";
} else {
    $cuerpo .= "<span class='text-muted'>0%</span>";
}

$cuerpo .= "</td>
        </tr>
    </tfoot>
</table>";

$firma = '<tr><td class="sub_pie">BOTICA ALFONZO UGARTE</td></tr>';
$firma .= '<tr><td class="sub_pie">botica.au@gmail.com</td></tr>';            

$mailer = new CLSPHPMailer();
$res = $mailer->fnMail($arraddress, $arrAddcc, $asunto, $cuerpo, 'pie', $firma, null);

if ($res) {
    echo "Reporte semanal enviado correctamente a las " . date('Y-m-d H:i:s') . "\n";
} else {
    echo "Fallo al enviar el reporte semanal a las " . date('Y-m-d H:i:s') . "\n";
}
?>
