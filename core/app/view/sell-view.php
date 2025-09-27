<?php
####################### NUMERO DE REGISTRO DE COMPROBANTES ###############################
$mysqli = new mysqli('localhost', 'milenio', 'armagedon', 'dbcarepharm', 3306); //BASE DE DATOS

//cantidad de boleta en la version 1.2
$query = $mysqli->prepare("SELECT * FROM boleta");//TABLA
$query->execute();
$query->store_result();
$registros = $query->num_rows;

$query = $mysqli->prepare("SELECT * FROM factura");//TABLA
$query->execute();
$query->store_result();
$registros_factura = $query->num_rows;

$query = $mysqli->prepare("SELECT * FROM sell WHERE tipo_comprobante = '70' ");
$query->execute();
$query->store_result();
$registros_orden = $query->num_rows;

$id_comprobante_actual = $registros + 1;
$id_comprobante_actual_f = $registros_factura + 1;
$id_comprobante_actual_o = $registros_orden + 1;

$empresa = EmpresaData::getDatos();
function generaCerosComprobante($numero)
{
	$empresa = EmpresaData::getDatos();
	$largo_numero = strlen($numero); //OBTENGO EL LARGO DEL NUMERO

	$largo_maximo = 8; //ESPECIFICO EL LARGO MAXIMO DE LA CADENA
	if ($empresa->Emp_Sucursal != 0) {
		$largo_maximo = 8;
	} //PARCHE OTRA SUCURSAL
	$agregar = $largo_maximo - $largo_numero; //TOMO LA CANTIDAD DE 0 AGREGAR
	for ($i = 0; $i < $agregar; $i++) {
		$numero = "0" . $numero;
	} //AGREGA LOS CEROS
	return $numero; //RETORNA EL NUMERO CON LOS CEROS
}

function generaCerosSerie($numero2)
{
	$empresa = EmpresaData::getDatos();
	$largo_numero = strlen($numero2);//OBTENGO EL LARGO DEL NUMERO
	$largo_maximo = 3;//ESPECIFICO EL LARGO MAXIMO DE LA CADENA
	if ($empresa->Emp_Sucursal != 0) {
		$largo_maximo = 2;
	} //PARCHE OTRA SUCURSAL
	$agregar = $largo_maximo - $largo_numero;   //TOMO LA CANTIDAD DE 0 AGREGAR
	for ($i = 0; $i < $agregar; $i++) {
		$numero2 = "0" . $numero2;
	} //AGREGA LOS CEROS
	return $numero2; //RETORNA EL NUMERO CON LOS CEROS
}

//COMPROBANTE PRIMERA SERIE B001
$NUMERO_COMPROBANTE = generaCerosComprobante($id_comprobante_actual);
//CAPTAMOS SERIE DE B001 AL B999
$NUMERO_SERIE = (int) (($id_comprobante_actual / 99999999) + 1);
if ($empresa->Emp_Sucursal == 0) {
	$SERIE = "B" . generaCerosSerie($NUMERO_SERIE);
} else if ($empresa->Emp_Sucursal == 1) {
	$SERIE = "BB" . generaCerosSerie($NUMERO_SERIE);
} else if ($empresa->Emp_Sucursal == 2) {
	$SERIE = "BD" . generaCerosSerie($NUMERO_SERIE);
} else if ($empresa->Emp_Sucursal == 3) {
	$SERIE = "BF" . generaCerosSerie($NUMERO_SERIE);
} else if ($empresa->Emp_Sucursal == 4) {
	$SERIE = "BH" . generaCerosSerie($NUMERO_SERIE);
} else if ($empresa->Emp_Sucursal == 5) {
	$SERIE = "BJ" . generaCerosSerie($NUMERO_SERIE);
}


//COMPROBANTE SERIE B001 AL SUPERIOR
if ($NUMERO_SERIE > 1) {
	$NUMERO_COMPROBANTE = $id_comprobante_actual % 99999999;
}
$COMPROBANTE = generaCerosComprobante($NUMERO_COMPROBANTE);

//COMPROBANTE PRIMERA SERIE F001
$NUMERO_COMPROBANTE_F = generaCerosComprobante($id_comprobante_actual_f);
//CAPTAMOS SERIE DE F001 AL F999
$NUMERO_SERIE_F = (int) (($id_comprobante_actual_f / 99999999) + 1);

