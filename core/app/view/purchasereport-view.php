<?php
$months_to_analyze = 4;
$months_to_provision = 2;

// Obtenemos los datos desde el modelo
$proposals = OperationData::getPurchaseProposal($months_to_analyze);
$data_report = [];

foreach($proposals as $r){
    $avg_monthly = $r->total_sold / $months_to_analyze;
    $provision_needed = $avg_monthly * $months_to_provision;
    $proposed_purchase = $provision_needed - $r->stock_actual;
    
    if($proposed_purchase < 0) $proposed_purchase = 0;

    $data_report[] = [
        "id" => $r->id,
        "name" => $r->producto,
        "stock" => $r->stock_actual,
        "total_sold" => $r->total_sold,
        "avg" => $avg_monthly,
        "proposal" => ceil($proposed_purchase)
    ];
}
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-shopping-cart text-success mr-2"></i> Propuesta de Compra Sugerida
                </h1>
                <p class="text-muted text-sm mb-0">Basado en ventas de los últimos <?=$months_to_analyze?> meses para cubrir los próximos <?=$months_to_provision?> meses.</p>
            </div>
            <div class="col-sm-6 text-right">
                <ol class="breadcrumb float-sm-right bg-transparent p-0 mt-2">
                    <li class="breadcrumb-item"><a href="./?view=home">Inicio</a></li>
                    <li class="breadcrumb-item">Reportes</li>
                    <li class="breadcrumb-item active text-success">Propuesta Compra</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header border-0 py-3">
                        <h3 class="card-title font-weight-bold uppercase text-sm">
                            <i class="fas fa-list mr-1"></i> Análisis de Reposición de Inventario
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-valign-middle mb-0" id="table-purchase-proposal">
                                <thead class="bg-light text-xs text-muted uppercase">
                                    <tr>
                                        <th class="pl-4 py-3" style="width: 50px;">N°</th>
                                        <th class="py-3">Producto</th>
                                        <th class="text-center py-3">Stock Actual</th>
                                        <th class="text-center py-3">Vendidos (<?=$months_to_analyze?> meses)</th>
                                        <th class="text-center py-3">Promedio Mensual</th>
                                        <th class="text-right pr-4 py-3 bg-light-success">Compra Propuesta (<?=$months_to_provision?> meses)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $n = 1;
                                    foreach($data_report as $item): 
                                        $stock_class = ($item['stock'] <= ($item['avg'] * 0.5)) ? 'text-danger font-weight-bold' : 'text-dark';
                                    ?>
                                    <tr class="text-sm">
                                        <td class="pl-4 py-2 text-muted"><?=$n++?></td>
                                        <td class="py-2">
                                            <span class="font-weight-bold text-primary"><?=$item['name']?></span>
                                        </td>
                                        <td class="text-center py-2 <?=$stock_class?>">
                                            <?=number_format($item['stock'], 0)?>
                                        </td>
                                        <td class="text-center py-2">
                                            <?=number_format($item['total_sold'], 0)?>
                                        </td>
                                        <td class="text-center py-2 text-muted">
                                            <?=number_format($item['avg'], 1)?>
                                        </td>
                                        <td class="text-right pr-4 py-2 bg-light" data-order="<?=$item['proposal']?>">
                                            <?php if($item['proposal'] > 0): ?>
                                                <span class="badge badge-success px-3 py-2 shadow-sm" style="font-size: 1rem;">
                                                    + <?=$item['proposal']?> <small>uds</small>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted"><i class="fas fa-check-circle text-gray mr-1"></i> Stock Suficiente</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<style>
    .bg-light-success { background-color: rgba(40, 167, 69, 0.05); }
</style>

<script>
    $(document).ready(function() {
        $('#table-purchase-proposal').DataTable({
            "language": {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sSearch":         "Buscar:",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     "Siguiente",
                    "sPrevious": "Anterior"
                }
            },
            "pageLength": 100,
            "order": [[5, "desc"]], // Ordenar por propuesta de compra descendente
            "dom": 'Bfrtip',
            "buttons": [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel mr-1"></i> Exportar a Excel',
                    className: 'btn btn-success btn-sm mb-3'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print mr-1"></i> Imprimir',
                    className: 'btn btn-info btn-sm mb-3'
                }
            ]
        });
    });
</script>
