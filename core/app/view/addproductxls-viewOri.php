<?php
// Función para generar un código de barras aleatorio de 13 dígitos
function generarCodigoBarras() {
    // El primer dígito debe ser 7 para mantener el formato EAN-13 (opcional)
    $primerDigito = '7'; // Puedes cambiarlo según tus necesidades
    
    // Generar los 12 dígitos restantes aleatorios
    $resto = '';
    for ($i = 0; $i < 12; $i++) {
        $resto .= mt_rand(0, 9);
    }
    
    $codigo = $primerDigito . $resto;
    
    // Calcular dígito de control (checksum) para EAN-13 válido
    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
        $sum += ($i % 2 === 0) ? $codigo[$i] * 1 : $codigo[$i] * 3;
    }
    $digitoControl = (10 - ($sum % 10)) % 10;
    
    return $codigo . $digitoControl;
}

// Función para verificar y generar código único
function obtenerCodigoBarrasUnico($conn, $codigoExistente = null) {
    if (!empty($codigoExistente)) {
        // Verificar si el código existente es válido (13 dígitos numéricos)
        if (preg_match('/^\d{13}$/', $codigoExistente)) {
            return $codigoExistente;
        }
    }
    
    // Generar nuevo código y verificar que no exista
    do {
        $nuevoCodigo = generarCodigoBarras();
        $stmt = $conn->prepare("SELECT COUNT(*) FROM product WHERE barcode = ?");
        $stmt->execute([$nuevoCodigo]);
        $existe = $stmt->fetchColumn();
    } while ($existe > 0);
    
    return $nuevoCodigo;
}

// Incluyo la clase
include "lib/simplexlsx.class.php";

// Configuración de la base de datos
$db_host = "localhost";
$db_name = "dbcarepharm";
$db_user = "milenio";
$db_pass = "armagedon";

