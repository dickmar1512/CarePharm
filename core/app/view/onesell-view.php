<?php
if ($_GET["tipodoc"] == 3):
	$venta = Boleta2Data::getByExtra($_GET["id"]);
	//$img = "img/bol.png";
    $desComprobante = "BOLETA ELECTRÓNICA";
	$docLabel = "DNI";
	$nomLabel = "SEÑOR(ES)";
else:
	$venta = Factura2Data::getByExtra($_GET["id"]);
	//$img = "img/fac.png";
    $desComprobante = "FACTURA ELECTRÓNICA";
	$docLabel = "RUC";
	$nomLabel = "RAZON SOCIAL";
endif;

$comp_cab = Cab_1_2Data::getById($venta->id, $_GET["tipodoc"]);
$comp_aca = Aca_1_2Data::getById($venta->id, $_GET["tipodoc"]);
$detalles = Det_1_2Data::getById($venta->id, $_GET["tipodoc"]);
$comp_tri = Tri_1_2Data::getById($venta->id, $_GET["tipodoc"]);
$comp_ley = Ley_1_2Data::getById($venta->id, $_GET["tipodoc"]);
$sellTemp = SellData::getById($venta->EXTRA1);

$sell = (object)[
	'id'=> $sellTemp->id,
	'person_id'=> $sellTemp->person_id,
	'user_id'=> $sellTemp->user_id,
	'total'=> $sellTemp->total,
	'cash'=> $sellTemp->cash,
	'discount'=> $sellTemp->discount,
	'created_at'=> $sellTemp->created_at,
	'tipo_comprobante'=> $sellTemp->tipo_comprobante,
	'serie'=> $sellTemp->serie,
	'comprobante'=> $sellTemp->comprobante,
	'estado'=> $sellTemp->estado,
	'tipo_pago'=> $sellTemp->tipo_pago,
	'box_id'=> $sellTemp->box_id,
	'operation_type_id'=> $sellTemp->operation_type_id,
	'observacion'=> $sellTemp->observacion,
	'forma_pago'=> $sellTemp->forma_pago
];

$pagoParcial = SellData::getImportePagoParcial($venta->EXTRA1);

$datoPagoParcial = [
    'id' => $pagoParcial[0]->id ?? 0,
    'importepp' => $pagoParcial[0]->importepp ?? 0
];

$operations = OperationData::getAllProductsBySellId($_GET["id"]);

$cajero = null;
$cajero = UserData::getById($sell->user_id)->username;
$empresa = EmpresaData::getDatos();

$fechaObj = new DateTime($comp_cab->fecEmision);
$fechaFormateada = $fechaObj->format('d/m/Y');

$selected = isset($sell->tipo_pago) ? $sell->tipo_pago : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title>Comprobante - Sistema de Ventas</title> -->
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> -->
     <link rel="stylesheet" href="dist/css/style.onesell.css">
