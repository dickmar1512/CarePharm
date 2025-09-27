<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        $sells = SellData::getSellsUnBoxed();
        
        if (count($sells) == 0) {
            throw new Exception("No hay ventas para procesar");
        }
        
        // Procesar caja
        $box = new BoxData();
        $box->created_at = date("Y-m-d H:i:s");    
        $box->user_id = $_SESSION["user_id"];
        $totalBox = 0;
        $datosUsuario = UserData::getById($_SESSION["user_id"])->name ." ".UserData::getById($_SESSION["user_id"])->lastname;
        $usuario = UserData::getById($_SESSION["user_id"])->username;
        $fechaIngreso = date("d/m/Y H:i:s");        
        $b = $box->add(); // Guardar caja
        // Guardar denominaciones (nuevo)
        $denominations = $_POST['denominations'] ?? [];
        $box->b200 = $denominations['b200'] ?? 0;
        $box->b100 = $denominations['b100'] ?? 0;
        $box->b50 = $denominations['b50'] ?? 0;
        $box->b20 = $denominations['b20'] ?? 0;
        $box->b10 = $denominations['b10'] ?? 0;
        $box->m5 = $denominations['m5'] ?? 0;
        $box->m2 = $denominations['m2'] ?? 0;
        $box->m1 = $denominations['m1'] ?? 0;
        $box->c50 = $denominations['c50'] ?? 0;
        $box->c20 = $denominations['c20'] ?? 0;
        $box->c10 = $denominations['c10'] ?? 0;
        $box->id = $b[1]; // Asignar ID de caja
        
        $box->addDetalle(); // Guardar denominaciones en la base de datos
        
        foreach($sells as $sell) {
            $notacomprobar = $sell->serie . "-" . $sell->comprobante; 
            $probar = Not_1_2Data::getByIdComprobado($notacomprobar);
            $impNotaCredito = $probar->sumImpVenta ?? 0;
            
            $sell->box_id = $b[1];
            $sell->update_box();
            $totalBox += $sell->total - $impNotaCredito;
        }
        
        // Enviar correo
        $arraddress = $arrAddcc = array();
        //$arraddress[] = 'juan.irene@kalpg.com';
        $arraddress[] = 'dick.marlon.tamani.romayna@gmail.com';
        $arrAddcc[] = 'sagitatario.1982@gmail.com';
        //$arrAddcc[] = 'mayaya.ocampo@gmail.com';
        $asunto = "Cierre caja por Limite de efectivo en caja";
        
        $mailer = new CLSPHPMailer();
        $cuerpo = "<head>
                        <style>
                            .mi-tabla {
                                width: 100%;
                                border-collapse: collapse;
                                border: 1px solid #000;
                                font-family: Arial, sans-serif;
                            }
                            .mi-tabla thead {
                                border-bottom: 2px solid white; /* Solo el borde inferior blanco */
                            }
                            
                            .mi-tabla th {
                                background-color: #000;
                                color: #fff;
                                font-weight: bold;
                                padding: 5px;
                                text-align: left;
                                border: 1px solid #000;
                            }
                            
                            .mi-tabla td {
                                font-weight: bold;
                                padding: 10px;
                                border: 1px solid #000;
                            }
                            
                            .mi-tabla tr:nth-child(even) {
                                background-color: #f2f2f2;
                            }

                            .subtotal{
                                background-color: #ffc107 !important;
                            }

                            .total{
                                background-color: #28a745 !important;
                                color: #fff !important;
                            }        
                        </style>
                    </head>"; 
        $cuerpo .= "<h3>Srs. Botica Alfonso Ugarte.</h3>";
        $cuerpo .= "<h5>La presente es para comunicar que se llegó al limite de efectivo por día y por cajero. Se detalla a continuación.</h5>";
        $cuerpo .= "<table class='mi-tabla'>
                    <thead>                        
                        <tr>
                            <th>Cajero</th>
                            <th>Usuario</th>
                            <th>Monto de cierre</th>
                            <th>Fecha y hora de cierre</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>".$datosUsuario."</td>
                            <td>".$usuario."</td>
                            <td>S/ ".number_format($totalBox, 2, ".", ",")."</td>
                            <td>".$fechaIngreso."</td>
                        </tr>
                    </tbody>
                </table>";
        
        // Agregar información de denominaciones al correo
        $denominationsData = json_decode(json_encode($denominations), true);
        $cuerpo .= "<h4>Desglose de efectivo:</h4><ul>";
        foreach ($denominationsData as $key => $value) {
            if ($value > 0) {
                // Determinar el tipo de denominación
                $tipo = substr($key, 0, 1); // Primera letra (b, m o c)
                $valor = substr($key, 1);    // Número después de la letra
                
                // Formatear el nombre y el valor
                switch($tipo) {
                    case 'b':
                        $denominationName = "Billete S/. $valor";
                        $unidades = "$value unidad(es)";
                        break;
                    case 'm':
                        $denominationName = "Moneda S/. $valor";
                        $unidades = "$value unidad(es)";
                        break;
                    case 'c':
                        $valorFormateado = number_format($valor/100, 2, '.', '');
                        $denominationName = "Moneda S/. $valorFormateado";
                        $unidades = "$value unidad(es)";
                        break;
                    default:
                        $denominationName = $key;
                        $unidades = "S/ ".number_format($value, 2, '.', '')." Soles.";
                }
                
                $cuerpo .= "<li>$denominationName: $unidades</li>";
            }
        }
        $cuerpo .= "</ul>";
        
        $firma = '<tr><td class="sub_pie">BOTICA ALFONZO UGARTE</td></tr>';
        $firma .= '<tr><td class="sub_pie">botica.au@gmail.com</td></tr>';            
        
        $mailer->fnMail($arraddress,$arrAddcc,$asunto,$cuerpo,'pie',$firma,null);
        
        echo json_encode([
            'success' => true,
            'box_id' => $b[1],
            'totalBox' => $totalBox,
            'message' => 'Cierre de caja procesado correctamente'
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    
    exit;
}

// (Mantener tu código original para el caso no-AJAX si es necesario)
?>