<?php
/**
 * CRON: Reporte de Productos Vencidos y Por Vencer
 * -------------------------------------------------------
 * Lógica de vencimiento idéntica a searchproduct-action.php:
 *   - Vencido   : días_restantes <= 0
 *   - Crítico   : 1 – 90 días
 *   - Próximo   : 91 – 120 días
 *   - Vigente   : > 120 días
 *
 * Programación cron sugerida (Linux/cPanel):
 *   0 8 15 * *         php /ruta/al/proyecto/cron_reporte_vencimientos.php
 *   0 8 28-31 * *      php /ruta/al/proyecto/cron_reporte_vencimientos.php
 *
 * El script detecta si hoy es el último día del mes y se detiene
 * si no corresponde (días 28–31 que no sean el último día real).
 *
 * En Windows (Programador de tareas) apuntar a este archivo.
 */

define("ROOT", dirname(__FILE__));
date_default_timezone_set("America/Lima");

// ── Verificar que hoy es día 15 O último día del mes ─────────────────────────
$hoy        = (int) date('j');               // día del mes (sin cero inicial)
$ultimo_dia = (int) date('t');               // total de días del mes actual

$es_dia_15     = ($hoy === 15);
$es_ultimo_dia = ($hoy === $ultimo_dia);

if (!$es_dia_15 && !$es_ultimo_dia) {
    echo "Hoy (" . date('d/m/Y') . ") no corresponde ejecutar el reporte. Se requiere día 15 o último día del mes.\n";
    exit(0);
}

// ── Dependencias del proyecto ────────────────────────────────────────────────
include_once "core/autoload.php";
include_once "core/app/model/ProductData.php";
include_once "core/app/model/OperationData.php";
include_once "core/app/model/SellData.php";
include_once "core/app/model/UserData.php";
include_once "core/app/model/CLSPHPMailer.php";
require_once "plugins/fpdf/fpdf.php";

Core::$root = "";

// Asegurar carpeta de temporales
if (!file_exists(ROOT . "/storage")) {
    mkdir(ROOT . "/storage", 0777, true);
}

// ── Función: calcula estado de vencimiento (igual que searchproduct-action) ──
function calcularEstadoVenc($fecha_venc_raw) {
    if (empty($fecha_venc_raw)) {
        return [
            'dias'           => PHP_INT_MAX,
            'estado'         => 'sin_fecha',
            'fecha_fmt'      => '—',
            'dias_txt'       => '—',
        ];
    }
    $ts             = strtotime($fecha_venc_raw);
    $dias           = (int) ceil(($ts - time()) / 86400);
    $fecha_fmt      = date('d/m/Y', $ts);

    if ($dias <= 0) {
        $estado  = 'vencido';
        $dias_txt = 'Vencido hace ' . abs($dias) . ' días';
    } elseif ($dias <= 90) {
        $estado  = 'critico';
        $dias_txt = 'Faltan ' . $dias . ' días';
    } elseif ($dias <= 120) {
        $estado  = 'proximo';
        $dias_txt = 'Faltan ' . $dias . ' días';
    } else {
        $estado  = 'vigente';
        $dias_txt = 'Faltan ' . $dias . ' días';
    }

    return compact('dias', 'estado', 'fecha_fmt', 'dias_txt');
}

// ── Cargar y clasificar todos los productos activos ──────────────────────────
$todos_productos = ProductData::getAll2();

$vencidos  = [];
$criticos  = [];
$proximos  = [];

foreach ($todos_productos as $p) {
    $info = calcularEstadoVenc($p->fecha_venc);
    $p->_v = $info;
    switch ($info['estado']) {
        case 'vencido': $vencidos[] = $p; break;
        case 'critico': $criticos[] = $p; break;
        case 'proximo': $proximos[] = $p; break;
    }
}

// Ordenar cada grupo por días restantes (más urgente primero)
$ordenar = function($a, $b) {
    $da = ($a->_v['dias'] === PHP_INT_MAX) ? 99999 : $a->_v['dias'];
    $db = ($b->_v['dias'] === PHP_INT_MAX) ? 99999 : $b->_v['dias'];
    return $da <=> $db;
};
usort($vencidos, $ordenar);
usort($criticos, $ordenar);
usort($proximos, $ordenar);

