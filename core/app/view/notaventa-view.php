<?php 
$orden_id = $_GET["id"];
$orden = SellData::getById($orden_id);
$detalle = OperationData::getAllProductsBySellId($orden->id);
$cliente = PersonData::getById($orden->person_id);
$empresa = EmpresaData::getDatos();

$cajero = null;
$cajero = UserData::getById($orden->user_id)->username;

if (strlen($cliente->numero_documento) == 8):
	$docLabel = "DNI";
	$nomLabel = "SEÑOR(ES)";
else:
	$docLabel = "RUC";
	$nomLabel = "RAZON SOCIAL";
endif;
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

                <button class="btn btn-primary" id="imprimirNV80mm">
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
                        <label for="numeroComprobante" style="font-size: 0.9rem;">NOTA DE VENTA</label>
                    </div>
                    <div class="document-number" id="numeroComprobante" name="numeroComprobante"><?php echo $orden->serie . "-" . $orden->comprobante; ?></div>
                </div>
            </div>

            <!-- Customer Information Compacto -->
            <div class="customer-info">
                <div class="info-row">
                    <span class="info-label"><?= $docLabel ?></span>
                    <span class="info-value"><?php echo ": " . $cliente->numero_documento; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label"><?= $nomLabel ?></span>
                    <span class="info-value"><?php echo ": " . $cliente->lastname . ' ' . $cliente->name; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">DIRECCIÓN</span>
                    <span class="info-value"><?php echo ": " . $cliente->address1; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">FECHA:</span>
                    <span class="info-value"><?php echo ": " . $orden->created_at; ?></span>
                </div>
            </div>

            <!-- Products Table Compacto -->
            <table class="products-table">
                <thead>
                    <tr>
                        <th>CANT</th>
                        <th>DESCRIPCIÓN</th>
                        <th>P. UNIT.</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
						$total = 0;
						$productos = [];
						foreach ($detalle as $ope) {
                            $product = ProductData::getById($ope->product_id);
                            $subtotal = $ope->q * $ope->prec_alt;                            
							$productos[] = array("id" => $ope->id, "ctdUnidadItem" => $ope->q, "desItem" =>$product->name, "mtoValorUnitario" => $ope->prec_alt, "mtoValorVentaItem" =>  $subtotal);
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
                            $totalConDesc = $total - $orden->discount;
                            $numLetra = NumeroLetras::convertir(number_format($totalConDesc, 2, '.', ','));
						}
                        
                        $datosComprobante = array(
							"cliente"      => $cliente,
                            "venta"       => $orden,
                            "detalles"    => $productos,
                            "empresa"     => $empresa,
                            "cajero"      => $cajero,
                            "numLetra"    => $numLetra
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
                        <tr><td>Operación Gratuita</td><td>0.00</td></tr>
                        <tr><td>Operación Exonerada</td><td><?php echo number_format($total, 2, '.', ','); ?></td></tr>
                        <tr><td>Operación Inafecta</td><td>0.00</td></tr>
                        <tr><td>Operación Gravada</td><td>0.00</td></tr>
                        <tr><td>IGV (18%)</td><td>0.00</td></tr>
                        <tr class="total-row">
                            <td>TOTAL</td>
                            <td><?php echo number_format($total, 2, '.', ','); ?></td>
                        </tr>
                    </table>
                    <div class="amount-in-words">
                        SON: <?php echo $numLetra; ?>
                    </div>
                    <?php
						echo "<input type='hiddenxx' id='datosComprobante' name='datosComprobante' value='" . json_encode($datosComprobante) . "'>";
					?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>