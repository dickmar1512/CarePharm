<?php
/**
 * CRON: Reporte Mensual de Ventas (12 Meses + Gráfico)
 * Ejecutar: Todos los 1ro de cada mes a las 11:00 AM
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

// Fechas: Obtener el mes anterior completo como mes FINAL
$endObj = date('Y-m-t', strtotime('last month'));
// El mes de inicio será 11 meses atrás del mes final (para tener 12 meses en total)
$startObj = date('Y-m-01', strtotime('-11 months', strtotime(date('Y-m-01', strtotime('last month')))));

// 1. Extraer los datos de la BD de los 12 meses
$sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as mes, SUM(total) as total
        FROM sell
        WHERE operation_type_id = 2 AND tipo_comprobante != 70 AND estado = 1
        AND DATE(created_at) BETWEEN '$startObj' AND '$endObj'
        GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY created_at DESC";

$query = Executor::doit($sql);
$data = [];
while($r = $query[0]->fetch_array()){
    $data[$r['mes']] = floatval($r['total']);
}

// Para calcular el crecimiento del primer mes, necesitamos el mes anterior a startObj
$prevMonth = date('Y-m', strtotime('-1 month', strtotime($startObj)));
$sqlPrev = "SELECT SUM(total) as t FROM sell WHERE operation_type_id=2 AND tipo_comprobante!=70 AND estado=1 AND DATE_FORMAT(created_at, '%Y-%m') = '$prevMonth'";
$qP = Executor::doit($sqlPrev);
$venta_anterior = 0;
if($rP = $qP[0]->fetch_array()){
    $venta_anterior = floatval($rP['t']);
}

$start = new DateTime($startObj);
$end = new DateTime($endObj);
$period = new DatePeriod($start, new DateInterval('P1M'), $end->modify('+1 day'));

$mesesNombres = ['01'=>'Ene', '02'=>'Feb', '03'=>'Mar', '04'=>'Abr', '05'=>'May', '06'=>'Jun', '07'=>'Jul', '08'=>'Ago', '09'=>'Set', '10'=>'Oct', '11'=>'Nov', '12'=>'Dic'];

$cuadro = [];
$labels = [];
$ventasData = [];

$totalVentas = 0;
$totalCrecimiento = 0;
$sumaPorcentajes = 0;
$mesesConCrec = 0;

foreach($period as $dt){
    $m = $dt->format('Y-m');
    $label = $mesesNombres[$dt->format('m')] . ' ' . $dt->format('y');
    $venta = isset($data[$m]) ? $data[$m] : 0;
    
    $crec_soles = $venta_anterior > 0 ? $venta - $venta_anterior : 0;
    $crec_porc = 0;
    if($venta_anterior > 0) {
        $crec_porc = (($venta - $venta_anterior) / $venta_anterior) * 100;
    }
    
    $cuadro[] = [
        'mes' => $label,
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
        $mesesConCrec++;
    }
    
    $venta_anterior = $venta;
}

$promedioPorc = $mesesConCrec > 0 ? ($sumaPorcentajes / $mesesConCrec) : 0;

// 2. Generar el Gráfico de Barras vía QuickChart (se adjuntará como imagen remota)
$chartConfig = [
    'type' => 'bar',
    'data' => [
        'labels' => $labels,
        'datasets' => [
            [
                'label' => 'Ventas en Soles',
                'data' => $ventasData,
                'backgroundColor' => 'rgba(54, 162, 235, 0.8)',
                'borderColor' => 'rgba(54, 162, 235, 1)',
                'borderWidth' => 1
            ]
        ]
    ],
    'options' => [
        'title' => [ 'display' => true, 'text' => 'Ventas Mensuales (Ultimos 12 meses)' ],
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
$asunto = "REPORTE DE VENTAS MENSUALES COMPARATIVO (Ultimos 12 Meses)";

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

$cuerpo .= "<h3>Reporte Comparativo de Ingresos</h3>";
$cuerpo .= "<p>Análisis de crecimiento mensual de ventas (12 meses móviles).</p>";

// Gráfico
$cuerpo .= "<div style='text-align: center; margin-bottom: 20px;'>";
$cuerpo .= "<img src='{$chartUrl}' alt='Gráfico de Ventas' style='max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 4px; padding: 5px;'>";
$cuerpo .= "</div>";

// Tabla
$cuerpo .= "<table class='mi-tabla'>
    <thead>
        <tr>
            <th width='25%'>MES</th>
            <th width='25%' class='text-right'>VENTAS (SOLES)</th>
            <th width='25%' class='text-right'>CRECIMIENTO (SOLES)</th>
            <th width='25%' class='text-right'>CRECIMIENTO (%)</th>
        </tr>
    </thead>
    <tbody>";
// Invertimos el orden para que el mes más actual aparezca primero en la tabla
$cuadro_descendente = array_reverse($cuadro);

foreach ($cuadro_descendente as $m) {
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
        <td class='font-weight-bold'>{$m['mes']}</td>
        <td class='text-success text-right'>{$venta_str}</td>
        <td class='text-right'>{$crec_soles_str}</td>
        <td class='text-right'>{$crec_porc_str}</td>
    </tr>";
}

// Totales Footer
$cuerpo .= "</tbody>
    <tfoot>
        <tr style='background-color: #e9ecef;'>
            <td class='font-weight-bold'>TOTALES / PROMEDIO</td>
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
    echo "Reporte comparativo enviado correctamente a las " . date('Y-m-d H:i:s') . "\n";
} else {
    echo "Fallo al enviar el reporte comparativo a las " . date('Y-m-d H:i:s') . "\n";
}
?>