$total_alertas = count($vencidos) + count($criticos) + count($proximos);

echo "=== CRON VENCIMIENTOS [" . date('d/m/Y H:i') . "] ===\n";
echo "Vencidos: " . count($vencidos) . " | Críticos: " . count($criticos) . " | Próximos: " . count($proximos) . "\n";
echo "Total con alerta: $total_alertas\n";

// ── Generar PDF con FPDF ──────────────────────────────────────────────────────
$pdf = new FPDF();
$pdf->SetMargins(12, 12, 12);

// ---- Portada / resumen ----
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetFillColor(30, 60, 114);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(0, 14, iconv('UTF-8','windows-1252',"REPORTE DE VENCIMIENTOS DE PRODUCTOS"), 0, 1, 'C', true);

$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 8, iconv('UTF-8','windows-1252',"Generado el: " . date('d/m/Y H:i')), 0, 1, 'C');
$pdf->Ln(4);

// Resumen estadístico
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 9, iconv('UTF-8','windows-1252',"Resumen de Alertas"), 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);

// Caja Vencidos
$pdf->SetFillColor(220, 53, 69);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(60, 10, iconv('UTF-8','windows-1252',"Vencidos"), 1, 0, 'C', true);
$pdf->SetFillColor(253, 232, 232);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(20, 10, (string)count($vencidos), 1, 0, 'C', true);

// Caja Críticos
$pdf->SetFillColor(220, 53, 69);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(60, 10, iconv('UTF-8','windows-1252',"Críticos (≤90 días)"), 1, 0, 'C', true);
$pdf->SetFillColor(253, 232, 232);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(20, 10, (string)count($criticos), 1, 0, 'C', true);

// Caja Próximos
$pdf->SetFillColor(217, 164, 6);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(0, 10, iconv('UTF-8','windows-1252',"Próximos (91–120 días)"), 1, 0, 'C', true);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln();

// Contador total
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(52, 58, 64);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(0, 9, iconv('UTF-8','windows-1252',"TOTAL CON ALERTA: $total_alertas productos"), 1, 1, 'C', true);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(5);