</head>
<body>
    <div class="main-container">
        <!-- Header Section Compacto -->
        <div class="header-section fade-in">
            <div class="header-title">
                <div class="title-group">
                    <i class="fas fa-receipt"></i>
                    <h1>Comprobante</h1>
                </div>
                <div class="breadcrumb">
                    <i class="fas fa-home"></i> Ventas > Comprobante
                </div>
            </div>

            <div class="controls-section">
                <button class="btn btn-back" onclick="goBack()">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>

                <button class="btn btn-primary" id="imprimir80mm">
                    <i class="fas fa-print"></i> Imprimir
                </button>

                <div class="form-group">
                    <label class="form-label" for="selTipoPago">Tipo de Pago</label>
                    <select id="selTipoPago" class="form-control">
                        <option value="1" <?= $selected == 1 ? 'selected' : '' ?>>💵 Efectivo</option>
                        <option value="2" <?= $selected == 2 ? 'selected' : '' ?>>📱 Plin</option>
                        <option value="3" <?= $selected == 3 ? 'selected' : '' ?>>📱 Yape</option>
                        <option value="4" <?= $selected == 4 ? 'selected' : '' ?>>💳 T. Débito</option>
                        <option value="5" <?= $selected == 5 ? 'selected' : '' ?>>💳 T. Crédito</option>
                    </select>
                    <input type="hidden" id="sellid" name="sellid" value="<?=$venta->EXTRA1?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="importeParcial">Pago Parcial</label>
                    <input type="text" id="importeParcial" name="importeParcial" class="form-control" placeholder="0.00" value="<?= number_format($datoPagoParcial['importepp'], 2, '.', ',') ?>">
                </div>

                <button class="btn btn-primary" id="actualizarTipoPago">
                    <i class="fas fa-sync-alt"></i> Actualizar
                </button>
            </div>
        </div>

        <!-- Receipt Container Compacto -->
        <div class="receipt-container fade-in" id="receiptArea">
            <!-- Company Header Compacto -->
            <div class="company-info">
                <div class="company-logo">
                    <i class="fas fa-building" style="font-size: 1.5rem;"></i>
                </div>
                <div class="company-details">
                    <h3><?php echo $empresa->Emp_RazonSocial ?></h3>
                    <p><?php echo $empresa->Emp_Direccion ?></p>
                    <p>📞 <?php echo $empresa->Emp_Telefono ?></p>
                    <p>✉ <?php echo $empresa->Emp_Celular ?></p>
                </div>
                <div class="document-info">
                    <div style="font-size: 0.85rem;"><strong>RUC: <?php echo $empresa->Emp_Ruc ?></strong></div>
                    <div style="margin: 6px 0;">
                        <label for="numeroComprobante" style="font-size: 0.9rem;"><?php echo $desComprobante;?></label>
                    </div>
                    <div class="document-number" id="numeroComprobante" name="numeroComprobante"><?php echo $venta->SERIE . "-" . $venta->COMPROBANTE; ?></div>
                </div>
            </div>

            <!-- Customer Information Compacto -->
            <div class="customer-info">
                <div class="info-row">
                    <span class="info-label"><?= $docLabel ?></span>
                    <span class="info-value"><?php echo ": " . $comp_cab->numDocUsuario; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label"><?= $nomLabel ?></span>
                    <span class="info-value"><?php echo ": " . $comp_cab->rznSocialUsuario; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">DIRECCIÓN</span>
                    <span class="info-value"><?php echo ": " . $comp_aca->desDireccionCliente; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">FECHA:</span>
                    <span class="info-value"><?php echo ": " . $fechaFormateada . "  " . $comp_cab->horEmision; ?></span>
                </div>
            </div>

            <!-- Products Table Compacto -->
            <table class="products-table">
                <thead>
                    <tr>
                        <th>CANT</th>
                        <th>DESCRIPCIÓN</th>
                        <th>P.U.</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
						$total = 0;
						foreach ($operations as $ope) {
                            $product = ProductData::getById($ope->product_id);
                            $subtotal = $ope->q * $ope->prec_alt;
					?>
                    <tr>
                        <td class="quantity-cell"><?php echo $ope->q; ?></td>
                        <td class="description-cell">
                            <div class="product-name"><?php echo $product->name; ?></div>
                            <?php
								if ($ope->descripcion != "") { 
                            ?>
                            <div class="product-desc"><?php echo $ope->descripcion;?></div>
                            <?php } ?>
                        </td>
                        <td class="price-cell"><?php echo $ope->prec_alt; ?></td>
                        <td class="price-cell"><?php echo number_format($subtotal, 2, '.', ','); ?></td>
                    </tr>
                    <?php
						    $total = $subtotal + $total;
                            $totalConDesc = $total - $comp_cab->sumDescTotal;
                            $numLetra = NumeroLetras::convertir(number_format($totalConDesc, 2, '.', ','));
						}
                        
                        $datosComprobante = array(
                            "venta"       => $venta,
                            "detalles"    => $detalles,
                            "comp_cab"    => $comp_cab,
                            "comp_aca"    => $comp_aca,
                            "comp_tri"    => $comp_tri,
                            "comp_ley"    => $comp_ley,
                            "empresa"     => $empresa,
                            "cajero"      => $cajero,
                            "numLetra"    => $numLetra,
                            "sell"        => $sell,
                            "pagoParcial" => $datoPagoParcial
                        );
					?>
                </tbody>
            </table>

            <!-- Totals Section Compacto -->
            <div class="totals-section">
                <div class="user-info">
                    <i class="fas fa-user-tie" style="color: #667eea;"></i>
                    <div>
                        <div><strong>Cajero:</strong> <?=$cajero?></div>
                        <small style="color: #666;">Terminal: CAJA-01</small>
                    </div>
                </div>

                <div class="totals-table">
                    <table>
                        <tr><td>Op. Gratuita</td><td>0.00</td></tr>
                        <tr><td>Op. Exonerada</td><td><?php echo number_format($total, 2, '.', ','); ?></td></tr>
                        <tr><td>Op. Inafecta</td><td>0.00</td></tr>
                        <tr><td>Op. Gravada</td><td>0.00</td></tr>
                        <tr><td>IGV (18%)</td><td>0.00</td></tr>
                        <tr class="total-row">
                            <td>TOTAL</td>
                            <td><?php echo number_format($total, 2, '.', ','); ?></td>
                        </tr>
                    </table>
                    <div class="amount-in-words">
                        SON: <?php echo $numLetra; ?>
                    </div>
                    <!-- <input type=hiddenxx" id="datosComprobante" name="datosComprobante" value="'<?=json_encode($datosComprobante)?>'"> -->
                    <?php
						echo "<input type='hidden' id='datosComprobante' name='datosComprobante' value='" . json_encode($datosComprobante) . "'>";
					?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>