<?php
// Función para generar un código de barras EAN-13 válido
function generarCodigoBarras() {
    $primerDigito = '7';
    $resto = '';
    for ($i = 0; $i < 12; $i++) {
        $resto .= mt_rand(0, 9);
    }
    
    $codigo = $primerDigito . $resto;
    
    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
        $sum += ($i % 2 === 0) ? $codigo[$i] * 1 : $codigo[$i] * 3;
    }
    $digitoControl = (10 - ($sum % 10)) % 10;
    
    return $codigo . $digitoControl;
}

function obtenerCodigoBarrasUnico($conn, $codigoExistente = null) {
    if (!empty($codigoExistente)) {
        $codigoExistente = trim($codigoExistente);
        // Si viene de Excel en notación científica (ej. 7.70212E+13)
        if (is_numeric($codigoExistente) && stripos($codigoExistente, 'E') !== false) {
            $codigoExistente = sprintf('%.0f', (float)$codigoExistente);
        }
        return $codigoExistente;
    }
    
    do {
        $nuevoCodigo = generarCodigoBarras();
        $stmt = $conn->prepare("SELECT COUNT(*) FROM product WHERE barcode = ?");
        $stmt->execute([$nuevoCodigo]);
        $existe = $stmt->fetchColumn();
    } while ($existe > 0);
    
    return $nuevoCodigo;
}

include "lib/simplexlsx.class.php";

// Configuración de la base de datos
$db_host = "localhost";
$db_name = "dbcarepharm";
$db_user = "milenio";
$db_pass = "armagedon";

