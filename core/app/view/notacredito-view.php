<?php
	// Determinar el tipo de documento (Boleta o Factura)
	$tipodoc = isset($_GET["tipodoc"]) ? $_GET["tipodoc"] : 3; // 3=Boleta, 1=Factura
	
	if ($tipodoc == 3) {
		// BOLETA
		$product = Boleta2Data::getByNumDoc($_GET["num"]);
		$desComprobante = "NOTA DE CRÉDITO ELECTRÓNICA";
		$docLabel = "DNI";
		$nomLabel = "NOMBRE";
		$docModifica = "BOLETA ELECTRÓNICA";
	} else {
		// FACTURA
		$product = Factura2Data::getByNumDoc($_GET["num"]);
		$desComprobante = "NOTA DE CRÉDITO ELECTRÓNICA";
		$docLabel = "RUC";
		$nomLabel = "RAZÓN SOCIAL";
		$docModifica = "FACTURA ELECTRÓNICA";
	}

	$comp_cab = Not_1_2Data::getById($product->id, 7);
	$detalles = Det_1_2Data::getById($product->id, 7);
	$comp_tri = Tri_1_2Data::getById($product->id, 7);
	$comp_ley = Ley_1_2Data::getById($product->id, 7);

	$sell = SellData::getByNroDoc($comp_cab->serieDocModifica);
    $cajero = null;
    $cajero = UserData::getById($sell->user_id);
    $empresa = EmpresaData::getDatos();
    
    // Obtener el tipo de nota
    $tiposNota = [
        1 => "Anulación en la Operación",
        2 => "Anulación por error en el RUC",
        3 => "Corrección por error en la descripción",
        4 => "Descuento global",
        5 => "Descuento por item",
        6 => "Devolución total",
        7 => "Devolución por item"
    ];
    $tipoNota = $tiposNota[$comp_cab->codTipoNota] ?? "";
    
    // Calcular vuelto
    $total = 0;
    foreach ($detalles as $det) {
        $total += $det->mtoValorVentaItem;
    }
    $vuelto = $sell->cash - $total;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dist/css/style.onesell.css">
</head>
<body>
    <div class="main-container">
        <!-- Header Section Compacto -->
        <div class="header-section fade-in">
            <div class="header-title">
                <div class="title-group">
                    <i class="fas fa-file-invoice"></i>
                    <h1>Nota de Crédito</h1>
                </div>
                <div class="breadcrumb">
                    <i class="fas fa-home"></i> Ventas > Nota de Crédito > <?php echo ($tipodoc == 3) ? 'Boleta' : 'Factura'; ?>
                </div>
            </div>

            <div class="controls-section">
                <button class="btn btn-back" onclick="goBack()">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>

                <button class="btn btn-primary" id="imprimir">
                    <i class="fas fa-print"></i> Imprimir
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
                        <label for="numeroComprobante" style="font-size: 0.9rem;"><?php echo $desComprobante; ?></label>
                    </div>
                    <div class="document-number" id="numeroComprobante" name="numeroComprobante"><?php echo $product->SERIE . "-" . $product->COMPROBANTE; ?></div>
                </div>
            </div>

            <!-- Tipo de Nota (destacado en rojo) -->
            <div style="background: #fee; border: 2px solid #f44; padding: 12px; margin: 15px 0; border-radius: 8px; text-align: center;">
                <h3 style="color: #c00; margin: 0; font-size: 1rem;">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $tipoNota; ?>
                </h3>
            </div>

            <!-- Document Information -->
            <div class="customer-info">
                <div style="background: #f8f9fa; padding: 10px; border-radius: 6px; margin-bottom: 10px;">
                    <strong style="color: #667eea;">📄 DOCUMENTO QUE MODIFICA</strong>
                </div>
                <div class="info-row">
                    <span class="info-label"><?php echo $docModifica; ?></span>
                    <span class="info-value"><?php echo ": " . $comp_cab->serieDocModifica; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label"><?php echo $docLabel; ?></span>
                    <span class="info-value"><?php echo ": " . $comp_cab->numDocUsuario; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label"><?php echo $nomLabel; ?></span>
                    <span class="info-value"><?php echo ": " . $comp_cab->rznSocialUsuario; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">MOTIVO</span>
                    <span class="info-value"><?php echo ": " . $comp_cab->descMotivo; ?></span>
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
                        foreach ($detalles as $det) {
                    ?>
                    <tr>
                        <td class="quantity-cell"><?php echo $det->ctdUnidadItem; ?></td>
                        <td class="description-cell">
                            <div class="product-name"><?php echo $det->desItem; ?></div>
                        </td>
                        <td class="price-cell"><?php echo number_format($det->mtoValorUnitario, 2, '.', ','); ?></td>
                        <td class="price-cell"><?php echo number_format($det->mtoValorVentaItem, 2, '.', ','); ?></td>
                    </tr>
                    <?php
                            $total += $det->mtoValorVentaItem;
                            $numLetra = NumeroLetras::convertir(number_format($total, 2, '.', ','));
                        }
                        $vuelto = $sell->cash - $total;

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
                            "sell"        => $sell
                        );
                    ?>
                </tbody>
            </table>

            <!-- Totals Section Compacto -->
            <div class="totals-section">
                <div class="user-info">
                    <i class="fas fa-user-tie" style="color: #667eea;"></i>
                    <div>
                        <div><strong>Cajero:</strong> <?php echo $cajero->username; ?></div>
                        <small style="color: #666;">Terminal: CAJA-01</small>
                    </div>
                </div>

                <div class="totals-table">
                    <table>
                        <tr><td>Operación Gratuita</td><td>0.00</td></tr>
                        <tr><td>Operación Exonerada</td><td><?php echo number_format($total, 2, '.', ','); ?></td></tr>
                        <tr><td>Operación Inafecta</td><td>0.00</td></tr>
                        <tr><td>Operación Gravada</td><td>0.00</td></tr>
                        <tr><td>IGV (18%)</td><td>0.00</td></tr>
                        <tr class="total-row">
                            <td><strong>MONTO TOTAL</strong></td>
                            <td><strong>S/ <?php echo number_format($total, 2, '.', ','); ?></strong></td>
                        </tr>
                    </table>
                    <div class="amount-in-words">
                        SON: <?php echo $numLetra; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }        
    </script>
</body>
</html>