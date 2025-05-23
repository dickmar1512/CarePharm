<?php
$sd = (isset($_GET["sd"])) ? $_GET["sd"] : date('d/m/Y');
$ed = (isset($_GET["ed"])) ? $_GET["ed"] : date('d/m/Y');
$user_id = (isset($_GET["user_id"])) ? $_GET["user_id"] : 0;
?>
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class='fas fa-cart-plus'></i> Registro de ventas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Venta</a></li>
                    <li class="breadcrumb-item active">Registro de ventas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter"></i>
                    Filtros de búsqueda
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form id="filterForm">
                    <input type="hidden" name="view" value="sells">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha inicial</label>
                                <div class="input-group date" id="fechaini" data-target-input="nearest">
                                    <input type="text" name="sd" value="<?=$sd?>" class="form-control datetimepicker-input" data-target="#fechaini"/>
                                    <div class="input-group-append" data-target="#fechaini" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha final</label>
                                <div class="input-group date" id="fechafin" data-target-input="nearest">
                                    <input type="text" name="ed" value="<?=$ed?>" class="form-control datetimepicker-input" data-target="#fechafin"/>
                                    <div class="input-group-append" data-target="#fechafin" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Usuario</label>
                                <select name="user_id" class="form-control select2" style="width: 100%;">
                                    <option value="0">TODOS LOS USUARIOS</option>
                                    <?php foreach (UserData::getAll() as $user): ?>
                                        <option value="<?=$user->id?>" <?=($user_id == $user->id) ? 'selected' : ''?>>
                                            <?=$user->username?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" style="margin-top: 32px;">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i>
                    Resultados
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="sells" class="table table-bordered table-striped" style="width:100%">
                        <thead class="thead-dark">
                            <tr>
                                <th>Acciones</th>
                                <th>Comprobante</th>
                                <th>Cliente</th>
                                <th>Importe (S/)</th>
                                <th>Fecha</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargarán via AJAX -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" style="text-align:right">Total:</th>
                                <th id="total-ventas">0.00</th>
                                <th colspan="2"></th>
                            </tr>
                            <?php if (UserData::getById($_SESSION["user_id"])->is_admin): ?>
                            <tr>
                                <th colspan="3" style="text-align:right">Capital:</th>
                                <th id="total-capital">0.00</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr>
                                <th colspan="3" style="text-align:right">Ganancia:</th>
                                <th id="total-ganancia">0.00</th>
                                <th colspan="2"></th>
                            </tr>
                            <?php endif; ?>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>