<?php
$sd = isset($_GET["sd"]) ? $_GET["sd"] : date("Y-m-01");
$ed = isset($_GET["ed"]) ? $_GET["ed"] : date("Y-m-t");
$user_id = isset($_GET["user_id"]) ? $_GET["user_id"] : 0;

$users = UserData::getAll();
$operations = [];

if (isset($_GET["sd"]) && isset($_GET["ed"])) {
    $operations = OperationData::getBincartReport($sd, $ed, $user_id);
} else {
    // Si no se han enviado parámetros pero es la primera vez, cargar por defecto
    $operations = OperationData::getBincartReport($sd, $ed, $user_id);
}

// Mapear el ID de tipo de comprobante a su nombre (Factura, Boleta, etc.)
function getTipoComprobanteName($id) {
    if ($id == '1' || $id == '01') return 'FACTURA';
    if ($id == '2' || $id == '03') return 'BOLETA';
    if ($id == '3' || $id == '07') return 'NOTA DE CRÉDITO';
    if ($id == '4' || $id == '08') return 'NOTA DE DÉBITO';
    if ($id == '70') return 'ORDEN DE VENTA';
    
    // Si no tiene tipo asignado, puede ser una operación de ajuste de stock o anulación general
    if (empty($id)) return 'AJUSTE / OTROS';
    return 'DOC-'.$id;
}
?>
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fa fa-list-alt text-primary"></i> Reporte Bincard (Kardex Detallado)</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="./?view=home">Inicio</a></li>
                    <li class="breadcrumb-item active">Bincart</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <form method="GET" action="./">
                    <input type="hidden" name="view" value="bincartreport">
                    <div class="row">
                        <div class="col-md-3">
                            <label><i class="fa fa-calendar-alt text-muted"></i> Desde:</label>
                            <input type="date" name="sd" value="<?=$sd?>" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label><i class="fa fa-calendar-alt text-muted"></i> Hasta:</label>
                            <input type="date" name="ed" value="<?=$ed?>" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label><i class="fa fa-user text-muted"></i> Usuario Responsable:</label>
                            <select name="user_id" class="form-control select2bs4">
                                <option value="0">-- TODOS LOS USUARIOS --</option>
                                <?php foreach($users as $u): ?>
                                    <option value="<?=$u->id?>" <?=($user_id==$u->id)?'selected':''?>><?=$u->name." ".$u->lastname?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-filter"></i> Filtrar</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="card-body">
                <?php if(count($operations)>0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover datatable table-sm">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th rowspan="2" class="align-middle text-center">FECHA</th>
                                    <th rowspan="2" class="align-middle text-center">PRODUCTO</th>
                                    <th rowspan="2" class="align-middle text-center">ESTADO</th>
                                    <th colspan="2" class="text-center">COMPROBANTE</th>
                                    <th rowspan="2" class="align-middle text-center">USUARIO</th>
                                    <th colspan="3" class="text-center bg-info">MOVIMIENTOS</th>
                                </tr>
                                <tr>
                                    <th class="text-center">TIPO</th>
                                    <th class="text-center">NRO.</th>
                                    <th class="text-center bg-success">ENTRADA</th>
                                    <th class="text-center bg-danger">SALIDA</th>
                                    <th class="text-center bg-primary">SALDO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($operations as $op): 
                                    $is_inactivo = ($op->estado_op == 0);
                                    $estado_lbl = $is_inactivo ? '<span class="badge badge-danger">INACTIVO</span>' : '<span class="badge badge-success">ACTIVO</span>';
                                    
                                    // Row styling for cancelled operations
                                    $tr_class = $is_inactivo ? 'text-muted bg-light' : '';
                                    
                                    $entrada = ($op->operation_type_id == 1) ? $op->q : '';
                                    $salida = ($op->operation_type_id == 2) ? $op->q : '';
                                    
                                    // Si la operación está inactiva, se tacha la cantidad para que no confunda el saldo visualmente
                                    if ($is_inactivo) {
                                        $entrada = $entrada ? "<s>$entrada</s>" : '';
                                        $salida = $salida ? "<s>$salida</s>" : '';
                                    }
                                    
                                    $comprobante = $op->serie ? $op->serie.'-'.str_pad($op->comprobante, 8, "0", STR_PAD_LEFT) : $op->comprobante;
                                ?>
                                <tr class="<?=$tr_class?>">
                                    <td class="text-center align-middle"><?=date("d/m/Y", strtotime($op->fecha))?></td>
                                    <td class="align-middle"><b><?=$op->producto?></b></td>
                                    <td class="text-center align-middle"><?=$estado_lbl?></td>
                                    <td class="text-center align-middle"><?=getTipoComprobanteName($op->tipo_comprobante_id)?></td>
                                    <td class="text-center align-middle"><?=$comprobante?></td>
                                    <td class="text-center align-middle"><?=$op->usuario?></td>
                                    <td class="text-center text-success font-weight-bold align-middle"><?=$entrada?></td>
                                    <td class="text-center text-danger font-weight-bold align-middle"><?=$salida?></td>
                                    <td class="text-center text-primary font-weight-bold align-middle" style="font-size: 1.1em;"><?=$op->saldo?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fa fa-info-circle fa-2x mb-2"></i><br>
                        No se encontraron movimientos de inventario para los filtros seleccionados.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