try {
    // Validar archivo subido
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("No se ha subido ningún archivo o hubo un error en la carga.");
    }

    $file_tmp_path = $_FILES['image']['tmp_name'];
    $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (!in_array($file_ext, ['xlsx', 'xls'])) {
        throw new Exception("Solo se permiten archivos Excel (.xlsx, .xls)");
    }

    // Procesar el archivo Excel
    $xlsx = new SimpleXLSX($file_tmp_path);
    
    // Conectar a la base de datos
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparar todas las sentencias SQL necesarias
    $stmtSelectProveedor = $conn->prepare("SELECT id FROM person WHERE numero_documento = ?");
    $stmtInsertProveedor = $conn->prepare("INSERT INTO person (tipo_persona, numero_documento, name, lastname, address1, email1, phone1, kind, created_at) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmtSelectProducto = $conn->prepare("SELECT id FROM product WHERE (cod_digemid <> '' AND cod_digemid = ?) OR (barcode <> '' AND barcode = ?) OR name = ?");
    $stmtInsertProducto = $conn->prepare("INSERT INTO product (image, barcode, name, description, stock, is_stock, inventary_min, price_in, price_out, price_may, unit, presentation, user_id, category_id, fecha_venc, laboratorio, reg_san, created_at, is_active, cod_digemid, principio_activo) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtUpdateProducto = $conn->prepare("UPDATE product SET stock = stock + ? , price_in = ?, price_out = ?, price_may = ?, fecha_venc = ?, user_id = ?, reg_san = ?, cod_digemid = ?, principio_activo = ? 
                                         WHERE id = ?");
    
    $stmtSelectCompra = $conn->prepare("SELECT id FROM sell WHERE estado = 1 and comprobante = ? AND serie = ? ");
    $stmtInsertCompra = $conn->prepare("INSERT INTO sell (person_id, tipo_comprobante, serie, comprobante, fecha_emi, user_id, operation_type_id, created_at, total, cash, discount, observacion) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");     
    $stmtInsertOperacion = $conn->prepare("INSERT INTO operation (product_id, q, prec_alt, descuento, operation_type_id, sell_id, created_at, descripcion, idpaquete) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
  
    $stmtInsertLote = $conn->prepare("INSERT INTO lote (id_prod, num_lot, fech_ing, id_sell, user_id) 
                                          VALUES (?, ?, NOW(), ?, ?)");                                              
    
    $stmtSelectUnidad = $conn->prepare("SELECT id FROM unidad_medida WHERE name = ?");
    $stmtInsertUnidad = $conn->prepare("INSERT INTO unidad_medida (name,sigla) VALUES (?,'-')");                                    

    // Nuevas sentencias para evitar duplicados y actualizar totales
    $stmtSelectOperacion = $conn->prepare("SELECT id, q FROM operation WHERE estado = 1 and product_id = ? AND sell_id = ?");
    $stmtUpdateOperacion = $conn->prepare("UPDATE operation SET q = q + ?, prec_alt = ? WHERE id = ?");
    
    $stmtSelectLoteExistente = $conn->prepare("SELECT id FROM lote WHERE id_prod = ? AND num_lot = ? AND id_sell = ?");
    
    $stmtUpdateSellTotal = $conn->prepare("UPDATE sell SET total = (SELECT SUM(q * prec_alt) FROM operation WHERE sell_id = ?), cash = (SELECT SUM(q * prec_alt) FROM operation WHERE sell_id = ?) WHERE id = ?");

    $stmtInsertHistory = $conn->prepare("INSERT INTO price_history (product_id, price_in, price_out, user_id, sell_id, created_at) 
                                        VALUES (?, ?, ?, ?, ?, ?)");

    // Obtener y filtrar filas del Excel
    $rows = $xlsx->rows();
    array_shift($rows); // Eliminar encabezados
    
    $rows = array_filter($rows, function($fila) {
        return !empty(array_filter($fila, function($valor) {
            return $valor !== '' && $valor !== null;
        }));
    });

    $inserted_rows = 0;
    $idcompra = null;
    $processedSells = []; // Para acumular IDs de compras y actualizar sus totales al final

    // Inicia transacción para poder hacer rollback si hay errores
    $conn->beginTransaction();

    foreach ($rows as $fields) {
        // Ignorar fila de encabezado si no fue eliminada por array_shift
        if (isset($fields[0]) && strtolower(trim($fields[0])) === 'cod_digemid') {
            continue;
        }

        // Procesar proveedor
        $rucproveedor = trim($fields[18] ?? '');
        $proveedor = trim($fields[17] ?? '');
        
        if (empty($rucproveedor)) continue; // Saltar filas sin RUC

        // Validar y obtener/insertar proveedor
        $stmtSelectProveedor->execute([$rucproveedor]);
        $proveedorExistente = $stmtSelectProveedor->fetch(PDO::FETCH_ASSOC);
        
        if ($proveedorExistente) {
            $idproveedor = $proveedorExistente['id'];
        } else {
            $stmtInsertProveedor->execute([
                1, // tipo_persona
                $rucproveedor,
                $proveedor,
                '',
                '',
                '',
                '',
                2, // kind (proveedor)
                date('Y-m-d H:i:s')
            ]);
            $idproveedor = $conn->lastInsertId();
        }
        
        // Procesar unidad de medida
        $nombreUnidad = !empty($fields[3]) ? trim($fields[3]) : 'UNIDAD';
        $stmtSelectUnidad->execute([$nombreUnidad]);
        $unidadExistente = $stmtSelectUnidad->fetch(PDO::FETCH_ASSOC);
        if (!$unidadExistente) {
            $stmtInsertUnidad->execute([$nombreUnidad]);
            $idunidad = $conn->lastInsertId();
        } else {
            $idunidad = $unidadExistente['id'];
        }

        // Procesar producto
        $codigoExistente = !empty($fields[16]) ? $fields[16] : null;
        $barcode = obtenerCodigoBarrasUnico($conn, $codigoExistente);
        
        $productData = [
            'image' => 'medgen.png',
            'barcode' => $barcode,
            'cod_digemid' => (!empty($fields[0]) && is_numeric(trim($fields[0]))) ? (int)trim($fields[0]) : 0,
            'name' => trim($fields[1]),
            'description' => '',
            'principio_activo' => trim($fields[2]),
            'presentation' => trim($fields[3]),
            'stock' => !empty($fields[8]) ? (int)$fields[8] : 0,
            'is_stock' => 1,
            'inventary_min' => 10,
            'price_in' => !empty($fields[11]) ? (float)str_replace(['S/', ' ', ','], ['', '', ''], $fields[11]) : 0,
            'price_out' => !empty($fields[14]) ? (float)str_replace(['S/', ' ', ','], ['', '', ''], $fields[14]) : 0,
            'price_may' => !empty($fields[15]) ? (float)str_replace(['S/', ' ', ','], ['', '', ''], $fields[15]) : 0,
            'unit' => $idunidad,
            'user_id' => $_SESSION["user_id"],
            'category_id' => 1,
            'fecha_venc' => !empty($fields[6]) ? $fields[6] : null,
            'laboratorio' => !empty($fields[4]) ? trim($fields[4]) : '-',
            'reg_san' => !empty($fields[5]) ? trim($fields[5]) : '-',
            'created_at' => date('Y-m-d H:i:s'),
            'is_active' => 1
        ];

        $stmtSelectProducto->execute([$productData['cod_digemid'], $barcode, $productData['name']]);
        $productoExistente = $stmtSelectProducto->fetch(PDO::FETCH_ASSOC);
        
        if ($productoExistente) {
            $stmtUpdateProducto->execute([
                $productData['stock'],
                $productData['price_in'],
                $productData['price_out'],
                $productData['price_may'],
                $productData['fecha_venc'],
                $productData['user_id'],
                $productData['reg_san'],
                $productData['cod_digemid'],
                $productData['principio_activo'],
                $productoExistente['id']
            ]);
            $idproducto = $productoExistente['id'];
        } else {
            $stmtInsertProducto->execute([
                $productData['image'], $productData['barcode'], $productData['name'], 
                $productData['description'], $productData['stock'], $productData['is_stock'], 
                $productData['inventary_min'], $productData['price_in'], $productData['price_out'], 
                $productData['price_may'], $productData['unit'], $productData['presentation'], 
                $productData['user_id'], $productData['category_id'], $productData['fecha_venc'],
                $productData['laboratorio'], $productData['reg_san'], $productData['created_at'], $productData['is_active'],
                $productData['cod_digemid'], $productData['principio_activo']
            ]);
            $idproducto = $conn->lastInsertId();
        }
       
        // Procesar compra (Header)
        $comprobante_input = trim($fields[12]);
        $parts = explode('-', $comprobante_input);
        $serie = trim($parts[0] ?? '');
        $numcom = trim($parts[1] ?? '');
        
        $stmtSelectCompra->execute([$numcom, $serie]);
        $compraExistente = $stmtSelectCompra->fetch(PDO::FETCH_ASSOC);
        
        if ($compraExistente) {
            $idcompra = $compraExistente['id'];
        } else {
            $fechcompra = trim($fields[20]);
            $fecha_emi = date('Y-m-d');
            if (!empty($fechcompra)) {
                $fechaObj = DateTime::createFromFormat('d/m/Y', $fechcompra);
                if ($fechaObj !== false) $fecha_emi = $fechaObj->format('Y-m-d');
            }
            
            $nro_guia = trim($fields[13] ?? '');
            $sede = trim($fields[19] ?? '');
            $observacion = "GUIA: $nro_guia | SEDE: $sede";

            $stmtInsertCompra->execute([
                $idproveedor, 1, $serie, $numcom, $fecha_emi, $_SESSION["user_id"], 1, 
                date('Y-m-d H:i:s'), 0, 0, 0, $observacion
            ]);
            $idcompra = $conn->lastInsertId();
        }

        if (!in_array($idcompra, $processedSells)) {
            $processedSells[] = $idcompra;
        }

        // Procesar Operación (Detalle) - Evitar duplicados consolidando cantidades
        $stmtSelectOperacion->execute([$idproducto, $idcompra]);
        $opExistente = $stmtSelectOperacion->fetch(PDO::FETCH_ASSOC);

        if ($opExistente) {
            $stmtUpdateOperacion->execute([$productData['stock'], $productData['price_in'], $opExistente['id']]);
        } else {
            $stmtInsertOperacion->execute([
                $idproducto, $productData['stock'], $productData['price_in'], 0, 1, 
                $idcompra, date('Y-m-d H:i:s'), '', ''
            ]);
        }
        
        // Insertar lote si no existe para esta compra/producto
        $numLote = !empty($fields[9]) ? trim($fields[9]) : 'S/L';
        $stmtSelectLoteExistente->execute([$idproducto, $numLote, $idcompra]);
        if (!$stmtSelectLoteExistente->fetch()) {
            $stmtInsertLote->execute([$idproducto, $numLote, $idcompra, $_SESSION["user_id"]]);
        }

        // REGISTRAR EN EL HISTORIAL DE PRECIOS
        $stmtInsertHistory->execute([
            $idproducto,
            $productData['price_in'],
            $productData['price_out'],
            $_SESSION["user_id"],
            $idcompra,
            date('Y-m-d H:i:s')
        ]);

        $inserted_rows++;
    }

    // ACTUALIZAR TOTALES DE TODAS LAS COMPRAS PROCESADAS
    foreach ($processedSells as $sid) {
        $stmtUpdateSellTotal->execute([$sid, $sid, $sid]);
    }

    // Punto de verificación 1
    error_log("DEBUG: Después de procesar proveedor - RUC: " . $rucproveedor);
    var_dump($idproveedor); // Verifica el ID del proveedor
    
    // Punto de verificación 2
    error_log("DEBUG: Antes de procesar producto - Código: " . $barcode);
    var_dump($productData); // Verifica los datos del producto
    
    $conn->commit();

    header("Location: ./?view=importarexcel&success=1&rows=$inserted_rows");
    exit(0);

} catch (Exception $e) {
    // header("Location: ./?view=importarexcel&error=" . urlencode($e->getMessage()));
    // exit(0);
    $conn->rollBack();
    
    // Registro detallado del error
    error_log("ERROR EN IMPORTACIÓN: " . $e->getMessage());
    error_log("TRACE: " . $e->getTraceAsString());
    
    // Muestra información detallada en pantalla (solo para desarrollo)
    echo "<h2>Error Detallado:</h2>";
    echo "<pre>";
    echo "Mensaje: " . htmlspecialchars($e->getMessage()) . "\n\n";
    echo "Archivo: " . $e->getFile() . " (Línea: " . $e->getLine() . ")\n\n";
    echo "Trace:\n" . htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
    
    // También puedes registrar la última consulta SQL ejecutada
    if (isset($stmt)) {
        error_log("Última consulta SQL: " . $stmt->queryString);
    }
    
    exit; // Detener la ejecución para analizar el error
}
?>