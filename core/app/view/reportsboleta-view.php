<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><i class="fas fa-file-invoice mr-2"></i> Reporte de Boletas (Notas de Crédito)</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="./?view=home">Inicio</a></li>
                    <li class="breadcrumb-item active">Reporte Boletas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-info shadow-sm">
            <div class="card-header bg-light">
                <h3 class="card-title text-bold"><i class="fas fa-filter mr-1"></i> Filtros de búsqueda</h3>
            </div>
            <div class="card-body p-3">
                <form method="get">
                    <input type="hidden" name="view" value="reportsboleta">
                    <div class="row align-items-end">
                        <?php $series = Boleta2Data::get_series_notas_credito(); ?>
                        <div class="col-md-2">
                            <label class="small text-muted mb-1">Serie</label>
                            <select name="selSerie" class="form-control form-control-sm">
                                <option value="0">Todas las series</option>
                                <?php foreach ($series as $serie): ?>
                                    <option value="<?php echo $serie->SERIE?>" <?php echo (isset($_GET['selSerie']) && $_GET['selSerie'] == $serie->SERIE) ? 'selected' : ''; ?>><?php echo $serie->SERIE?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted mb-1">N° Comprobante</label>
                            <input type="text" name="comprobante" value="<?php echo $_GET['comprobante'] ?? ''; ?>" placeholder="Ej: 00000123" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted mb-1">Fecha Inicio</label>
                            <input type="date" name="sd" value="<?php echo $_GET["sd"] ?? date("Y-m-d"); ?>" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted mb-1">Fecha Fin</label>
                            <input type="date" name="ed" value="<?php echo $_GET["ed"] ?? date("Y-m-d"); ?>" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-info btn-sm btn-block shadow-sm">
                                <i class="fas fa-sync-alt mr-1"></i> Procesar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if(isset($_GET["sd"]) && isset($_GET["ed"])): ?>
            <?php 
                $boletas = Boleta2Data::get_boletas_x_fecha($_GET["sd"], $_GET["ed"], $_GET["selSerie"] ?? '0', $_GET["comprobante"] ?? '');
            ?>
            <div class="card shadow-sm mt-3">
                <div class="card-header border-0 d-flex align-items-center">
                    <h3 class="card-title text-bold"><i class="fas fa-list mr-1"></i> Resultados del Reporte</h3>
                </div>
                <div class="card-body p-0">
                    <?php if(count($boletas) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-striped mb-0 align-middle datatable">
                                <thead class="bg-dark text-white text-nowrap">
                                    <tr class="small text-uppercase">
                                        <th class="px-3 py-2 text-center" style="width: 50px;">#</th>
                                        <th class="px-3 py-2">Comprobante</th>
                                        <th class="px-3 py-2 text-center">Emisión</th>
                                        <th class="px-3 py-2">Doc. Cliente</th>
                                        <th class="px-3 py-2">Cliente / Nombres</th>
                                        <th class="px-3 py-2">Detalle Productos</th>
                                        <th class="px-3 py-2 text-right">Monto Total</th>
                                        <th class="px-3 py-2 text-center">N. Crédito</th>
                                        <th class="px-3 py-2 text-center" style="width: 80px;">Ver</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $total = 0;
                                        $nro = 0;
                                        foreach($boletas as $bol):
                                            $nro++;
                                            $notacomprobar = $bol->SERIE."-".$bol->COMPROBANTE; 
                                            $probar = Not_1_2Data::getByIdComprobado($notacomprobar);
                                            
                                            $bgColor = "";
                                            if (isset($probar)) {
                                                if ($probar->TIPO_DOC==8) $bgColor = "table-success";
                                                if ($probar->TIPO_DOC==7) $bgColor = "table-danger";
                                            }
                                    ?>
                                        <tr class="<?php echo $bgColor; ?> small">
                                            <td class="text-center fw-bold"><?php echo $nro ?></td>
                                            <td class="text-bold"><?php echo $bol->SERIE . "-" . $bol->COMPROBANTE ?></td>
                                            <td class="text-center"><?php echo date("d/m/Y", strtotime($bol->fecEmision)) ?></td>
                                            <td><span class="badge badge-light border text-muted px-2"><?php echo $bol->numDocUsuario ?></span></td>
                                            <td class="text-truncate" style="max-width: 150px;"><?php echo $bol->rznSocialUsuario ?></td>
                                            <td class="small text-muted" style="max-width: 200px;">
                                                <?php 
                                                    $operations = OperationData::getAllProductsBySellId($bol->EXTRA1);
                                                    foreach ($operations as $ope) {
                                                        $product = ProductData::getById($ope->product_id);
                                                        echo "• " . $product->name . ($ope->descripcion ? " (".$ope->descripcion.")" : "") . "<br>";
                                                    }
                                                ?>
                                            </td>
                                            <td class="text-right text-bold">
                                                <?php 
                                                    $valor = $bol->sumPrecioVenta;
                                                    if (isset($probar)) {
                                                        if ($probar->TIPO_DOC==8) $valor += (float)$probar->sumPrecioVenta;
                                                        elseif ($probar->TIPO_DOC==7) $valor = 0; // Se asume anulado total
                                                    }
                                                    $total += $valor;
                                                    echo number_format($valor, 2);
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (isset($probar)): ?>
                                                    <?php if ($probar->TIPO_DOC==7): ?>
                                                        <span class="badge badge-danger"><?php echo $probar->SERIE."-".$probar->COMPROBANTE; ?></span>
                                                    <?php else: ?>
                                                        <span class="badge badge-success">Nota Débito</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <a href="./?view=nocboleta&id=<?php echo $bol->EXTRA1 ?>" class="btn btn-outline-danger btn-xs px-2" title="Generar Nota de Crédito">
                                                        <i class="fas fa-file-invoice mr-1"></i> N.Cred
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="./?view=onesell&id=<?php echo $bol->EXTRA1 ?>&tipodoc=3" class="btn btn-dark btn-xs" title="Ver Detalle">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="./?action=regeneratesfsjson&tipo_doc=03&id_tipo_doc=<?php echo $bol->id; ?>" class="btn btn-primary btn-xs" title="Descargar JSON SUNAT">
                                                    <i class="fas fa-file-code"></i>
                                                </a>		
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="bg-light border-top">
                                    <tr class="text-bold">
                                        <td colspan="6" class="text-right py-2 small uppercase">Gran Total Reportado:</td>
                                        <td class="text-right py-2 h5 mb-0 text-info">S/ <?php echo number_format($total, 2); ?></td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info m-3 mb-0">
                            <i class="fas fa-info-circle mr-2"></i> No se encontraron boletas en el rango seleccionado.
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white py-3">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="description-block border-right text-muted">
                                <h5 class="description-header"><?php echo $nro; ?></h5>
                                <span class="description-text small">TOTAL BOLETAS</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="description-block text-info">
                                <h5 class="description-header">S/ <?php echo number_format($total, 2); ?></h5>
                                <span class="description-text small">MONTO TOTAL NETO</span>
                            </div>
                        </div>
                        <div class="col-md-4 small text-muted text-left px-4 d-flex align-items-center">
                            <div>
                                <i class="fas fa-square text-danger mr-1 small opacity-50"></i> Notas de Crédito (Anuladas)<br>
                                <i class="fas fa-square text-success mr-1 small opacity-50"></i> Notas de Débito (Aumentadas)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
    .table-sm td, .table-sm th { padding: .4rem .75rem; vertical-align: middle; }
    .card-outline.card-info { border-top: 3px solid #17a2b8; }
    .bg-dark { background-color: #343a40 !important; }
    .text-truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .badge { font-weight: 500; font-size: 85%; }
    .opacity-50 { opacity: 0.5; }
</style>