<?php
/**
 * CRON: Reporte Mensual de Ventas
 * Ejecutar: Todos los 1ro de cada mes a las 11:00 AM
 * Comando sugerido en Programador de Tareas de Windows (o crontab):
 * c:\laragon\bin\php\php-8.x.x\php.exe c:\laragon\www\CarePharm\cron_reporte_mensual.php
 */

// Simular el entorno web de CarePharm
define("ROOT", dirname(__FILE__));
date_default_timezone_set("America/Lima");

include_once "core/autoload.php";
include_once "core/app/model/ProductData.php";
include_once "core/app/model/OperationData.php";
include_once "core/app/model/SellData.php";
include_once "core/app/model/UserData.php";
include_once "core/app/model/CLSPHPMailer.php";

Core::$root = "";

// Setear fechas del mes anterior completo (1ro del mes al ultimo dia del mes anterior)
$sd = date('Y-m-01', strtotime('last month'));
$ed = date('Y-m-t', strtotime('last month'));

$meses = ['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
$nombre_mes = $meses[date('m', strtotime($sd))] . ' ' . date('Y', strtotime($sd));

$ventas = OperationData::getSalesSummary($sd, $ed);

$arraddress = array();
$arrAddcc = array();
$arraddress[] = 'juan.irene@kalpg.com';
$arrAddcc[] = 'sagitatario.1982@gmail.com';
$arrAddcc[] = 'mayaya.ocampo@gmail.com';

$asunto = "REPORTE DE VENTAS MENSUAL ($nombre_mes)";

$cuerpo = "<head><style>
    .mi-tabla { width: 100%; border-collapse: collapse; border: 1px solid #000; font-family: Arial, sans-serif; }
    .mi-tabla thead { border-bottom: 2px solid white; }
    .mi-tabla th { background-color: #000; color: #fff; font-weight: bold; padding: 5px; text-align: left; border: 1px solid #000; }
    .mi-tabla td { font-weight: bold; padding: 10px; border: 1px solid #000; }
    .mi-tabla tr:nth-child(even) { background-color: #f2f2f2; }
    .text-right { text-align: right; }
    </style></head>";

$cuerpo .= "<h3>Reporte de Ventas del Mes de $nombre_mes ($sd al $ed)</h3>";
$cuerpo .= "<p>Adjunto el detalle de ventas acumuladas del mes anterior.</p>";

$cuerpo .= "<table class='mi-tabla'>
    <thead>
        <tr>
            <th>Producto</th>
            <th class='text-right'>Cantidad Vendida</th>
            <th class='text-right'>Venta Total (Soles)</th>
        </tr>
    </thead>
    <tbody>";

$total_qty = 0;
$total_monto = 0;

if (count($ventas) > 0) {
    foreach ($ventas as $v) {
        $cuerpo .= "<tr>
            <td>".$v['producto']."</td>
            <td class='text-right'>".number_format($v['qty'], 0)."</td>
            <td class='text-right'>S/ ".number_format($v['total'], 2)."</td>
        </tr>";
        $total_qty += $v['qty'];
        $total_monto += $v['total'];
    }
} else {
    $cuerpo .= "<tr><td colspan='3' style='text-align:center;'>No hubo ventas registradas en este mes.</td></tr>";
}

$cuerpo .= "</tbody>
    <tfoot>
        <tr>
            <th style='text-align: right;'>TOTALES MENSUALES:</th>
            <th class='text-right'>".number_format($total_qty, 0)."</th>
            <th class='text-right'>S/ ".number_format($total_monto, 2)."</th>
        </tr>
    </tfoot>
    </table>";

$firma = '<tr><td class="sub_pie">BOTICA ALFONZO UGARTE</td></tr>';
$firma .= '<tr><td class="sub_pie">botica.au@gmail.com</td></tr>';            

$mailer = new CLSPHPMailer();
$res = $mailer->fnMail($arraddress, $arrAddcc, $asunto, $cuerpo, 'pie', $firma, null);

if ($res) {
    echo "Reporte mensual enviado correctamente a las " . date('Y-m-d H:i:s') . "\n";
} else {
    echo "Fallo al enviar el reporte mensual a las " . date('Y-m-d H:i:s') . "\n";
}
?>