try {
    // Verificar si se ha subido un archivo
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("No se ha subido ningún archivo o hubo un error en la carga.");
    }

    // Obtener información del archivo subido
    $file_tmp_path = $_FILES['image']['tmp_name'];
    $file_name = $_FILES['image']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Verificar que sea un archivo Excel
    if ($file_ext !== 'xlsx' && $file_ext !== 'xls') {
        throw new Exception("Solo se permiten archivos Excel (.xlsx, .xls)");
    }

    // Procesar el archivo Excel
    $xlsx = new SimpleXLSX($file_tmp_path);
    
    // Conectar a la base de datos
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparar la sentencias SQL
    //Seleccionar Proveedor
    $stmtSelectProveedor = $conn->prepare("select id from person where numero_documento=?");
    //Isertar Proveedor
    $stmtInsertProveedor = $conn->prepare("insert into person (tipo_persona, numero_documento, name, lastname, address1, email1, phone1, kind, created_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    //Seleccionar Producto
    $stmtSelectProducto = $conn->prepare("select id from product where barcode=?");
    //Insertar Producto
    $stmtInsertProducto = $conn->prepare("insert into product (image, barcode, name, description, stock, is_stock, inventary_min, price_in, price_out, price_may, unit, presentation, user_id, category_id, created_at, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    //Actualizar stock Producto
    $stmtUpdateProducto = $conn->prepare("update product set stock=?, is_stock=?, inventary_min=?, price_in=?, price_out=?, price_may=?, unit=?, presentation=?, user_id=?, category_id=?, created_at=?, is_active=? where id=?");
    //Insertar Compra
    $sqlInsertCompra = $conn->prepare("insert into sell (person_id,tipo_comprobante, serie, comprobante,fecha_emi, user_id,operation_type_id,created_at,total,cash,discount) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"); 	
    //Insertar Operación
    $stmtInsertOperacion = $conn->prepare("insert into operation (product_id, q, prec_alt, descuento, operation_type_id, sell_id, created_at, descripcion, idpaquete) values (?, ?, ?, ?, ?, ?, ?, ?, ?)");	
		
    // Contador de registros insertados
    $inserted_rows = 0;

    // Obtener las filas del Excel (empezando desde la fila 2 para omitir el encabezado)
    $rows = $xlsx->rows();
    array_shift($rows); // Eliminar la primera fila (encabezados)

    $rows = array_filter($rows, function($fila) {
        foreach ($fila as $valor) {
            if ($valor !== '' && $valor !== null) {
                return true;
            }
        }
        return false;
    });

    echo "Total de filas: ".count($rows)."<br>\n";
    echo "Total de columnas: ".count($rows[0])."<br>\n";

    foreach ($rows as $fields) {
        // Mapear los campos del Excel a las columnas de la base de datos

        // Obtener el proveedor        
        $proveedor = $fields[16]; 
        $rucproveedor = $fields[17];
        $sede = $fields[18];
        $stmtSelectProveedor->execute([$rucproveedor]);
        $proveedorExistente = $stmtSelectProveedor->fetch(PDO::FETCH_ASSOC);
        if ($proveedorExistente) {
            $idproveedor = $proveedorExistente['id'];
        } else {
            // Insertar nuevo proveedor
            $stmtInsertProveedor->execute([
                1,
                $rucproveedor,
                $proveedor,
                '',
                '',
                '',
                '',
                2,
                date('Y-m-d H:i:s')
            ]);
            $idproveedor = $conn->lastInsertId();
        }
        // Obtener el producto
        $image = 'medgen.png';
        $barcode = $barcodetemp;
        $name = $fields[1]; 
        $description = '-';
        $stock = !empty($fields[7]) ? (int)$fields[7] : 0;
        $is_stock = 1; 
        $inventary_min = 10; 
        $price_in = !empty($fields[10]) ? (float)$fields[10] : 0;
        $price_out = !empty($fields[13]) ? (float)$fields[13] : 0;
        $price_may = !empty($fields[14]) ? (float)$fields[14] : 0;
        $anaquel = 'A';
        $unit = $fields[3];
        $presentation = $fields[2];
        $user_id = $_SESSION["user_id"];
        $category_id = 1;
        $created_at = date('Y-m-d H:i:s'); 
        $is_active = 1;

        $codigoExistente = !empty($fields[15]) ? $fields[15] : null;
        $barcodetemp = obtenerCodigoBarrasUnico($conn, $codigoExistente);

        $stmtSelectProducto->execute([$barcodetemp]);
        $productoExistente = $stmtSelectProducto->fetch(PDO::FETCH_ASSOC);

        if ($productoExistente) {
            // Actualizar producto existente
            $stmtUpdateProducto->execute([
                $image,
                $barcodetemp,
                $name,
                $description,
                $stock,
                $is_stock,
                $inventary_min,
                $price_in,
                $price_out,
                $price_may,
                $unit,
                $presentation,
                $user_id,
                $category_id,
                $created_at,
                $is_active,
                $productoExistente['id']
            ]);
            $idproducto = $productoExistente['id'];
        } else {
            // Insertar nuevo producto
            $stmtInsertProducto->execute([
                $image,
                $barcodetemp,
                $name,
                $description,
                $stock,
                $is_stock,
                $inventary_min,
                $price_in,
                $price_out,
                $price_may,
                $unit,
                $presentation,
                $user_id,
                $category_id,
                date('Y-m-d H:i:s'),
                1
            ]);
            $idproducto = $conn->lastInsertId();
        }
        
        // Insertar compra
        $comprobante = explode('-',$fields[11]);
        $serie = $comprobante[0];
        $numcom = $comprobante[1];
        $fechcompra = $fields[19];
        // Convertir la fecha de dd/mm/yyyy a yyyy-mm-dd
        $fecha_emi = null;
        if (!empty($fechcompra)) {
            $fechaObj = DateTime::createFromFormat('d/m/Y', $fechcompra);
            if ($fechaObj !== false) {
                $fecha_emi = $fechaObj->format('Y-m-d');
            } else {
                $fecha_emi = date('Y-m-d');
            }
        } else {
            $fecha_emi = date('Y-m-d');
        }
        
        $stmtInsertCompra->execute([
            $idproveedor,
            1,
            $serie,
            $numcom,
            $fecha_emi,
            $user_id,
            1,
            date('Y-m-d H:i:s'),
            $price_in,
            $price_in,
            0
        ]); 
        
        $idcompra = $conn->lastInsertId();

        // Insertar operación
        $stmtInsertOperacion->execute([
            $idproducto,
            $stock,
            $price_in,
            0,
            1,
            $idcompra,
            date('Y-m-d H:i:s'),
            '',
            '' 
        ]);

        $inserted_rows++;
    }

    // Redireccionar con mensaje de éxito
    header("Location: ./?view=importarexcel&success=1&rows=$inserted_rows");
    exit(0);

} catch (Exception $e) {
    // Redireccionar con mensaje de error
    header("Location: ./?view=importarexcel&error=" . urlencode($e->getMessage()));
    exit(0);
}
?>