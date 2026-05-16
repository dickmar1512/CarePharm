<?php
header('Content-Type: application/json');
$listaSituacion = [
		"ListaSituacion" => [
			["id" => "01", "nombre" => "Por Generar XML"],
			["id" => "02", "nombre" => "XML Generado"],
			["id" => "03", "nombre" => "Enviado y Aceptado SUNAT"],
			["id" => "04", "nombre" => "Enviado y Aceptado SUNAT con Obs."],
			["id" => "05", "nombre" => "Rechazado por SUNAT"],
			["id" => "06", "nombre" => "Con Errores"],
			["id" => "07", "nombre" => "Por Validar XML"],
			["id" => "08", "nombre" => "Enviado a SUNAT Por Procesar"],
			["id" => "09", "nombre" => "Enviado a SUNAT Procesando"],
			["id" => "10", "nombre" => "Rechazado por SUNAT"],
			["id" => "11", "nombre" => "Enviado y Aceptado SUNAT"],
			["id" => "12", "nombre" => "Enviado y Aceptado SUNAT con Obs."]
		]
	];

	$dbPath = '../efact1.3.4/bd/BDFacturador.db';	
	$rutaXML = '../efact1.3.4/sunat_archivos/sfs/FIRMA';
	$rutaCDR = '../efact1.3.4/sunat_archivos/sfs/RPTA';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