if ($empresa->Emp_Sucursal == 0) {
	$SERIE_F = "F" . generaCerosSerie($NUMERO_SERIE_F);
} else if ($empresa->Emp_Sucursal == 1) {
	$SERIE_F = "FB" . generaCerosSerie($NUMERO_SERIE_F);
} else if ($empresa->Emp_Sucursal == 2) {
	$SERIE_F = "FD" . generaCerosSerie($NUMERO_SERIE_F);
} else if ($empresa->Emp_Sucursal == 3) {
	$SERIE_F = "FF" . generaCerosSerie($NUMERO_SERIE_F);
} else if ($empresa->Emp_Sucursal == 4) {
	$SERIE_F = "FH" . generaCerosSerie($NUMERO_SERIE_F);
} else if ($empresa->Emp_Sucursal == 5) {
	$SERIE_F = "FJ" . generaCerosSerie($NUMERO_SERIE_F);
}
//COMPROBANTE SERIE B001 AL SUPERIOR
if ($NUMERO_SERIE_F > 1) {
	$NUMERO_COMPROBANTE_F = $id_comprobante_actual_f % 99999999;
}
$COMPROBANTE_F = generaCerosComprobante($NUMERO_COMPROBANTE_F);

$ORDEN = generaCerosComprobante($id_comprobante_actual_o);
#####################################################################################
$empresa = EmpresaData::getDatos();
?>

