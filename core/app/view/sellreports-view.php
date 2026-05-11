<?php
/**
 * Reporte de Ventas por Cliente - Versión Corporativa
 * CarePharm Modernized Report
 */

$clients = PersonData::getClients();

// Obtener parámetros de filtro
$client_id = $_GET["client_id"] ?? "";
$sd = $_GET["sd"] ?? date("Y-m-d");
$ed = $_GET["ed"] ?? date("Y-m-d");

$is_filtered = (isset($_GET["sd"]) && isset($_GET["ed"]));
$operations = array();

if ($is_filtered) {
    if ($client_id == "") {
        $operations = SellData::getAllByDateOp($sd, $ed, 2);
    } else {
        $operations = SellData::getAllByDateBCOp($client_id, $sd, $ed, 2);
    }
}

// Estadísticas rápidas
$total_v_neto = 0;
$total_v_desc = 0;
$total_v_final = 0;
$count_v = count($operations);

foreach ($operations as $op) {
    $total_v_neto += $op->total;
    $total_v_desc += $op->discount;
    $total_v_final += ($op->total - $op->discount);
}

$avg_ticket = ($count_v > 0) ? ($total_v_final / $count_v) : 0;
?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-chart-line text-info mr-2"></i> Reporte de Ventas por Cliente
                </h1>
                <p class="text-muted text-sm mb-0">Analice el comportamiento de compra de sus clientes en periodos específicos.</p>
            </div>
            <div class="col-sm-6 text-right">
                <ol class="breadcrumb float-sm-right bg-transparent p-0 mt-2">
                    <li class="breadcrumb-item"><a href="./?view=home"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item">Reportes</li>
                    <li class="breadcrumb-item active text-info">Venta x Cliente</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        
        <!-- Panel de Filtros -->
        <div class="card card-outline card-info shadow-sm mb-4">
            <div class="card-header py-2">
                <h3 class="card-title text-sm font-weight-bold uppercase"><i class="fas fa-filter mr-1"></i> Filtros de Búsqueda</h3>
            </div>
            <div class="card-body py-3">
                <form method="get" action="./">
                    <input type="hidden" name="view" value="sellreports">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="text-xs font-weight-bold text-muted mb-1 uppercase">Seleccionar Cliente</label>
                            <select name="client_id" class="form-control form-control-sm select2 shadow-none" style="width: 100%;">
                                <option value="">--- TODOS LOS CLIENTES ---</option>
                                <?php foreach ($clients as $p): ?>
                                    <option value="<?php echo $p->id; ?>" <?php echo ($client_id == $p->id) ? "selected" : ""; ?>>
                                        <?php echo $p->name . " " . $p->lastname . " - " . ($p->numero_documento ?? "S/D"); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="text-xs font-weight-bold text-muted mb-1 uppercase">Fecha Inicio</label>
                            <input type="date" name="sd" value="<?php echo $sd; ?>" class="form-control form-control-sm shadow-none">
                        </div>
                        <div class="col-md-2">
                            <label class="text-xs font-weight-bold text-muted mb-1 uppercase">Fecha Fin</label>
                            <input type="date" name="ed" value="<?php echo $ed; ?>" class="form-control form-control-sm shadow-none">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-info btn-sm btn-block shadow-sm font-weight-bold">
                                <i class="fas fa-sync-alt mr-1"></i> PROCESAR
                            </button>
                        </div>
                        <div class="col-md-2">
                            <?php if ($is_filtered): ?>
                            <a href="./?view=sellreports" class="btn btn-outline-secondary btn-sm btn-block">
                                <i class="fas fa-eraser mr-1"></i> LIMPIAR
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($is_filtered): ?>
            <!-- Widgets de Resumen -->
            <div class="row mb-4">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-white shadow-sm border-left border-info elevation-1 h-100">
                        <div class="inner">
                            <p class="text-muted text-xs uppercase font-weight-bold mb-1">Total Neto Facturado</p>
                            <h4 class="font-weight-bold">S/ <?php echo number_format($total_v_neto, 2); ?></h4>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill-alt text-info opacity-2"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-white shadow-sm border-left border-warning elevation-1 h-100">
                        <div class="inner">
                            <p class="text-muted text-xs uppercase font-weight-bold mb-1">Total Descuentos</p>
                            <h4 class="font-weight-bold text-warning">S/ <?php echo number_format($total_v_desc, 2); ?></h4>
                        </div>
                        <div class="icon"><i class="fas fa-percentage text-warning opacity-2"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-white shadow-sm border-left border-success elevation-1 h-100">
                        <div class="inner">
                            <p class="text-muted text-xs uppercase font-weight-bold mb-1">Ventas Reales (Final)</p>
                            <h4 class="text-success font-weight-bold">S/ <?php echo number_format($total_v_final, 2); ?></h4>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle text-success opacity-2"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-white shadow-sm border-left border-primary elevation-1 h-100">
                        <div class="inner">
                            <p class="text-muted text-xs uppercase font-weight-bold mb-1">Transacciones / Ticket Prom.</p>
                            <h4 class="text-primary font-weight-bold"><?php echo $count_v; ?> <small class="text-muted text-xs">/ S/ <?php echo number_format($avg_ticket, 2); ?></small></h4>
                        </div>
                        <div class="icon"><i class="fas fa-shopping-cart text-primary opacity-2"></i></div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Resultados -->
            <div class="card card-default shadow-sm elevation-1">
                <div class="card-header py-2 bg-light d-flex align-items-center">
                    <h3 class="card-title text-sm font-weight-bold uppercase mb-0">Listado de Operaciones</h3>
                    <div class="card-tools ml-auto">
                        <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (count($operations) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm table-valign-middle mb-0" id="table-sellreports">
                                <thead class="bg-light text-xs uppercase text-muted border-top-0">
                                    <tr>
                                        <th class="pl-4 py-3">Comprobante</th>
                                        <th class="py-3">Cliente</th>
                                        <th class="py-3">Fecha Emisión</th>
                                        <th class="text-right py-3">Subtotal</th>
                                        <th class="text-right py-3">Descuento</th>
                                        <th class="text-right pr-4 py-3">Total Final</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($operations as $op): 
                                        $client = $op->getPerson();
                                    ?>
                                    <tr class="text-sm">
                                        <td class="pl-4 font-weight-bold text-dark py-2">
                                            <a href="./?view=onesell&id=<?php echo $op->id; ?>" class="text-info">
                                                <?php echo $op->serie . "-" . $op->comprobante; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="text-muted"><?php echo $client ? ($client->name . " " . $client->lastname) : "---"; ?></span>
                                        </td>
                                        <td><?php echo date("d/m/Y H:i", strtotime($op->created_at)); ?></td>
                                        <td class="text-right font-weight-bold">S/ <?php echo number_format($op->total, 2); ?></td>
                                        <td class="text-right text-warning">-S/ <?php echo number_format($op->discount, 2); ?></td>
                                        <td class="text-right pr-4 font-weight-bold text-success">
                                            S/ <?php echo number_format($op->total - $op->discount, 2); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="bg-light font-weight-bold text-sm">
                                    <tr>
                                        <td colspan="3" class="text-right pl-4 py-3 uppercase">Totales Generales:</td>
                                        <td class="text-right py-3 text-dark">S/ <?php echo number_format($total_v_neto, 2); ?></td>
                                        <td class="text-right py-3 text-warning">-S/ <?php echo number_format($total_v_desc, 2); ?></td>
                                        <td class="text-right pr-4 py-3 text-success" style="font-size: 1.1rem;">S/ <?php echo number_format($total_v_final, 2); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-search-minus fa-4x text-muted opacity-2 mb-3"></i>
                            <h5 class="text-muted">No se encontraron resultados</h5>
                            <p class="text-xs text-muted mb-0">Intente ajustando el rango de fechas o seleccione otro cliente.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Estado Inicial: Sin filtros -->
            <div class="card shadow-none border bg-light">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-clipboard-check fa-5x text-info opacity-1"></i>
                    </div>
                    <h4 class="text-muted font-weight-bold">Analice sus ventas por cliente</h4>
                    <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
                        Seleccione un cliente específico (o déjelo en blanco para todos) y defina un rango de fechas para visualizar el resumen de facturación detallado.
                    </p>
                    <div class="d-flex justify-content-center">
                        <div class="bg-white p-2 rounded shadow-sm border d-flex align-items-center">
                            <i class="fas fa-info-circle text-info mr-2 ml-2"></i>
                            <span class="text-xs mr-2">Use el panel superior para empezar la consulta.</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<style>
    .opacity-1 { opacity: 0.1; }
    .opacity-2 { opacity: 0.2; }
    .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
    .table-valign-middle td { vertical-align: middle !important; }
    #table-sellreports_wrapper .dt-buttons { margin-bottom: 15px; }
</style>

<script>
    $(document).ready(function() {
        // Inicializar Select2 para clientes
        if($('.select2').length) {
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: "Buscar cliente..."
            });
        }

        // Inicializar DataTable profesional
        if ($('#table-sellreports').length) {
            $('#table-sellreports').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                },
                "pageLength": 50,
                "ordering": true,
                "responsive": true,
                "dom": '<"row d-flex justify-content-between mx-2 py-3"<"col-sm-6"B><"col-sm-6"f>>rtip',
                "buttons": [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel mr-1"></i> Excel',
                        className: 'btn btn-success btn-xs shadow-sm',
                        title: 'Reporte_Ventas_Cliente_<?php echo date("Ymd"); ?>'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf mr-1"></i> PDF',
                        className: 'btn btn-danger btn-xs shadow-sm',
                        orientation: 'landscape',
                        pageSize: 'A4'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print mr-1"></i> Imprimir',
                        className: 'btn btn-dark btn-xs shadow-sm'
                    }
                ]
            });
        }
    });
</script>