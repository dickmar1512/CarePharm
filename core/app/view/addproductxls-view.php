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
    if (!empty($codigoExistente) && preg_match('/^\d{13}$/', $codigoExistente)) {
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
    
    $stmtSelectProducto = $conn->prepare("SELECT id FROM product WHERE barcode = ?");
    $stmtInsertProducto = $conn->prepare("INSERT INTO product (image, barcode, name, description, stock, is_stock, inventary_min, price_in, price_out, price_may, unit, presentation, user_id, category_id, fecha_venc, laboratorio, created_at, is_active) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtUpdateProducto = $conn->prepare("UPDATE product SET stock = stock + ? , price_in = ?, price_out = ?, price_may = ?, fecha_venc = ?, user_id = ? 
                                         WHERE id = ?");
    
    $stmtSelectCompra = $conn->prepare("SELECT id FROM sell WHERE comprobante = ? AND serie = ?");
    $stmtInsertCompra = $conn->prepare("INSERT INTO sell (person_id, tipo_comprobante, serie, comprobante, fecha_emi, user_id, operation_type_id, created_at, total, cash, discount) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");     
    $stmtInsertOperacion = $conn->prepare("INSERT INTO operation (product_id, q, prec_alt, descuento, operation_type_id, sell_id, created_at, descripcion, idpaquete) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
  
    $stmtInsertLote = $conn->prepare("INSERT INTO lote (id_prod, num_lot, fech_ing, id_sell, user_id) 
                                          VALUES (?, ?, NOW(), ?, ?)");                                              
    
    $stmtSelectUnidad = $conn->prepare("SELECT id FROM unidad_medida WHERE name = ?");
    $stmtInsertUnidad = $conn->prepare("INSERT INTO unidad_medida (name,sigla) VALUES (?,'-')");                                    

    // Obtener y filtrar filas del Excel
    $rows = $xlsx->rows();
    array_shift($rows); // Eliminar encabezados
    
    $rows = array_filter($rows, function($fila) {
        return !empty(array_filter($fila, function($valor) {
            return $valor !== '' && $valor !== null;
        }));
    });

    $inserted_rows = 0;
    $ultimoRucProcesado = null;
    $idcompra = null;

    // Inicia transacción para poder hacer rollback si hay errores
    $conn->beginTransaction();

    foreach ($rows as $fields) {
        // Procesar proveedor
        $rucproveedor = trim($fields[17]);
        $proveedor = trim($fields[16]);
        
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
        $stmtSelectUnidad->execute([trim($fields[3])]);
        $unidadExistente = $stmtSelectUnidad->fetch(PDO::FETCH_ASSOC);
        if (!$unidadExistente) {
            $stmtInsertUnidad->execute([trim($fields[3])]);
            $idunidad = $conn->lastInsertId();
        } else {
            $idunidad = $unidadExistente['id'];
        }

        // Procesar producto
        $codigoExistente = !empty($fields[15]) ? $fields[15] : null;
        $barcode = obtenerCodigoBarrasUnico($conn, $codigoExistente);
        
        $productData = [
            'image' => 'medgen.png',
            'barcode' => $barcode,
            'name' => trim($fields[1]),
            'description' => '-',
            'stock' => !empty($fields[7]) ? (int)$fields[7] : 0,
            'is_stock' => 1,
            'inventary_min' => 10,
            'price_in' => !empty($fields[10]) ? (float)$fields[10] : 0,
            'price_out' => !empty($fields[13]) ? (float)$fields[13] : 0,
            'price_may' => !empty($fields[14]) ? (float)$fields[14] : 0,
            'unit' => $idunidad,
            'presentation' => trim($fields[3]),
            'user_id' => $_SESSION["user_id"],
            'category_id' => 1,
            'fecha_venc' => $fields[5],
            'laboratorio' => trim($fields[4]),
            'created_at' => date('Y-m-d H:i:s'),
            'is_active' => 1
        ];

        $stmtSelectProducto->execute([$barcode]);
        $productoExistente = $stmtSelectProducto->fetch(PDO::FETCH_ASSOC);
        
        if ($productoExistente) {
            // Actualizar producto existente
            $stmtUpdateProducto->execute([
                $productData['stock'],
                $productData['price_in'],
                $productData['price_out'],
                $productData['price_may'],
                $productData['fecha_venc'],
                $productData['user_id'],
                $productoExistente['id']
            ]);
            $idproducto = $productoExistente['id'];
        } else {
            // Insertar nuevo producto
            $stmtInsertProducto->execute([
                $productData['image'],
                $productData['barcode'],
                $productData['name'], 
                $productData['description'], 
                $productData['stock'], 
                $productData['is_stock'], 
                $productData['inventary_min'], 
                $productData['price_in'],
                $productData['price_out'], 
                $productData['price_may'],
                $productData['unit'], 
                $productData['presentation'], 
                $productData['user_id'],
                $productData['category_id'],
                $productData['fecha_venc'],
                $productData['laboratorio'],
                $productData['created_at'],
                $productData['is_active']
            ]);
            $idproducto = $conn->lastInsertId();
        }
       
        // Procesar compra (solo si el RUC es diferente al anterior)
        if ($rucproveedor !== $ultimoRucProcesado) {
            $comprobante = explode('-', $fields[11]);
            $serie = trim($comprobante[0]);
            $numcom = trim($comprobante[1] ?? '');
            
            // Validar si la compra ya existe
            $stmtSelectCompra->execute([$numcom, $serie]);
            $compraExistente = $stmtSelectCompra->fetch(PDO::FETCH_ASSOC);
            
            if (!$compraExistente) {
                // Formatear fecha
                $fechcompra = trim($fields[19]);
                $fecha_emi = date('Y-m-d');
                
                if (!empty($fechcompra)) {
                    $fechaObj = DateTime::createFromFormat('d/m/Y', $fechcompra);
                    if ($fechaObj !== false) {
                        $fecha_emi = $fechaObj->format('Y-m-d');
                    }
                }
                
                // Insertar nueva compra
                $stmtInsertCompra->execute([
                    $idproveedor,
                    1, // tipo_comprobante
                    $serie,
                    $numcom,
                    $fecha_emi,
                    $_SESSION["user_id"],
                    1, // operation_type_id
                    date('Y-m-d H:i:s'),
                    $productData['price_in'],
                    $productData['price_in'],
                    0 // discount
                ]);
                
                $idcompra = $conn->lastInsertId();
                $ultimoRucProcesado = $rucproveedor;
            } else {
                $idcompra = $compraExistente['id'];
            }
        }

        // Insertar operación solo si tenemos una compra válida
        if ($idcompra) {
            $stmtInsertOperacion->execute([
                $idproducto,
                $productData['stock'],
                $productData['price_in'],
                0, // descuento
                1, // operation_type_id
                $idcompra,
                date('Y-m-d H:i:s'),
                '', // descripcion
                '' // idpaquete
            ]);
            
            $inserted_rows++;
        }

         // Insertar lote
        $stmtInsertLote->execute([
            $idproducto,
            trim($fields[8]),
            $idcompra,
            $_SESSION["user_id"]
        ]);

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