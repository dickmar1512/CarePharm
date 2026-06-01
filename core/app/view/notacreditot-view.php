<?php
	$product = Factura2Data::getByNumDoc($_GET["num"]);
	$comp_cab = null;
	$detalles = [];
	$comp_tri = null;
	$comp_ley = null;
	$sell = null;
	$cajero = null;

	if ($product) {
		$comp_cab = Not_1_2Data::getById($product->id, 7);
		$detalles = Det_1_2Data::getById($product->id, 7);
		if (!$detalles) {
			$detalles = [];
		}
		$comp_tri = Tri_1_2Data::getById($product->id, 7);
		$comp_ley = Ley_1_2Data::getById($product->id, 7);

		if ($comp_cab && !empty($comp_cab->serieDocModifica)) {
			$sell = SellData::getByNroDoc($comp_cab->serieDocModifica);
		}
	}

	if ($sell && isset($sell->user_id)) {
		$cajero = UserData::getById($sell->user_id);
	}
	$empresa = EmpresaData::getDatos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dist/css/style.onesell.css">
    <style>
        .badge-nota { display: inline-block; padding: 0.25em 0.6em; font-size: 85%; font-weight: 700; border-radius: 0.25rem; background-color: #dc3545; color: #fff; margin-bottom: 10px; }
        .modifica-label { font-size: 0.8rem; color: #666; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; display: block; }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header Section -->
        <div class="header-section fade-in">
            <div class="header-title">
                <div class="title-group">
                    <i class="fas fa-file-invoice"></i>
                    <h1>Nota de Crédito</h1>
                </div>
                <div class="breadcrumb">
                    <i class="fas fa-home"></i> Reportes > Nota de Crédito
                </div>
            </div>

            <div class="controls-section">
                <button class="btn btn-back" onclick="window.history.back()">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>

                <button class="btn btn-primary" id="imprimir">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>

        <!-- Receipt Container -->
        <div class="receipt-container fade-in" id="receiptArea">
            <!-- Company Header -->
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
                <div class="document-info text-center">
                    <div style="font-size: 0.85rem;"><strong>RUC: <?php echo $empresa->Emp_Ruc ?></strong></div>
                    <div style="margin: 6px 0;">
                        <label style="font-size: 0.9rem; font-weight: bold; color: #dc3545;">NOTA DE CRÉDITO ELECTRÓNICA</label>
                    </div>
                    <div class="document-number"><?php echo $product ? ($product->SERIE . "-" . $product->COMPROBANTE) : ""; ?></div>
                    <div class="badge-nota mt-2">
                        <?php 
                            $motivos = [
                                1 => "Anulación de la Operación",
                                2 => "Anulación por error en el RUC",
                                3 => "Corrección por error en la descripción",
                                4 => "Descuento global",
                                5 => "Descuento por item",
                                6 => "Devolución total",
                                7 => "Devolución por item"
                            ];
                            echo ($comp_cab && isset($comp_cab->codTipoNota)) ? ($motivos[$comp_cab->codTipoNota] ?? "Otros") : "Otros";
                        ?>
                    </div>
                </div>
            </div>

            <!-- Modification Info -->
            <div class="customer-info border-bottom pb-3 mb-3">
                <span class="modifica-label">Documento que modifica</span>
                <div class="info-row">
                    <span class="info-label">FACTURA</span>
                    <span class="info-value"><?php echo ": " . ($comp_cab ? $comp_cab->serieDocModifica : ""); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">DOC. CLIENTE</span>
                    <span class="info-value"><?php echo ": " . ($comp_cab ? $comp_cab->numDocUsuario : ""); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">CLIENTE</span>
                    <span class="info-value"><?php echo ": " . ($comp_cab ? $comp_cab->rznSocialUsuario : ""); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">MOTIVO</span>
                    <span class="info-value text-bold"><?php echo ": " . ($comp_cab ? $comp_cab->descMotivo : ""); ?></span>
                </div>
            </div>

            <!-- Products Table -->
            <table class="products-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">CANT</th>
                        <th>DESCRIPCIÓN</th>
                        <th style="width: 80px;" class="text-right">P.U.</th>
                        <th style="width: 100px;" class="text-right">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $total = 0;
                        foreach ($detalles as $det) {
                            $total += $det->mtoValorVentaItem;
                    ?>
                    <tr>
                        <td class="quantity-cell text-center"><?php echo $det->ctdUnidadItem; ?></td>
                        <td class="description-cell">
                            <div class="product-name"><?php echo $det->desItem; ?></div>
                        </td>
                        <td class="price-cell text-right"><?php echo number_format($det->mtoValorUnitario, 2); ?></td>
                        <td class="price-cell text-right"><?php echo number_format($det->mtoValorVentaItem, 2); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Totals Section -->
            <div class="totals-section">
                <div class="user-info">
                    <i class="fas fa-user-tie" style="color: #dc3545;"></i>
                    <div>
                        <div><strong>Cajero:</strong> <?php echo $cajero ? $cajero->username : ""; ?></div>
                        <small style="color: #666;">Fecha: <?php echo ($comp_cab && isset($comp_cab->fecEmision)) ? date("d/m/Y H:i", strtotime($comp_cab->fecEmision)) : ""; ?></small>
                    </div>
                </div>

                <div class="totals-table">
                    <table>
                        <tr><td>Op. Gratuita</td><td class="text-right">0.00</td></tr>
                        <tr><td>Op. Exonerada</td><td class="text-right"><?php echo number_format($total, 2); ?></td></tr>
                        <tr><td>Op. Inafecta</td><td class="text-right">0.00</td></tr>
                        <tr><td>Op. Gravada</td><td class="text-right">0.00</td></tr>
                        <tr><td>IGV (18%)</td><td class="text-right">0.00</td></tr>
                        <tr class="total-row" style="border-top: 2px solid #333;">
                            <td>TOTAL ANULADO</td>
                            <td class="text-right">S/ <?php echo number_format($total, 2); ?></td>
                        </tr>
                    </table>
                    <?php if(isset($sell->cash) && $sell->cash > 0): ?>
                    <div class="mt-2 small text-right text-muted">
                        Efectivo: S/ <?php echo number_format($sell->cash, 2); ?> | 
                        Vuelto: S/ <?php echo number_format($sell->cash - $total, 2); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="text-center mt-4 pt-3 border-top small text-muted">
                <p>Representación impresa de la Nota de Crédito Electrónica.<br>Consulte su validez en el portal de la SUNAT.</p>
            </div>
        </div>
    </div>

    <script src="plugins/jquery/jquery.min.js"></script>
    <script>
        $('#imprimir').click(function() {
            window.print();
        });
    </script>
</body>
</html>
	
<!-- /.content -->