try {
    $response = [
        'data' => [],
        'total_ventas' => '0.00',
        'total_capital' => '0.00',
        'total_ganancia' => '0.00',
        'success' => false
    ];

    $admin = UserData::getById($_SESSION["user_id"])->is_admin;
    $tv = 0;
    $tc = 0;

    // Procesar parámetros de fecha
    $fechaSd = date('Y-m-d');
    $fechaEd = date('Y-m-d');
    
    if (isset($_GET["sd"]) && isset($_GET["ed"]) && $_GET["sd"] != "" && $_GET["ed"] != "") { 
        $fechaini = DateTime::createFromFormat('d/m/Y', $_GET['sd']);
        $fechafin = DateTime::createFromFormat('d/m/Y', $_GET['ed']);
        
        if ($fechaini && $fechafin) {
            $fechaSd = $fechaini->format('Y-m-d');
            $fechaEd = $fechafin->format('Y-m-d');
        } else {
            // Reintentar con formato Y-m-d si d/m/Y falla
            $fechaini = DateTime::createFromFormat('Y-m-d', $_GET['sd']);
            $fechafin = DateTime::createFromFormat('Y-m-d', $_GET['ed']);
            if($fechaini && $fechafin){
                $fechaSd = $fechaini->format('Y-m-d');
                $fechaEd = $fechafin->format('Y-m-d');
            }
        }
    }
    
    // Obtener datos según filtros
    $user_id = isset($_GET["user_id"]) ? intval($_GET["user_id"]) : 0;
    
    $products = SellData::getSells($fechaSd, $fechaEd, $user_id);
    
    $plin = 0;
    $yape = 0;
    $tdebito = 0;
    $tcredito = 0;
    $efectivo = 0;
    
    // Conexión SQLite única fuera del loop
    $sqliteDb = null;
    if (file_exists($dbPath) && class_exists('SQLite3')) {
        try {
            $sqliteDb = new SQLite3($dbPath, SQLITE3_OPEN_READONLY);
        } catch (Exception $e) {
            // Ignorar error de conexión a SQLite para no romper todo el reporte
            error_log("Error al conectar a SQLite: " . $e->getMessage());
        }
    }

    // Procesar resultados
    $data = [];
    foreach ($products as $sell) {
        $usuario = UserData::getById($sell->user_id);
        
        $cliente = null;
        if (!empty($sell->person_id)) {
            $cliente = PersonData::getById($sell->person_id);
        }

        $notacomprobar = $sell->serie . "-" . $sell->comprobante; 
		$probar = Not_1_2Data::getByIdComprobado($notacomprobar);

        switch ($sell->tipo_pago){
            case 1: $medioPago = "EFECTIVO"; break;
			case 2: $medioPago = "PLIN"; break;
			case 3: $medioPago = "YAPE"; break;
			case 4: $medioPago = "TARJETA DEBITO"; break;
			case 5: $medioPago = "TARJETA CREDITO"; break;	
			default: $medioPago = "OTRO MEDIO DE PAGO"; break;				
		}
								
        $documento = false;
        if ($sqliteDb) {
            $query = "SELECT * FROM DOCUMENTO WHERE NUM_DOCU = '" . $sell->serie . "-" . $sell->comprobante . "'";
            $results = $sqliteDb->query($query);
            if ($results) {
                $documento = $results->fetchArray(SQLITE3_ASSOC);
            }
        }
            
        // Si no hay resultados o no hay DB, asignar valores por defecto
        if ($documento === false) {
            $documento = [
                'FEC_GENE' => null, 'FEC_ENVI' => null, 'FEC_CARG' => null,
                'TIP_DOCU' => null, 'NUM_DOCU' => null, 'NUM_RUC' => null,
                'NOM_ARCH' => null, 'TIP_ARCH' => null, 'DES_OBSE' => null,
                'FIRM_DIGITAL' => null, 'IND_SITU' => null
            ];
        }
        
        // Asignar valores
        $fechaEnvio = $documento['FEC_ENVI'] ?? '-';
        $estadoSituacion = $documento['IND_SITU'] ?? '-';
        $nombreArchivo = $documento['NOM_ARCH'] ?? '-';

        $comprobanteXML = $nombreArchivo . ".xml";
        $comprobanteCDR = "R" . $nombreArchivo . ".zip";

        // Buscar situación
        $situacion = array_filter($listaSituacion['ListaSituacion'], function($item) use ($estadoSituacion) {
            return $item['id'] == $estadoSituacion;
        });

        $nombreSituacion = !empty($situacion) ? current($situacion)['nombre'] : 'Ejecutar Facturador sunat';
        $estado = (isset($probar->TIPO_DOC) && $probar->TIPO_DOC == 7) ? "N.CRE: ".$probar->SERIE."-".$probar->COMPROBANTE : $nombreSituacion;
                        
        $descargarXML = false;
        $descargarCDR = false;

        if(in_array($estadoSituacion, ["02", "07", "08", "09"])) {
            $descargarXML = true;
        } elseif(in_array($estadoSituacion, ["03", "04", "05", "10", "11", "12"])) {
            $descargarXML = true;
            $descargarCDR = true;
        }

		$fechaObj = new DateTime($sell->created_at);
		$fechaFormateada = $fechaObj->format('d/m/Y H:i:s');
        $isCreditNote = (isset($probar->TIPO_DOC) && $probar->TIPO_DOC == 7);
        $isRejected = in_array($estadoSituacion, ["05", "10", "06"]); // Rechazado / Errores
        $isAnnulled = ($sell->estado == 0 || stripos($nombreSituacion, 'anulad') !== false || stripos($nombreSituacion, 'baja') !== false);
        
        $isInvalid = ($isCreditNote || $isRejected || $isAnnulled);

        if ($isInvalid) {
            $background = "#FFC4C4";
            if ($sell->estado == 0) { $estado = "ANULADO SISTEMA"; }
        } else {
            if (isset($probar->TIPO_DOC) && $probar->TIPO_DOC == 8) { 
                $background = "#C2FCCF"; 
            } else {
                $background = "#FFFFFF";
            }
        }

        $verComprobanteLink = '<a href="./?view=onesell&id='.$sell->id.'&tipodoc='.$sell->tipo_comprobante.'" class="btn btn-xs btn-default"><i class="fas fa-eye"></i></a>';
        $verNotaCreditoLink = $isCreditNote ? '<a href="./?view=notacreditoboletat&num='.$probar->SERIE.'-'.$probar->COMPROBANTE.'" class="btn btn-xs btn-danger" title="Ver Nota de Credito"><i class="fas fa-file-invoice"></i></a>': '';
        $descargarXMLLink = $descargarXML ? '<a href="'.$rutaXML.'/'.$comprobanteXML.'" class="btn btn-xs btn-default" download="'.$comprobanteXML.'"><i class="fas fa-download"></i> XML</a>' : '';
        $descargarCDRLink = $descargarCDR ? '<a href="'.$rutaCDR.'/'.$comprobanteCDR.'" class="btn btn-xs btn-default" target="_blank"><i class="fas fa-download"></i> CDR</a>' : '';

        $capital = 0;
        if ($admin == 1 && !$isInvalid) {
            $objOper = OperationData::getAllProductsBySellId($sell->id);
            foreach ($objOper as $oper) {
                $objProd = ProductData::getById($oper->product_id);
                if($objProd){
                    $capital += $oper->q * $objProd->price_in;
                }
            }
            $tc += $capital;
        }
        
        $total = $isInvalid ? 0 : $sell->total;
        $tv += $total;
        
        if (!$isInvalid && in_array($sell->tipo_comprobante, [1, 3])) {
            switch ($sell->tipo_pago) {
                case 1: $efectivo += $total; break;
                case 2: $plin += $total; break;
                case 3: $yape += $total; break;
                case 4: $tdebito += $total; break;
                case 5: $tcredito += $total; break;
            }
        }
        
        $data[] = [
            'background' => $background,
            'verComprobante' => $verComprobanteLink,
            'verNotaCredito' => $verNotaCreditoLink,
            'comprobante' => $sell->serie.'-'.$sell->comprobante,
            'cliente' => $cliente ? $cliente->name . ' ' . $cliente->lastname : 'PÚBLICO GENERAL',
            'importe' => $total,
            'medioPago' => $medioPago,
            'fecha' => $fechaFormateada,
            'fechaEnvio' => $fechaEnvio,
            'estado' => $estado,
            'descargarXML' => $descargarXMLLink,
            'descargarCDR' => $descargarCDRLink,
            'usuario' => $usuario->username
        ];
    }

    if ($sqliteDb) $sqliteDb->close();

    $response['data'] = $data;
    $response['total_ventas'] = number_format($tv, 2, '.', '');
    $response['total_capital'] = number_format($tc, 2, '.', '');
    $response['total_ganancia'] = number_format($tv - $tc, 2, '.', '');
    $response['total_plin'] = number_format($plin, 2, '.', '');
    $response['total_yape'] = number_format($yape, 2, '.', '');
    $response['total_tdebito'] = number_format($tdebito, 2, '.', '');
    $response['total_tcredito'] = number_format($tcredito, 2, '.', '');
    $response['success'] = true;

} catch (Exception $e) {
    http_response_code(500);
    $response['error'] = $e->getMessage();
    $response['success'] = false;
}

echo json_encode($response);
exit;
?>