<style>
/* CSS para hacer el formulario más compacto con cards */
.compact-form {
    font-size: 13px;
}
.compact-form .form-control {
    padding: 4px 8px;
    font-size: 12px;
    height: auto;
    min-height: 28px;
}
.compact-form .form-group {
    margin-bottom: 8px;
}
.compact-form label {
    font-size: 11px;
    font-weight: 600;
    margin-bottom: 2px;
    color: #555;
}
.compact-form .btn {
    padding: 4px 12px;
    font-size: 12px;
}
.compact-form .card {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.compact-form .card-header {
    background-color: #f8f9fa;
    padding: 8px 12px;
    border-bottom: 1px solid #dee2e6;
}
.compact-form .card-header h6 {
    margin: 0;
    font-size: 13px;
    font-weight: 600;
    color: #495057;
}
.compact-form .card-body {
    padding: 12px;
}
.compact-form .table td, .compact-form .table th {
    padding: 4px 6px;
    font-size: 11px;
    vertical-align: middle;
}
.compact-form .breadcrumb {
    padding: 4px 8px;
    margin-bottom: 8px;
    font-size: 12px;
}
.compact-form .content-header h1 {
    font-size: 20px;
    margin: 0;
}
.compact-form .icheck-primary, .compact-form .icheck-success, .compact-form .icheck-danger {
    margin: 0 10px;
}
.compact-form .icheck-primary input, .compact-form .icheck-success input, .compact-form .icheck-danger input {
    margin-right: 5px;
}
.compact-form .icheck-primary label, .compact-form .icheck-success label, .compact-form .icheck-danger label {
    font-size: 12px;
    margin: 0;
}
.btn-full {
    width: 100%;
    background: #28a745;
    color: white;
    border: none;
    padding: 10px;
    font-size: 13px;
    border-radius: 4px;
    cursor: pointer;
    margin-bottom: 10px;
}
.btn-full:hover {
    background: #218838;
}
.icon-inbox {
    font-size: 48px;
    color: #ccc;
}
.icon-container {
    text-align: center;
    padding: 40px;
}
.total-section {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 15px;
    margin-top: 10px;
}
.total-section .total-amount {
    font-size: 18px;
    font-weight: bold;
    color: #28a745;
}
.action-buttons {
    margin-top: 10px;
}
</style>

<div class="compact-form">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-1">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class='fa fa-shopping-cart'></i> Añadir Producto</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Venta</a></li>
                        <li class="breadcrumb-item active">Generar Venta</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">ELIJA COMPROBANTE</h6>
                    <div class="text-center mt-2">
                        <div class="icheck-primary d-inline">
                            <input type="radio" id="optTipoComprobante1" name="optTipoComprobante" value="3" checked>
                            <label for="optTipoComprobante1">BOLETA (<span style="color:red"><b>F4</b></span>)</label>
                        </div>
                        <div class="icheck-success d-inline">
                            <input type="radio" id="optTipoComprobante2" name="optTipoComprobante" value="1">
                            <label for="optTipoComprobante2">FACTURA (<span style="color:red"><b>F5</b></span>)</label>
                        </div>
                        <div class="icheck-danger d-inline">
                            <input type="radio" id="optTipoComprobante3" name="optTipoComprobante" value="0">
                            <label for="optTipoComprobante3">NOTA VENTA (<span style="color:red"><b>F6</b></span>)</label>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <?php
                    $permiso = PermisoData::get_permiso_x_key('proforma');
                    $permiso2 = PermisoData::get_permiso_x_key('comprobantes_fisicos');
                    $permiso3 = PermisoData::get_permiso_x_key('otro_documento_no_dni');
                    $total = 0;
                    $dsctotal = 0;
                    ?>

                    <!-- FORMULARIO BOLETA -->
                    <div id="comprobante_boleta">
                        <form action="?view=addboleta" id="formboleta" class="form-horizontal" method="post" onsubmit="enviado2(3,event)">
                            <!-- Campos ocultos -->
                            <input type="hidden" name="person_id" value="">
                            <input type="hidden" name="RUC" value="<?php echo $empresa->Emp_Ruc; ?>">
                            <input type="hidden" name="TIPO" value="03">
                            <input type="hidden" name="tipOperacion" value="0101">
                            <input type="hidden" name="fecVencimiento" value="-">
                            <input type="hidden" name="codLocalEmisor" value="0000">
                            <input type="hidden" name="tipMoneda" value="PEN">
                            <input type="hidden" name="porDescGlobal" value="-">
                            <input type="hidden" name="mtoDescGlobal" value="0">
                            <input type="hidden" name="mtoBasImpDescGlobal" value="0">
                            <input type="hidden" name="sumTotTributos" value="0">
                            <input type="hidden" name="sumDescTotal" value="<?= $dsctotal ?>">
                            <input type="hidden" name="sumOtrosCargos" value="0">
                            <input type="hidden" name="sumTotalAnticipos" value="0">
                            <input type="hidden" name="ublVersionId" value="2.1">
                            <input type="hidden" name="customizationId" value="2.0">
                            <input type="hidden" name="tipDocUsuario" value="1">
                            <!-- Más campos ocultos... -->

                            <!-- Cards superiores -->
                            <div class="row mb-3">
                                <!-- Card Información del Comprobante -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6><i class="fa fa-file-alt"></i> Información del Comprobante</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>FECHA:</label>
                                                        <input type="date" name="fecEmision" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>HORA:</label>
                                                        <input type="text" name="horEmision" class="form-control form-control-sm" value="<?php echo date('H:i:s'); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>SERIE:</label>
                                                        <input type="text" name="SERIE" class="form-control form-control-sm" value="<?php echo $SERIE; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>CORRELATIVO:</label>
                                                        <input type="text" name="COMPROBANTE" class="form-control form-control-sm" value="<?php echo $COMPROBANTE; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>FORMA DE PAGO:</label>
                                                        <select name="formaPago" class="form-control form-control-sm">
                                                            <option value="1" selected>Contado</option>
                                                            <option value="2">Crédito</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>TIPO DE PAGO:</label>
                                                        <select name="selTipoPago" class="form-control form-control-sm">
                                                            <option value="1" selected>Efectivo</option>
                                                            <option value="2">Plin</option>
                                                            <option value="3">Yape</option>
                                                            <option value="4">T. Débito</option>
                                                            <option value="5">T. Crédito</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Información del Cliente -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6><i class="fa fa-user"></i> Información del Cliente</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label><?php echo ($permiso3->Pee_Valor == 1) ? 'Documento:' : 'DNI:'; ?></label>
                                                        <input type="number" name="numDocUsuario" id="numDocUsuario" class="form-control form-control-sm" placeholder="DNI" 
                                                               onblur="<?php echo ($permiso3->Pee_Valor == 1) ? 'validar_no_dni()' : 'validar_dni()'; ?>" 
                                                               required value="00000000">
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label>CLIENTE:</label>
                                                        <input type="text" name="rznSocialUsuario" id="rznSocialUsuario" class="form-control form-control-sm" 
                                                               value="Cliente General" placeholder="Cliente" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>DISTRITO:</label>
                                                        <?php $ubigeos = SellData::getAllUbigeo(); ?>
                                                        <select name="codUbigeoCliente" id="codUbigeoCliente" class="form-control form-control-sm">
                                                            <option value="0">SELECCIONAR</option>
                                                            <?php foreach ($ubigeos as $ubigeo): ?>
                                                                <option value="<?php echo $ubigeo->codubigeo; ?>"><?php echo $ubigeo->distrito; ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label>DIRECCIÓN:</label>
                                                        <input type="text" name="desDireccionCliente" id="desDireccionCliente" class="form-control form-control-sm" placeholder="Dirección">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>DESCUENTO:</label>
                                                        <input type="number" name="discount" class="form-control form-control-sm" 
                                                               required value="<?= $dsctotal ?>" placeholder="0.00" step="any">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>CASH CLIENTE:</label>
                                                        <input type="number" name="money" id="money" required class="form-control form-control-sm" 
                                                               placeholder="0.00" step="any" value="<?php echo $total; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>PAGO PARCIAL:</label>
                                                        <input type="number" name="pagoParcial" class="form-control form-control-sm" 
                                                               placeholder="0.00" step="any" value="0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- VALORES POR DEFECTO -->
                             <input type="hidden" name="tipDocUsuario" value="1">
                             <input type="hidden" name="codProducto" value="0">
                             <input type="hidden" name="codUnidadMedida" value="NIU">
                             <input type="hidden" name="sumTotTributosItem" value="0">
                             <input type="hidden" name="codProductoSUNAT" value="-">
                             <input type="hidden" name="mtoIgvItem" value="0">
                             <input type="hidden" name="codTriIGV" value="9997">
                             <input type="hidden" name="codTipTributoIgvItem" value="VAT">
                             <input type="hidden" name="nomTributoIgvItem" value="EXO">
                             <input type="hidden" name="tipAfeIGV" value="20">
                             <input type="hidden" name="codCatTributoIgvItem" value="E">
                             <input type="hidden" name="tipSisISC" value="">
                             <input type="hidden" name="mtoIscItem" value="0">                             
                             <input type="hidden" name="porIgvItem" value="0">
                             <input type="hidden" name="codTriISC" value="-">
                             <input type="hidden" name="mtoIscItem" value="">
                             <input type="hidden" name="nomTributoIscItem" value="">
                             <input type="hidden" name="codTipTributoIscItem" value="-">
                             <input type="hidden" name="codCatTributoIscItem" value="-">
                             <input type="hidden" name="tipSisISC" value="">
                             <input type="hidden" name="porIscItem" value="">
                             <input type="hidden" name="mtoValorReferencialUnitario" value="0">
                             <input type="hidden" name="codTipDescuentoItem" value="-">
                             <input type="hidden" name="porDescuentoItem" value="0">
                             <input type="hidden" name="mtoDescuentoItem" value="0">
                             <input type="hidden" name="mtoBasImpDescuentoItem" value="0">
                             <input type="hidden" name="codTipCargoItem" value="-">
                             <input type="hidden" name="porCargoItem" value="0">
                             <input type="hidden" name="mtoCargoItem" value="0">
                             <input type="hidden" name="mtoBasImpCargoItem" value="0">
                             <input type="hidden" name="ideTributo" value="9997">
                             <input type="hidden" name="nomTributo" value="EXO">
                             <input type="hidden" name="codTipTributo" value="VAT">
                             <input type="hidden" name="codCatTributo" value="E">
                             <input type="hidden" name="mtoTributo" value="0">
                            <!-- Card Carrito de Compras -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6><i class="fa fa-shopping-cart"></i> Carrito de Compras</h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- Botón Agregar Item -->
                                            <button type="button" class="btn-full" id="btnAgregarItem">
                                                <i class="fa fa-plus"></i> Agregar item (F1)
                                            </button>

                                            <!-- Carrito de compras -->
                                            <?php if (isset($_SESSION["cart"])): ?>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm">
                                                        <thead class="thead-dark">
                                                            <tr>
                                                                <th width="30">Nº</th>
                                                                <th width="60">CANT.</th>
                                                                <th>DESCRIPCIÓN</th>
                                                                <th width="80">ANAQUEL</th>
                                                                <th width="80">P. UNIT.</th>
                                                                <th width="80">TOTAL</th>
                                                                <th width="40"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $contador = 0;
                                                            $dsctotal = 0;
                                                            foreach ($_SESSION["cart"] as $p):
                                                                $contador++;
                                                                $product = ProductData::getById($p["product_id"]);
                                                                $precio = ($product->is_may == 1) ? $product->price_may : $p["precio_unitario"];
                                                                ?>
                                                                <tr>
                                                                    <td class="text-center bg-dark text-white"><?php echo $contador; ?></td>
                                                                    <td class="text-center"><?php echo round($p["q"], 3); ?></td>
                                                                    <td>
                                                                        <?php echo $product->name . ' X ' . $product->presentation; ?>
                                                                        <?php
                                                                        $desc = ($product->description != "" && $product->description != "-") 
                                                                                ? $product->description . '-' . $product->laboratorio
                                                                                : (($p["descripcion"] != "" && $p["descripcion"] != "-") 
                                                                                   ? $p["descripcion"] . '-' . $product->laboratorio : '');
                                                                        if ($desc) echo "<small class=\"text-muted\">(" . $desc . ")</small>";
                                                                        ?>
                                                                    </td>
                                                                    <td class="text-center"><?= $product->anaquel ?></td>
                                                                    <td class="text-right"><?php echo number_format($p["precio_unitario"], 2, '.', ','); ?></td>
                                                                    <td class="text-right">
                                                                        <?php
                                                                        $pt = number_format($precio - $p["descuento"], 5) * round($p["q"], 3);
                                                                        $total += $pt;
                                                                        $dsctotal += $p["descuento"] * $p["q"];
                                                                        echo number_format($pt, 2);
                                                                        ?>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <a href="./?view=clearcart&product_id=<?php echo $product->id; ?>" 
                                                                           class="btn btn-danger btn-sm">
                                                                            <i class="fa fa-trash"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <?php $total = round($total, 2); ?>
                                            <?php else: ?>
                                                <div class="text-center">
                                                    <div class="icon-container">
                                                        <i class="fa fa-inbox icon-inbox"></i>
                                                        <p class="text-muted">Carrito vacío - Agregue productos para continuar</p>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Total y Acciones -->
                                            <div class="total-section">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <div class="total-amount">
                                                            IMPORTE TOTAL: S/ <?php echo number_format($total, 2, '.', ','); ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="action-buttons text-right">
                                                            <a href="./?view=clearcart" class="btn btn-danger btn-sm">
                                                                <i class="fa fa-times"></i> Cancelar
                                                            </a>
                                                            <button type="submit" class="btn btn-primary btn-sm ml-2">
                                                                <i class="fa fa-check"></i> Emitir Boleta (F3)
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" name="total" id="total" value="<?php echo $total; ?>">
                                            <input name="is_oficial" type="hidden" value="1">

                                            <!-- Errores -->
                                            <?php if (isset($_SESSION["errors"])): ?>
                                                <div class="alert alert-danger mt-3">
                                                    <h6>Errores encontrados:</h6>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Código</th>
                                                            <th>Producto</th>
                                                            <th>Mensaje</th>
                                                        </tr>
                                                        <?php foreach ($_SESSION["errors"] as $error):
                                                            $product = ProductData::getById($error["product_id"]);
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $product->id; ?></td>
                                                                <td><?php echo $product->barcode; ?></td>
                                                                <td><?php echo $product->name; ?></td>
                                                                <td><b><?php echo $error["message"]; ?></b></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </table>
                                                </div>
                                                <?php unset($_SESSION["errors"]); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Formularios para FACTURA y NOTA VENTA con el mismo estilo -->
                    <div id="comprobante_factura" style="display: none">
                        <form action="?view=addfactura" id="formfactura" class="form-horizontal" method="post" onsubmit="enviado2(1,event)">
                            <!-- Campos ocultos -->
                            <input type="hidden" name="person_id" value="">
                            <input type="hidden" name="RUC" value="<?php echo $empresa->Emp_Ruc; ?>">
                            <input type="hidden" name="TIPO" value="03">
                            <input type="hidden" name="tipOperacion" value="0101">
                            <input type="hidden" name="fecVencimiento" value="-">
                            <input type="hidden" name="codLocalEmisor" value="0000">
                            <input type="hidden" name="tipMoneda" value="PEN">
                            <input type="hidden" name="porDescGlobal" value="-">
                            <input type="hidden" name="mtoDescGlobal" value="0">
                            <input type="hidden" name="mtoBasImpDescGlobal" value="0">
                            <input type="hidden" name="sumTotTributos" value="0">
                            <input type="hidden" name="sumDescTotal" value="<?= $dsctotal ?>">
                            <input type="hidden" name="sumOtrosCargos" value="0">
                            <input type="hidden" name="sumTotalAnticipos" value="0">
                            <input type="hidden" name="ublVersionId" value="2.1">
                            <input type="hidden" name="customizationId" value="2.0">
                            <input type="hidden" name="tipDocUsuario" value="1">
                            <!-- Más campos ocultos... -->

                            <!-- Cards superiores -->
                            <div class="row mb-3">
                                <!-- Card Información del Comprobante -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6><i class="fa fa-file-alt"></i> Información del Comprobante</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>FECHA:</label>
                                                        <input type="date" name="fecEmision" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>HORA:</label>
                                                        <input type="text" name="horEmision" class="form-control form-control-sm" value="<?php echo date('H:i:s'); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>SERIE:</label>
                                                        <input type="text" name="SERIE" class="form-control form-control-sm" value="<?php echo $SERIE_F; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>CORRELATIVO:</label>
                                                        <input type="text" name="COMPROBANTE" class="form-control form-control-sm" value="<?php echo $COMPROBANTE_F; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>FORMA DE PAGO:</label>
                                                        <select name="formaPago" class="form-control form-control-sm">
                                                            <option value="1" selected>Contado</option>
                                                            <option value="2">Crédito</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>TIPO DE PAGO:</label>
                                                        <select name="selTipoPago" class="form-control form-control-sm">
                                                            <option value="1" selected>Efectivo</option>
                                                            <option value="2">Plin</option>
                                                            <option value="3">Yape</option>
                                                            <option value="4">T. Débito</option>
                                                            <option value="5">T. Crédito</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Información del Cliente -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6><i class="fa fa-user"></i> Información del Cliente</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>RUC:</label>
                                                        <input type="number" name="numDocUsuario" class="form-control" id="ruc" placeholder="Nº de RUC" onblur="validar_ruc()" required="required"> 
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label>RAZÓN SOCIAL:</label>
                                                        <input type="text" name="rznSocialUsuario" id="rznSocialUsuario" class="form-control form-control-sm" value="" placeholder="DATOS CLIENTE" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>DISTRITO:</label>
                                                        <?php $ubigeos = SellData::getAllUbigeo(); ?>
                                                        <select name="codUbigeoCliente" id="codUbigeoCliente" class="form-control form-control-sm">
                                                            <option value="0">SELECCIONAR</option>
                                                            <?php foreach ($ubigeos as $ubigeo): ?>
                                                                <option value="<?php echo $ubigeo->codubigeo; ?>"><?php echo $ubigeo->distrito; ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label>DIRECCIÓN:</label>
                                                        <input type="text" name="desDireccionCliente" id="desDireccionCliente" class="form-control form-control-sm" placeholder="Dirección">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>DESCUENTO:</label>
                                                        <input type="number" name="discount" class="form-control form-control-sm" 
                                                               required value="<?= $dsctotal ?>" placeholder="0.00" step="any">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>CASH CLIENTE:</label>
                                                        <input type="number" name="money" id="money" required class="form-control form-control-sm" 
                                                               placeholder="0.00" step="any" value="<?php echo $total; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>PAGO PARCIAL:</label>
                                                        <input type="number" name="pagoParcial" class="form-control form-control-sm" 
                                                               placeholder="0.00" step="any" value="0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- VALORES POR DEFECTO -->
                            <input type="hidden" name="tipDocUsuario" value="6">
							<input type="hidden" name="codUnidadMedida" value="NIU">
							<input type="hidden" name="codProducto" value="0">
							<input type="hidden" name="codProductoSUNAT" value="-">
							<input type="hidden" name="sumTotTributosItem" value="0">
							<input type="hidden" name="codTriIGV" value="9997">
							<input type="hidden" name="mtoIgvItem" value="0">
							<input type="hidden" name="nomTributoIgvItem" value="EXO">
							<input type="hidden" name="codTipTributoIgvItem" value="VAT">
							<input type="hidden" name="codCatTributoIgvItem" value="E">
							<input type="hidden" name="tipAfeIGV" value="20">
							<input type="hidden" name="mtoIscItem" value="0">
							<input type="hidden" name="tipSisISC" value="">
							<input type="hidden" name="porIgvItem" value="0">
							<input type="hidden" name="codTriISC" value="-">
							<input type="hidden" name="mtoIscItem" value="">
							<input type="hidden" name="nomTributoIscItem" value="">
							<input type="hidden" name="codTipTributoIscItem" value="-">
							<input type="hidden" name="codCatTributoIscItem" value="-">
							<input type="hidden" name="porIscItem" value="">
							<input type="hidden" name="mtoValorReferencialUnitario" value="0">
							<input type="hidden" name="codTipDescuentoItem" value="-">
							<input type="hidden" name="porDescuentoItem" value="0">
							<input type="hidden" name="mtoDescuentoItem" value="0">
							<input type="hidden" name="mtoBasImpDescuentoItem" value="0">
							<input type="hidden" name="codTipCargoItem" value="-">
							<input type="hidden" name="porCargoItem" value="0">
							<input type="hidden" name="mtoCargoItem" value="0">
							<input type="hidden" name="mtoBasImpCargoItem" value="0">
							<input type="hidden" name="ideTributo" value="9997">
							<input type="hidden" name="nomTributo" value="EXO">
							<input type="hidden" name="codTipTributo" value="VAT">
							<input type="hidden" name="codCatTributo" value="E">
							<input type="hidden" name="mtoTributo" value="0">

                            <!-- Card Carrito de Compras -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6><i class="fa fa-shopping-cart"></i> Carrito de Compras</h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- Botón Agregar Item -->
                                            <button type="button" class="btn-full" id="btnAgregarItem2">
                                                <i class="fa fa-plus"></i> Agregar item (F1)
                                            </button>

                                            <!-- Carrito de compras -->
                                            <?php if (isset($_SESSION["cart"])): ?>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm">
                                                        <thead class="thead-dark">
                                                            <tr>
                                                                <th width="30">Nº</th>
                                                                <th width="60">CANT.</th>
                                                                <th>DESCRIPCIÓN</th>
                                                                <th width="80">ANAQUEL</th>
                                                                <th width="80">P. UNIT.</th>
                                                                <th width="80">TOTAL</th>
                                                                <th width="40"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $contador = 0;
                                                            $dsctotal = 0;
                                                            foreach ($_SESSION["cart"] as $p):
                                                                $contador++;
                                                                $product = ProductData::getById($p["product_id"]);
                                                                $precio = ($product->is_may == 1) ? $product->price_may : $p["precio_unitario"];
                                                                ?>
                                                                <tr>
                                                                    <td class="text-center bg-dark text-white"><?php echo $contador; ?></td>
                                                                    <td class="text-center"><?php echo round($p["q"], 3); ?></td>
                                                                    <td>
                                                                        <?php echo $product->name . ' X ' . $product->presentation; ?>
                                                                        <?php
                                                                        $desc = ($product->description != "" && $product->description != "-") 
                                                                                ? $product->description . '-' . $product->laboratorio
                                                                                : (($p["descripcion"] != "" && $p["descripcion"] != "-") 
                                                                                   ? $p["descripcion"] . '-' . $product->laboratorio : '');
                                                                        if ($desc) echo "<small class=\"text-muted\">(" . $desc . ")</small>";
                                                                        ?>
                                                                    </td>
                                                                    <td class="text-center"><?= $product->anaquel ?></td>
                                                                    <td class="text-right"><?php echo number_format($p["precio_unitario"], 2, '.', ','); ?></td>
                                                                    <td class="text-right">
                                                                        <?php
                                                                        $pt = number_format($precio - $p["descuento"], 5) * round($p["q"], 3);
                                                                        $total += $pt;
                                                                        $dsctotal += $p["descuento"] * $p["q"];
                                                                        echo number_format($pt, 2);
                                                                        ?>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <a href="./?view=clearcart&product_id=<?php echo $product->id; ?>" 
                                                                           class="btn btn-danger btn-sm">
                                                                            <i class="fa fa-trash"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <?php $total = round($total, 2); ?>
                                            <?php else: ?>
                                                <div class="text-center">
                                                    <div class="icon-container">
                                                        <i class="fa fa-inbox icon-inbox"></i>
                                                        <p class="text-muted">Carrito vacío - Agregue productos para continuar</p>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Total y Acciones -->
                                            <div class="total-section">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <div class="total-amount">
                                                            IMPORTE TOTAL: S/ <?php echo number_format($total, 2, '.', ','); ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="action-buttons text-right">
                                                            <a href="./?view=clearcart" class="btn btn-danger btn-sm">
                                                                <i class="fa fa-times"></i> Cancelar
                                                            </a>
                                                            <button type="submit" class="btn btn-primary btn-sm ml-2">
                                                                <i class="fa fa-check"></i> Emitir Factura (F3)
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" name="total" id="total" value="<?php echo $total; ?>">
                                            <input name="is_oficial" type="hidden" value="1">

                                            <!-- Errores -->
                                            <?php if (isset($_SESSION["errors"])): ?>
                                                <div class="alert alert-danger mt-3">
                                                    <h6>Errores encontrados:</h6>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Código</th>
                                                            <th>Producto</th>
                                                            <th>Mensaje</th>
                                                        </tr>
                                                        <?php foreach ($_SESSION["errors"] as $error):
                                                            $product = ProductData::getById($error["product_id"]);
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $product->id; ?></td>
                                                                <td><?php echo $product->barcode; ?></td>
                                                                <td><?php echo $product->name; ?></td>
                                                                <td><b><?php echo $error["message"]; ?></b></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </table>
                                                </div>
                                                <?php unset($_SESSION["errors"]); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div id="comprobante_orden" style="display: none;">
                        <form action="?view=processsell" id="formnotaventa" class="form-horizontal" method="post" onsubmit="return enviado2(0)">
                            <!-- Campos ocultos -->
                            <input type="hidden" name="TIPO" value="70">
                            <?php $total = 0; ?>

                            <!-- Cards superiores -->
                            <div class="row mb-3">
                                <!-- Card Información del Comprobante -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6><i class="fa fa-sticky-note"></i> Información de la Nota de Venta</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>FECHA:</label>
                                                        <input type="date" name="fecEmision" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>HORA:</label>
                                                        <input type="text" name="horEmision" class="form-control form-control-sm" value="<?php echo date('H:i:s'); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>SERIE:</label>
                                                        <input type="text" name="SERIE" class="form-control form-control-sm" value="0002">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Nº ORDEN:</label>
                                                        <input type="text" name="COMPROBANTE" class="form-control form-control-sm" value="<?php echo $ORDEN; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Información del Cliente -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6><i class="fa fa-user"></i> Información del Cliente</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>CLIENTE:</label>
                                                        <?php $clients = PersonData::getClients(); ?>
                                                        <select name="client_id" class="form-control form-control-sm select2bs4">
                                                            <?php foreach ($clients as $client): ?>
                                                                <option value="<?php echo $client->id; ?>">
                                                                    <?php echo $client->name . " " . $client->lastname; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>EFECTIVO:</label>
                                                        <input type="number" name="money3" class="form-control form-control-sm" 
                                                               placeholder="0.00" step="any" required>
                                                        <input type="hidden" name="discount3" value="0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Carrito de Compras -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6><i class="fa fa-shopping-cart"></i> Carrito de Compras</h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- Botón Agregar Item -->
                                            <button type="button" class="btn-full" id="btnAgregarItem3">
                                                <i class="fa fa-plus"></i> Agregar item (F1)
                                            </button>

                                            <!-- Carrito de compras -->
                                            <?php if (isset($_SESSION["cart"])): ?>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm">
                                                        <thead class="thead-dark">
                                                            <tr>
                                                                <th width="30">Nº</th>
                                                                <th width="60">CANT.</th>
                                                                <th>DESCRIPCIÓN</th>
                                                                <th width="80">ANAQUEL</th>
                                                                <th width="80">P. UNIT.</th>
                                                                <th width="80">TOTAL</th>
                                                                <th width="40"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $contador = 0;
                                                            $dsctotal = 0;
                                                            foreach ($_SESSION["cart"] as $p):
                                                                $contador++;
                                                                $product = ProductData::getById($p["product_id"]);
                                                                $precio = ($product->is_may == 1) ? $product->price_may : $p["precio_unitario"];
                                                                ?>
                                                                <tr>
                                                                    <td class="text-center bg-dark text-white"><?php echo $contador; ?></td>
                                                                    <td class="text-center"><?php echo round($p["q"], 3); ?></td>
                                                                    <td>
                                                                        <?php echo $product->name . ' X ' . $product->presentation; ?>
                                                                        <?php
                                                                        $desc = ($product->description != "" && $product->description != "-") 
                                                                                ? $product->description
                                                                                : (($p["descripcion"] != "" && $p["descripcion"] != "-") 
                                                                                   ? $p["descripcion"] : '');
                                                                        if ($desc) echo "<small class=\"text-muted\">(" . $desc . ")</small>";
                                                                        ?>
                                                                    </td>
                                                                    <td class="text-center"><?= $product->anaquel ?></td>
                                                                    <td class="text-right"><?php echo number_format($p["precio_unitario"], 2, '.', ','); ?></td>
                                                                    <td class="text-right">
                                                                        <?php
                                                                        $pt = number_format($precio - $p["descuento"], 5) * round($p["q"], 3);
                                                                        $total += $pt;
                                                                        $dsctotal += $p["descuento"] * $p["q"];
                                                                        echo number_format($pt, 2);
                                                                        ?>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <a href="./?view=clearcart&product_id=<?php echo $product->id; ?>" 
                                                                           class="btn btn-danger btn-sm">
                                                                            <i class="fa fa-trash"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <?php $total = round($total, 2); ?>
                                            <?php else: ?>
                                                <div class="text-center">
                                                    <div class="icon-container">
                                                        <i class="fa fa-inbox icon-inbox"></i>
                                                        <p class="text-muted">Carrito vacío - Agregue productos para continuar</p>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Total y Acciones -->
                                            <div class="total-section">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <div class="total-amount">
                                                            IMPORTE TOTAL: S/ <?php echo number_format($total, 2, '.', ','); ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="action-buttons text-right">
                                                            <a href="./?view=clearcart" class="btn btn-danger btn-sm">
                                                                <i class="fa fa-times"></i> Cancelar
                                                            </a>
                                                            <button type="submit" class="btn btn-primary btn-sm ml-2">
                                                                <i class="fa fa-check"></i> Finalizar Venta (F3)
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" name="total" value="<?php echo $total; ?>">
                                            <input name="is_oficial" type="hidden" value="1">

                                            <!-- Errores -->
                                            <?php if (isset($_SESSION["errors"])): ?>
                                                <div class="alert alert-danger mt-3">
                                                    <h6>Errores encontrados:</h6>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Código</th>
                                                            <th>Producto</th>
                                                            <th>Mensaje</th>
                                                        </tr>
                                                        <?php foreach ($_SESSION["errors"] as $error):
                                                            $product = ProductData::getById($error["product_id"]);
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $product->id; ?></td>
                                                                <td><?php echo $product->barcode; ?></td>
                                                                <td><?php echo $product->name; ?></td>
                                                                <td><b><?php echo $error["message"]; ?></b></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </table>
                                                </div>
                                                <?php unset($_SESSION["errors"]); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
</div>