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
    
    $products = SellData::getSells($fechaSd, $fechaEd, $user_id);
    $plin     = json_decode(json_encode(SellData::getVentasOtroTipoPago('X',2,$fechaSd, $fechaEd,$user_id)),true)['total'];
    $yape     = json_decode(json_encode(SellData::getVentasOtroTipoPago('X',3,$fechaSd, $fechaEd,$user_id)),true)['total'];
    $tdebito  = json_decode(json_encode(SellData::getVentasOtroTipoPago('X',4,$fechaSd, $fechaEd,$user_id)),true)['total'];
    $tcredito = json_decode(json_encode(SellData::getVentasOtroTipoPago('X',5,$fechaSd, $fechaEd,$user_id)),true)['total'];	
    
    // Procesar resultados
    $data = [];
    foreach ($products as $sell) {
        $usuario = UserData::getById($sell->user_id);
        $cliente = PersonData::getById($sell->person_id);

        $notacomprobar = $sell->serie . "-" . $sell->comprobante; 
		$probar = Not_1_2Data::getByIdComprobado($notacomprobar);

        switch ($sell->tipo_pago){
            case 1:
                $medioPago = "EFECTIVO";
                break;
			case 2:
				$medioPago = "PLIN";
				break;
			case 3:
				$medioPago = "YAPE";
				break;
			case 4:
				$medioPago = "TARJETA DEBITO";
				break;
			case 5:
				$medioPago = "TARJETA CREDITO";
				break;	
			default:
				$medioPago = "OTRO MEDIO DE PAGO";
				break;				
		}
								
        try {
            $db = new SQLite3($dbPath);
            $query = "SELECT * FROM DOCUMENTO WHERE NUM_DOCU = '" . $sell->serie . "-" . $sell->comprobante . "'";
            $results = $db->query($query);
            
            // Verificar si la consulta devolvió resultados
            if ($results === false) {
                die("Error en la consulta SQL: " . $db->lastErrorMsg());
            }
            
            $documento = $results->fetchArray(SQLITE3_ASSOC);
            
            // Si no hay resultados, asignar valores por defecto
			if ($documento === false) {
                $documento = [
                    'FEC_GENE' => null,
                    'FEC_ENVI' => null,
                    'FEC_CARG' => null,
                    'TIP_DOCU' => null,
                    'NUM_DOCU' => null,
                    'NUM_RUC' => null,
                    'NOM_ARCH' => null,
                    'TIP_ARCH' => null,
                    'DES_OBSE' => null,
                    'FIRM_DIGITAL' => null,
                    'IND_SITU' => null
                ];
            }
            
            // Asignar valores con operador ternario y validación
			$estado = "";
            $fechaGeneracion = $documento['FEC_GENE'] ?? '-';
            $fechaEnvio = $documento['FEC_ENVI'] ?? '-';
            $fechaCarga = $documento['FEC_CARG'] ?? '-';
            $tipoComprobante = $documento['TIP_DOCU'] ?? '-';
            $numeroComprobante = $documento['NUM_DOCU'] ?? '-';
            $numeroRuc = $documento['NUM_RUC'] ?? '-';
            $nombreArchivo = $documento['NOM_ARCH'] ?? '-';
            $tipoArchivo = $documento['TIP_ARCH'] ?? '-';
            $observaciones = $documento['DES_OBSE'] ?? '-';
            $firmadoDigital = $documento['FIRM_DIGITAL'] ?? '-';
            $estadoSituacion = $documento['IND_SITU'] ?? '-';

            $comprobanteXML = $nombreArchivo . ".xml";
			$comprobanteCDR = "R" . $nombreArchivo . ".zip";

			// Buscar situación (con validación)
			$situacion = array_filter($listaSituacion['ListaSituacion'], function($item) use ($estadoSituacion) {
                return $item['id'] == $estadoSituacion;
            });

			// Obtener nombre de situación (si existe)
			$nombreSituacion = !empty($situacion) ? current($situacion)['nombre'] : 'Ejecutar Facturador sunat';
			$estado = (isset($probar->TIPO_DOC) && $probar->TIPO_DOC ==7) ? "N.CRE: ".$probar->SERIE."-".$probar->COMPROBANTE :$nombreSituacion;
							
			$descargarXML = false;
			$descargarCDR = false;

			if($estadoSituacion == "02" || $estadoSituacion == "07" || $estadoSituacion == "08" || $estadoSituacion == "09") {
				$descargarXML = true;
			}elseif($estadoSituacion == "03" || $estadoSituacion == "04" || $estadoSituacion == "05" || $estadoSituacion == "10" || $estadoSituacion == "11" || $estadoSituacion == "12") {
				$descargarXML = true;
				$descargarCDR = true;
			}
									
		} catch (Exception $e) {
			die("Error al conectar o consultar la base de datos: " . $e->getMessage());
		} 

		$fechaObj = new DateTime($sell->created_at);
		$fechaFormateada = $fechaObj->format('d/m/Y H:i:s');
        $background = '';

        // Determinar el color de fondo según el estado
        if (isset($probar)) {
            if ($probar->TIPO_DOC == 8) {
                $background = "#C2FCCF"; // Verde claro para tipo 8
            } elseif ($probar->TIPO_DOC == 7) {
                $background = "#FFC4C4"; // Rojo claro para tipo 7
            }
        } else {
            // Si no hay comprobación, usar un color por defecto
            $background = "#FFFFFF"; // Blanco por defecto
        }

        //Determinar links para ver comprobante y descargar XML y CDR
        $verComprobanteLink = '<a href="./?view=onesell&id='.$sell->id.'&tipodoc='.$sell->tipo_comprobante.'" class="btn btn-xs btn-default"><i class="fas fa-eye"></i></a>';
        $verNotaCreditoLink = (isset($probar->TIPO_DOC) && $probar->TIPO_DOC ==7) ? '<a href="./?view=notacreditoboletat&num='.$probar->SERIE.'-'.$probar->COMPROBANTE.'" class="btn btn-xs btn-danger" title="Ver Nota de Credito"><i class="fas fa-file-invoice"></i></a>': '';
        $descargarXMLLink = $descargarXML ? '<a href="'.$rutaXML.'/'.$comprobanteXML.'" class="btn btn-xs btn-default" download="'.$comprobanteXML.'"><i class="fas fa-download"></i> XML</a>' : '';
        $descargarCDRLink = $descargarCDR ? '<a href="'.$rutaCDR.'/'.$comprobanteCDR.'" class="btn btn-xs btn-default" target="_blank"><i class="fas fa-download"></i> CDR</a>' : '';

        // Calcular capital si es admin
        $capital = 0;
        if ($admin == 1) {
            $objOper = OperationData::getAllProductsBySellId($sell->id);
            foreach ($objOper as $oper) {
                $objProd = ProductData::getById($oper->product_id);
                $capital += (isset($probar->TIPO_DOC) && $probar->TIPO_DOC ==7)  ? 0 : $oper->q * $objProd->price_in;
            }
            $tc += $capital;
        }
        
        $total = (isset($probar->TIPO_DOC) && $probar->TIPO_DOC ==7)  ? 0 : $sell->total;
        $tv += $total;
        
        $data[] = [
            'background' => $background,
            'verComprobante' => $verComprobanteLink,
            'verNotaCredito' => $verNotaCreditoLink,
            'comprobante' => $sell->serie.'-'.$sell->comprobante,
            'cliente' => $cliente->name . ' ' . $cliente->lastname,
            'importe' => $total,
            'medioPago' => $medioPago,
            'fecha' => $fechaFormateada,
            'fechaEnvio' => $fechaEnvio,
            'estado' => $estado,
            'descargarXML' => $descargarXMLLink,
            'descargarCDR' =>$descargarCDRLink,
            'usuario' => $usuario->username
        ];
    }

    $response['data'] = $data;
    $response['total_ventas'] = $tv;
    $response['total_capital'] = number_format($tc, 2, '.', ',');
    $response['total_ganancia'] = number_format($tv - $tc, 2, '.', ',');
    $response['total_plin'] = number_format($plin, 2, '.', ',');
    $response['total_yape'] = number_format($yape, 2, '.', ',');
    $response['total_tdebito'] = number_format($tdebito, 2, '.', ',');
    $response['total_tcredito'] = number_format($tcredito, 2, '.', ',');
    $response['success'] = true;

} catch (Exception $e) {
    http_response_code(500);
    $response['error'] = $e->getMessage();
    $response['success'] = false;
}

echo json_encode($response);
exit;
?>