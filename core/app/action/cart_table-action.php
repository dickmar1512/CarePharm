<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$total = 0;
$dsctotal = 0;
$contador = 0;
?>
<?php if (isset($_SESSION["cart"]) && count($_SESSION["cart"]) > 0): ?>
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
                foreach ($_SESSION["cart"] as $index => $p):
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
                            <button type="button" onclick="removeItem(<?= $product->id ?>)" class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php $total = round($total, 2); ?>
    
    <!-- Total y Acciones -->
    <div class="total-section">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="total-amount">
                    IMPORTE TOTAL: S/ <span id="display_total"><?php echo number_format($total, 2, '.', ','); ?></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="action-buttons text-right">
                    <button type="button" onclick="clearCart()" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm ml-2 btn-submit-form">
                        <i class="fa fa-check"></i> <span class="text-btn-submit">Emitir</span> (F3)
                    </button>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="total" class="input_total" value="<?php echo $total; ?>">
    <input type="hidden" class="js_total_val" value="<?php echo $total; ?>">

<?php else: ?>
    <div class="text-center">
        <div class="icon-container">
            <i class="fa fa-inbox icon-inbox"></i>
            <p class="text-muted">Carrito vacío - Agregue productos para continuar</p>
        </div>
    </div>
    <input type="hidden" name="total" class="input_total" value="0">
    <input type="hidden" class="js_total_val" value="0">
<?php endif; ?>