// ---- Función auxiliar para tabla de grupo ----
function pdfTablaGrupo(FPDF &$pdf, array $lista, string $titulo, array $fill_rgb) {
    if (empty($lista)) {
        return;
    }
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor($fill_rgb[0], $fill_rgb[1], $fill_rgb[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 10, iconv('UTF-8','windows-1252', $titulo . " (" . count($lista) . " productos)"), 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);

    // Encabezado
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(52, 58, 64);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(8,  8, '#',              1, 0, 'C', true);
    $pdf->Cell(65, 8, 'PRODUCTO',       1, 0, 'L', true);
    $pdf->Cell(42, 8, 'LABORATORIO',    1, 0, 'L', true);
    $pdf->Cell(18, 8, 'STOCK',          1, 0, 'C', true);
    $pdf->Cell(28, 8, 'F.VENCIMIENTO',  1, 0, 'C', true);
    $pdf->Cell(0,  8, iconv('UTF-8','windows-1252','DÍAS RESTANTES'), 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(0, 0, 0);

    $i = 1;
    foreach ($lista as $p) {
        $v   = $p->_v;
        $bg  = ($i % 2 === 0);
        if ($bg) {
            $pdf->SetFillColor(245, 245, 245);
        } else {
            $pdf->SetFillColor(255, 255, 255);
        }
        $nombre = iconv('UTF-8','windows-1252', mb_strimwidth($p->name ?? '', 0, 40, '…'));
        $lab    = iconv('UTF-8','windows-1252', mb_strimwidth($p->laboratorio ?? '—', 0, 24, '…'));
        $stock  = ($p->is_stock == 0) ? 'Ilimitado' : (string)(int)$p->stock;
        $fv     = iconv('UTF-8','windows-1252', $v['fecha_fmt']);
        $dt     = iconv('UTF-8','windows-1252', $v['dias_txt']);

        $pdf->Cell(8,  7, (string)$i,  1, 0, 'C', $bg);
        $pdf->Cell(65, 7, $nombre,     1, 0, 'L', $bg);
        $pdf->Cell(42, 7, $lab,        1, 0, 'L', $bg);
        $pdf->Cell(18, 7, $stock,      1, 0, 'C', $bg);
        $pdf->Cell(28, 7, $fv,         1, 0, 'C', $bg);
        $pdf->Cell(0,  7, $dt,         1, 1, 'C', $bg);
        $i++;
    }
    $pdf->Ln(5);
}

// ---- Sección Vencidos ----
if (!empty($vencidos)) {
    pdfTablaGrupo($pdf, $vencidos, "PRODUCTOS VENCIDOS", [180, 30, 45]);
}

// ---- Sección Críticos ----
if (!empty($criticos)) {
    // Salto de página si quedan menos de 50mm
    if ($pdf->GetY() > 220) { $pdf->AddPage(); }
    pdfTablaGrupo($pdf, $criticos, "CRÍTICOS — Vencen en ≤ 90 días", [200, 60, 40]);
}

// ---- Sección Próximos ----
if (!empty($proximos)) {
    if ($pdf->GetY() > 220) { $pdf->AddPage(); }
    pdfTablaGrupo($pdf, $proximos, "PRÓXIMOS — Vencen en 91–120 días", [180, 130, 10]);
}

$pdfPath = ROOT . "/storage/Reporte_Vencimientos_" . date('Y-m-d') . ".pdf";
$pdf->Output('F', $pdfPath);
echo "PDF generado: $pdfPath\n";

// ── Generar HTML del correo ───────────────────────────────────────────────────
$tipo_ejecucion = $es_dia_15 ? 'Quincena (día 15)' : 'Cierre de Mes (último día)';

// Función para construir tabla HTML de un grupo
function htmlTablaGrupo(array $lista, string $titulo, string $color_cabecera, string $color_fila) {
    if (empty($lista)) return '';

    $html  = "<h3 style='color:{$color_cabecera}; margin: 20px 0 8px;'>{$titulo} (" . count($lista) . " productos)</h3>";
    $html .= "<table style='width:100%;border-collapse:collapse;font-family:Arial,sans-serif;font-size:13px;margin-bottom:10px;'>";
    $html .= "<thead>
        <tr style='background:#343a40;color:#fff;'>
            <th style='padding:8px;border:1px solid #ccc;width:4%;'>#</th>
            <th style='padding:8px;border:1px solid #ccc;text-align:left;width:32%;'>PRODUCTO</th>
            <th style='padding:8px;border:1px solid #ccc;text-align:left;width:22%;'>LABORATORIO</th>
            <th style='padding:8px;border:1px solid #ccc;text-align:center;width:10%;'>STOCK</th>
            <th style='padding:8px;border:1px solid #ccc;text-align:center;width:14%;'>F. VENCIMIENTO</th>
            <th style='padding:8px;border:1px solid #ccc;text-align:center;width:18%;'>DÍAS RESTANTES</th>
        </tr>
    </thead><tbody>";

    $i = 1;
    foreach ($lista as $p) {
        $v     = $p->_v;
        $bg    = ($i % 2 === 0) ? '#f9f9f9' : '#ffffff';
        $stock = ($p->is_stock == 0) ? '<span style="color:#28a745;">∞</span>' : (string)(int)$p->stock;
        $nombre= htmlspecialchars($p->name ?? '');
        $lab   = htmlspecialchars($p->laboratorio ?? '—');
        $fv    = htmlspecialchars($v['fecha_fmt']);
        $dt    = htmlspecialchars($v['dias_txt']);

        $html .= "<tr style='background:{$color_fila};'>
            <td style='padding:7px 5px;border:1px solid #ddd;text-align:center;color:#555;'>{$i}</td>
            <td style='padding:7px 8px;border:1px solid #ddd;font-weight:bold;'>{$nombre}</td>
            <td style='padding:7px 8px;border:1px solid #ddd;font-size:12px;'>{$lab}</td>
            <td style='padding:7px 5px;border:1px solid #ddd;text-align:center;'>{$stock}</td>
            <td style='padding:7px 5px;border:1px solid #ddd;text-align:center;font-weight:bold;'>{$fv}</td>
            <td style='padding:7px 5px;border:1px solid #ddd;text-align:center;background:{$color_fila};color:#c0392b;font-weight:bold;'>{$dt}</td>
        </tr>";
        // Alternar color fondo
        $color_fila = ($color_fila === '#ffffff') ? '#fff5f5' : '#ffffff';
        $i++;
    }
    $html .= "</tbody></table>";
    return $html;
}

$cuerpo  = "<head><style>
    body { font-family: Arial, sans-serif; font-size: 14px; }
    .resumen-box { display:inline-block; padding:14px 24px; border-radius:8px; text-align:center; margin:0 6px; }
</style></head>";

$cuerpo .= "<h2 style='color:#1e3c72; margin-bottom:4px;'>&#128197; Reporte de Vencimientos de Productos</h2>";
$cuerpo .= "<p style='color:#555; margin-top:0;'>
    <strong>Tipo:</strong> {$tipo_ejecucion} &nbsp;|&nbsp;
    <strong>Generado:</strong> " . date('d/m/Y H:i') . "
</p>";
$cuerpo .= "<p>Se adjunta el PDF con el detalle completo. A continuación el resumen del reporte:</p>";

// Cajas resumen
$cuerpo .= "<div style='margin:18px 0; text-align:center;'>";
$cuerpo .= "<span class='resumen-box' style='background:#dc3545;color:#fff;'>
    <strong style='font-size:2rem;'>" . count($vencidos) . "</strong><br>VENCIDOS</span>";
$cuerpo .= "<span class='resumen-box' style='background:#fde8e8;color:#c0392b;border:2px solid #e74c3c;'>
    <strong style='font-size:2rem;'>" . count($criticos) . "</strong><br>CRÍTICOS<br><small>≤ 90 días</small></span>";
$cuerpo .= "<span class='resumen-box' style='background:#fff3cd;color:#856404;border:2px solid #ffc107;'>
    <strong style='font-size:2rem;'>" . count($proximos) . "</strong><br>PRÓXIMOS<br><small>91–120 días</small></span>";
$cuerpo .= "<span class='resumen-box' style='background:#343a40;color:#fff;'>
    <strong style='font-size:2rem;'>{$total_alertas}</strong><br>TOTAL ALERTA</span>";
$cuerpo .= "</div>";

if ($total_alertas === 0) {
    $cuerpo .= "<p style='background:#d4edda;color:#155724;padding:14px;border-radius:6px;'>
        &#10003; <strong>¡Sin alertas!</strong> Todos los productos están vigentes con más de 120 días de anticipación.
    </p>";
} else {
    $cuerpo .= htmlTablaGrupo($vencidos, '&#128128; Productos VENCIDOS',     '#dc3545', '#fff5f5');
    $cuerpo .= htmlTablaGrupo($criticos, '&#9888;&#65039; CRÍTICOS (≤ 90 días)', '#c0392b', '#fff5f5');
    $cuerpo .= htmlTablaGrupo($proximos, '&#128336; PRÓXIMOS (91–120 días)', '#856404', '#fffdf0');
}

$cuerpo .= "<p style='margin-top:20px; font-size:12px; color:#888;'>
    Este reporte se genera automáticamente el día 15 y el último día de cada mes.
</p>";

// ── Enviar correo ─────────────────────────────────────────────────────────────
$arraddress = ['juan.irene@kalpg.com'];
$arrAddcc   = ['sagitatario.1982@gmail.com', 'mayaya.ocampo@gmail.com'];
$asunto     = "REPORTE DE VENCIMIENTOS [" . date('d/m/Y') . "] — {$tipo_ejecucion} — {$total_alertas} producto(s) con alerta";

$firma  = '<tr><td class="sub_pie">BOTICA ALFONZO UGARTE</td></tr>';
$firma .= '<tr><td class="sub_pie">botica.au@gmail.com</td></tr>';

$mailer  = new CLSPHPMailer();
$atachar = [$pdfPath => "Reporte_Vencimientos_" . date('Y-m-d') . ".pdf"];
$res     = $mailer->fnMail($arraddress, $arrAddcc, $asunto, $cuerpo, 'pie', $firma, $atachar);

if ($res) {
    echo "Correo enviado correctamente a: " . implode(', ', array_merge($arraddress, $arrAddcc)) . "\n";
} else {
    echo "ERROR: Falló el envío del correo.\n";
}

// Limpiar PDF temporal si se desea (comentado para conservar histórico)
// unlink($pdfPath);

echo "=== FIN [" . date('H:i:s') . "] ===\n";
?>
