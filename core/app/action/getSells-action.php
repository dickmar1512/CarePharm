<?php
header('Content-Type: application/json');

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

try {
    $response = [
        'draw' => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'total_ventas' => '0.00',
        'total_capital' => '0.00',
        'total_ganancia' => '0.00'
    ];

    $admin = UserData::getById($_SESSION["user_id"])->is_admin;
    $tv = 0;
    $tc = 0;

    // Procesar parámetros de fecha
    $fechaSd = date('Y-m-d');
    $fechaEd = date('Y-m-d');
    
    if (isset($_GET["sd"]) && isset($_GET["ed"])) { 
        $fechaini = DateTime::createFromFormat('d/m/Y', $_GET['sd']);
        $fechafin = DateTime::createFromFormat('d/m/Y', $_GET['ed']);
        
        if ($fechaini && $fechafin) {
            $fechaSd = $fechaini->format('Y-m-d');
            $fechaEd = $fechafin->format('Y-m-d');
        }
    }
    
    // Obtener datos según filtros
    $user_id = isset($_GET["user_id"]) ? intval($_GET["user_id"]) : 0;
    
    if ($user_id > 0) {
        $products = SellData::getSellsXfechaUsuario($fechaSd, $fechaEd, $user_id);
    } else {
        $products = SellData::getSellsXfecha($fechaSd, $fechaEd);
    }
    
    // Procesar resultados
    $data = [];
    foreach ($products as $sell) {
        $usuario = UserData::getById($sell->user_id);
        $cliente = PersonData::getById($sell->person_id);
        
        // Calcular capital si es admin
        $capital = 0;
        if ($admin == 1) {
            $objOper = OperationData::getAllProductsBySellId($sell->id);
            foreach ($objOper as $oper) {
                $objProd = ProductData::getById($oper->product_id);
                $capital += $oper->q * $objProd->price_in;
            }
            $tc += $capital;
        }
        
        $total = $sell->total;
        $tv += $total;
        
        $data[] = [
            'id' => $sell->id,
            //'serie' => $sell->serie,
            'comprobante' => $sell->serie.'-'.$sell->comprobante,
            'cliente' => $cliente->name . ' ' . $cliente->lastname,
            'importe' => $total,
            'fecha' => $sell->created_at,
            'usuario' => $usuario->username,
            'tipo_comprobante' => $sell->tipo_comprobante
        ];
    }

    $response['recordsTotal'] = count($data);
    $response['recordsFiltered'] = count($data);
    $response['data'] = $data;
    $response['total_ventas'] = number_format($tv, 2, '.', ',');
    $response['total_capital'] = number_format($tc, 2, '.', ',');
    $response['total_ganancia'] = number_format($tv - $tc, 2, '.', ',');

} catch (Exception $e) {
    http_response_code(500);
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
exit;
?>