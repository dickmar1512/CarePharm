<?php
	$conexion = new mysqli('localhost','milenio','armagedon','dbcarepharm',3306);

	if (mysqli_connect_errno()) 
	{
    	printf("La conexión con el servidor de base de datos falló: %s\n", mysqli_connect_error());
    	exit();
	}

	if(count($_POST) > 0)
	{
		$TIPO='07';
		$TIPO_NOTA = $_POST["tipo"];
		$MOTIVO = $_POST["motivo"];
		$SERIE = $_POST["serie"];
		$COMPROBANTE = $_POST["comp"];
		$NUM=$_POST["numDoc"];

		$RUC = '';
		$SELLID=0;

		if ($TIPO_NOTA=='01' || $TIPO_NOTA=='02' || $TIPO_NOTA=='03' || $TIPO_NOTA=='04' || $TIPO_NOTA=='05' || $TIPO_NOTA=='06' || $TIPO_NOTA=='07') {

			$product = Boleta2Data::getByNumDoc($NUM);
			$RUC = $product->RUC;
			$SELLID = $product->EXTRA1;

			$comp_cab = Cab_1_2Data::getById($product->id, 3);
			$detalles = Det_1_2Data::getByIdNota($product->id, 3);

			$comp_tri = Tri_1_2Data::getById($product->id, 3);
			$comp_ley = Ley_1_2Data::getById($product->id, 3);

			//ARCHIVO NOT
			$tipOperacion = $comp_cab->tipOperacion;
			$fecEmision = date("Y-m-d");
			$horEmision = date('H:i:s');
			$fecVencimiento = $comp_cab->fecVencimiento;
			$codLocalEmisor = $comp_cab->codLocalEmisor;
			$tipDocUsuario = $comp_cab->tipDocUsuario;
			$numDocUsuario = $comp_cab->numDocUsuario;
			$rznSocialUsuario = $comp_cab->rznSocialUsuario;
			$tipMoneda = $comp_cab->tipMoneda;
			$codTipoNota = $TIPO_NOTA;
			$descMotivo = $MOTIVO;
			$tipDocModifica = '03';
			$serieDocModifica = $NUM;
			$sumTotTributos = $comp_cab->sumTotTributos;
			//precio total
			$sumTotValVenta = $comp_cab->sumTotValVenta;
			$sumPrecioVenta = $comp_cab->sumPrecioVenta;

			$sumDescTotal = $comp_cab->sumDescTotal;
			$sumOtrosCargos = $comp_cab->sumOtrosCargos;
			$sumTotalAnticipos = $comp_cab->sumTotalAnticipos;
			$sumImpVenta = $comp_cab->sumImpVenta;
			$ublVersionId = $comp_cab->ublVersionId;
			$customizationId = $comp_cab->customizationId;

			//ARCHIVO DET
			$codUnidadMedida = $detalles[0]->codUnidadMedida;
			$codProducto = $detalles[0]->codProducto;
			$codProductoSUNAT = $detalles[0]->codProductoSUNAT;
			$codTriIGV = $detalles[0]->codTriIGV;
			$mtoIgvItem = $detalles[0]->mtoIgvItem;
			$mtoBaseIgvItem = $detalles[0]->mtoBaseIgvItem;			
			$nomTributoIgvItem = $detalles[0]->nomTributoIgvItem;
			$codTipTributoIgvItem = $detalles[0]->codTipTributoIgvItem;
			$tipAfeIGV = $detalles[0]->tipAfeIGV;
			$porIgvItem = $detalles[0]->porIgvItem;
			$codTriISC = "-";
			$mtoIscItem = $detalles[0]->mtoIscItem;
			$mtoBaseIscItem = 0;
			$nomTributoIscItem = $detalles[0]->nomTributoIscItem;
			$codTipTributoIscItem = $detalles[0]->codTipTributoIscItem;
			$tipSisISC = $detalles[0]->tipSisISC;
			$porIscItem = $detalles[0]->porIscItem;
			$codTriOtroItem = "-";

			$sumTotTributosItem = $detalles[0]->sumTotTributosItem;

			//datos por verificar
			$mtoTriOtroItem = '';
			$mtoBaseTriOtroItem = 0;
			$nomTributoIOtroItem = '';
			$codTipTributoIOtroItem = '';
			$porTriOtroItem = '';
			// fin datos por verificar

			$mtoValorReferencialUnitario = $detalles[0]->mtoValorReferencialUnitario;

			//ARCHVIO TRI
			$ideTributo = $comp_tri->ideTributo;
			$nomTributo = $comp_tri->nomTributo;
			$codTipTributo = $comp_tri->codTipTributo;
			$mtoBaseImponible = $comp_tri->mtoBaseImponible;
			$mtoTributo = $comp_tri->mtoTributo;

			//ARCHVIO LEY
			$codLeyenda = $comp_ley->codLeyenda;		
			$desLeyenda = $comp_ley->desLeyenda;	
		} else {		
			//ARCHIVO CAB
			$tipOperacion = $_POST["tipOperacion"];
			$fecEmision = $_POST["fecEmision"];
			$horEmision = $_POST["horEmision"];
			$fecVencimiento = $_POST["fecVencimiento"];
			$codLocalEmisor = $_POST["codLocalEmisor"];
			$tipDocUsuario = $_POST["tipDocUsuario"];
			$numDocUsuario = $_POST["numDocUsuario"];
			$rznSocialUsuario = $_POST["rznSocialUsuario"];
			$tipMoneda = $_POST["tipMoneda"];
			$codTipoNota = $_POST["codTipoNota"];
			$descMotivo = $_POST["descMotivo"];
			$tipDocModifica = $_POST["tipDocModifica"];
			$serieDocModifica = $_POST["serieDocModifica"];
			$sumTotTributos = $_POST["sumTotTributos"];
			//precio total
			$sumTotValVenta = 0;
			$sumPrecioVenta = 0;

			//ARCHIVO DET
			$codUnidadMedida = $_POST["codUnidadMedida"];
			$codProducto = $_POST["codProducto"];
			$codProductoSUNAT = $_POST["codProductoSUNAT"];
			$codTriIGV = $_POST["codTriIGV"];
			$mtoIgvItem = $_POST["mtoIgvItem"];
			//$mtoBaseIgvItem = $_POST["mtoBaseIgvItem"]; *
			if ($_POST['mtoIscItem'] == '')
			{
				$mtoBaseIgvItem = 0.00;
			}else
			{
				$mtoBaseIgvItem = $_POST["mtoIscItem"];
			}
		
			$nomTributoIgvItem = $_POST["nomTributoIgvItem"];
			$codTipTributoIgvItem = $_POST["codTipTributoIgvItem"];
			$tipAfeIGV = $_POST["tipAfeIGV"];
			$porIgvItem = $_POST["porIgvItem"];
			$codTriISC = "-";
			$mtoIscItem = $_POST["mtoIscItem"];
			$mtoBaseIscItem = 0;
			$nomTributoIscItem = $_POST["nomTributoIscItem"];
			$codTipTributoIscItem = $_POST["codTipTributoIscItem"];
			$tipSisISC = $_POST["tipSisISC"];
			$porIscItem = $_POST["porIscItem"];
			$codTriOtroItem = "-";

			$sumTotTributosItem = $_POST["sumTotTributosItem"];

			//datos por verificar
			$mtoTriOtroItem = '';
			$mtoBaseTriOtroItem = 0;
			$nomTributoIOtroItem = '';
			$codTipTributoIOtroItem = '';
			$porTriOtroItem = '';
			// fin datos por verificar

			$mtoValorReferencialUnitario = $_POST["mtoValorReferencialUnitario"];

			//ARCHVIO TRI
			$ideTributo = $_POST["ideTributo"];
			$nomTributo = $_POST["nomTributo"];
			$codTipTributo = $_POST["codTipTributo"];
			$mtoBaseImponible = 0;//$_POST["mtoBaseImponible"];
			$mtoTributo = $_POST["mtoTributo"];

			//$sell_id = $_POST["id"];

			//ARCHVIO LEY
			$codLeyenda = "1000";

			//ARCHIVO ACA
			$ctaBancoNacionDetraccion = "-";
			$codBienDetraccion = "-";
			$porDetraccion = "-";
			$mtoDetraccion = "-";
			$codPaisCliente = 'PE';
			$codUbigeoCliente = $_POST['codUbigeoCliente'];
			$desDireccionCliente = $_POST['desDireccionCliente'];
			$codPaisEntrega = "-";
			$codUbigeoEntrega = "-";
			$desDireccionEntrega = "-";
		}

		//########## CONTENIDO ARCHIVO DET === 32 ITEMS #############################
		$downloadfile2 = "../efact1.3.4/sunat_archivos/sfs/DATA/".$RUC."-".$TIPO."-".$SERIE."-".$COMPROBANTE.".det";

		$filecontent2 = "";

			$sql_DOC = "insert into boleta (
				RUC,
				TIPO,
				SERIE,
				COMPROBANTE
				) values (
				\"$RUC\",
				\"$TIPO\",
				\"$SERIE\",
				\"$COMPROBANTE\"
			)";

			#CAPTAR TIPO DE DOCUMENTO: FACTURA y SU ID: OBTENIENDO SERIE Y NRO
			$conexion->query($sql_DOC);

			$id_factura_impresa = $conexion->insert_id;
			$TIPO_DOC = $TIPO;
			$ID_TIPO_DOC = $id_factura_impresa;

			$data=array();

			$operations = OperationData::getAllProductsBySellId($SELLID);


		if($TIPO_NOTA=='01' || $TIPO_NOTA=='02' || $TIPO_NOTA=='06'){
			foreach ($operations as $item)
			{
				$product  = $item->getProduct();
				$cantidad = $item->q;
				if($product->is_stock==0) $cantidad=1;
				
				$precio_unitario = $item->prec_alt;

				$descripcion_producto = $product->name;

				$producto = new ProductData();

				$producto->name= $descripcion_producto;
				$producto->stock = $product->stock;
				$producto->id = $product->id;
				
				if($product->is_stock==1){
					$producto->stock = $producto->stock + $cantidad;
					$producto->update_stock();
				}
				
				$mtoValorVentaItem = $cantidad * $precio_unitario;//$_POST["mtoValorVentaItem"];
				$mtoPrecioVentaUnitario = $precio_unitario;
				$mtoBaseIgvItem = $mtoValorVentaItem;

				$filecontent2 .= $codUnidadMedida.'|'.
								$cantidad.'|'.
								$codProducto."|".
								$codProductoSUNAT."|".
								$descripcion_producto.'|'.
								$precio_unitario.'|'.
								$sumTotTributosItem.'|'.
								$codTriIGV.'|'.
								$mtoIgvItem.'|'.
								$mtoBaseIgvItem.'|'.
								$nomTributoIgvItem.'|'.
								$codTipTributoIgvItem.'|'.
								$tipAfeIGV.'|'.
								$porIgvItem.'|'.
								$codTriISC.'|'.
								$mtoIscItem."|".
								$mtoBaseIscItem."|".
								$nomTributoIscItem."|".
								$codTipTributoIscItem."|".
								$tipSisISC."|".
								$porIscItem."|".
								$codTriOtroItem."|".
								$mtoTriOtroItem."|".
								$mtoBaseTriOtroItem."|".
								$nomTributoIOtroItem."|".
								$codTipTributoIOtroItem."|".
								$porTriOtroItem."|-||||||".
								$mtoPrecioVentaUnitario."|".
								$mtoValorVentaItem."|".
								$mtoValorReferencialUnitario."|".PHP_EOL;

				#BASE DE DATOS ARCHIVO .DET
								
				$sql_DET = "insert into det (
				  TIPO_DOC,
				  ID_TIPO_DOC,
				  codUnidadMedida,
				  ctdUnidadItem,
				  codProducto,
				  codProductoSUNAT,
				  desItem,
				  mtoValorUnitario,
				  sumTotTributosItem,
				  codTriIGV,
				  mtoIgvItem,
				  mtoBaseIgvItem,
				  nomTributoIgvItem,
				  codTipTributoIgvItem,
				  tipAfeIGV,
				  porIgvItem,
				  codTriISC,
				  mtoIscItem,
				  mtoBaseIscItem,
				  nomTributoIscItem,
				  codTipTributoIscItem,
				  tipSisISC,
				  porIscItem,
				  codTriOtroItem,
				  mtoTriOtroItem,
				  mtoBaseTriOtroItem,
				  nomTributoIOtroItem,
				  codTipTributoIOtroItem,
				  porTriOtroItem,
				  mtoPrecioVentaUnitario,
				  mtoValorVentaItem,
				  mtoValorReferencialUnitario
				) values (
				  \"$TIPO_DOC\",
				  \"$ID_TIPO_DOC\",
				  '".$codUnidadMedida."',
				  '".$cantidad."',
				  \"$codProducto\",
				  \"$codProductoSUNAT\",
				  '".$descripcion_producto."',
				  '".$precio_unitario."',
				  \"$sumTotTributosItem\",
				  \"$codTriIGV\",
				  \"$mtoIgvItem\",
				  \"$mtoBaseIgvItem\",
				  \"$nomTributoIgvItem\",
				  \"$codTipTributoIgvItem\",
				  \"$tipAfeIGV\",
				  \"$porIgvItem\",
				  \"$codTriISC\",
				  \"$mtoIscItem\",
				  \"$mtoBaseIscItem\",
				  \"$nomTributoIscItem\",
				  \"$codTipTributoIscItem\",
				  \"$tipSisISC\",
				  \"$porIscItem\",
				  \"$codTriOtroItem\",
				  \"$mtoTriOtroItem\",
				  \"$mtoBaseTriOtroItem\",
				  \"$nomTributoIOtroItem\",
				  \"$codTipTributoIOtroItem\",
				  \"$porTriOtroItem\",
				  \"$mtoPrecioVentaUnitario\",
				  \"$mtoValorVentaItem\",
				  \"$mtoValorReferencialUnitario\"
				)";

				$conexion->query($sql_DET);
        	}

		}

		else if($TIPO_NOTA=='03'){
				$data = $_POST['arraydet'];

				foreach ($operations as $item)
			{
				$product  = $item->getProduct();

				for ($i=0; $i < count($data); $i++) {

				if($data[$i][0]==$product->name){ 
				$cantidad = $item->q;
				$precio_unitario = $item->prec_alt;
				$descripcion_producto = $data[$i][1];

				$producto = new ProductData();

				$producto->name= $descripcion_producto;
				$producto->stock = $cantidad;
				$producto->restar_stock_name();

				$producto2 = new ProductData();

				$producto2->name= $data[$i][0];
				$producto2->stock = $cantidad;
				$producto2->sumar_stock_name();
				

				$mtoValorVentaItem = $cantidad * $precio_unitario;//$_POST["mtoValorVentaItem"];
				$mtoPrecioVentaUnitario = $precio_unitario;
				$mtoBaseIgvItem = $mtoValorVentaItem;

				$filecontent2 .= $codUnidadMedida.'|'.
								$cantidad.'|'.
								$codProducto."|".
								$codProductoSUNAT."|".
								$descripcion_producto.'|'.
								$precio_unitario.'|'.
								$sumTotTributosItem.'|'.
								$codTriIGV.'|'.
								$mtoIgvItem.'|'.
								$mtoBaseIgvItem.'|'.
								$nomTributoIgvItem.'|'.
								$codTipTributoIgvItem.'|'.
								$tipAfeIGV.'|'.
								$porIgvItem.'|'.
								$codTriISC.'|'.
								$mtoIscItem."|".
								$mtoBaseIscItem."|".
								$nomTributoIscItem."|".
								$codTipTributoIscItem."|".
								$tipSisISC."|".
								$porIscItem."|".
								$codTriOtroItem."|".
								$mtoTriOtroItem."|".
								$mtoBaseTriOtroItem."|".
								$nomTributoIOtroItem."|".
								$codTipTributoIOtroItem."|".
								$porTriOtroItem."|-||||||".
								$mtoPrecioVentaUnitario."|".
								$mtoValorVentaItem."|".
								$mtoValorReferencialUnitario."|".PHP_EOL;

				#BASE DE DATOS ARCHIVO .DET
								
				$sql_DET = "insert into det (
				  TIPO_DOC,
				  ID_TIPO_DOC,
				  codUnidadMedida,
				  ctdUnidadItem,
				  codProducto,
				  codProductoSUNAT,
				  desItem,
				  mtoValorUnitario,
				  sumTotTributosItem,
				  codTriIGV,
				  mtoIgvItem,
				  mtoBaseIgvItem,
				  nomTributoIgvItem,
				  codTipTributoIgvItem,
				  tipAfeIGV,
				  porIgvItem,
				  codTriISC,
				  mtoIscItem,
				  mtoBaseIscItem,
				  nomTributoIscItem,
				  codTipTributoIscItem,
				  tipSisISC,
				  porIscItem,
				  codTriOtroItem,
				  mtoTriOtroItem,
				  mtoBaseTriOtroItem,
				  nomTributoIOtroItem,
				  codTipTributoIOtroItem,
				  porTriOtroItem,
				  mtoPrecioVentaUnitario,
				  mtoValorVentaItem,
				  mtoValorReferencialUnitario
				) values (
				  \"$TIPO_DOC\",
				  \"$ID_TIPO_DOC\",
				  '".$codUnidadMedida."',
				  '".$cantidad."',
				  \"$codProducto\",
				  \"$codProductoSUNAT\",
				  '".$descripcion_producto."',
				  '".$precio_unitario."',
				  \"$sumTotTributosItem\",
				  \"$codTriIGV\",
				  \"$mtoIgvItem\",
				  \"$mtoBaseIgvItem\",
				  \"$nomTributoIgvItem\",
				  \"$codTipTributoIgvItem\",
				  \"$tipAfeIGV\",
				  \"$porIgvItem\",
				  \"$codTriISC\",
				  \"$mtoIscItem\",
				  \"$mtoBaseIscItem\",
				  \"$nomTributoIscItem\",
				  \"$codTipTributoIscItem\",
				  \"$tipSisISC\",
				  \"$porIscItem\",
				  \"$codTriOtroItem\",
				  \"$mtoTriOtroItem\",
				  \"$mtoBaseTriOtroItem\",
				  \"$nomTributoIOtroItem\",
				  \"$codTipTributoIOtroItem\",
				  \"$porTriOtroItem\",
				  \"$mtoPrecioVentaUnitario\",
				  \"$mtoValorVentaItem\",
				  \"$mtoValorReferencialUnitario\"
				)";

				$conexion->query($sql_DET);

				$sumTotValVenta = $cantidad*$precio_unitario + $sumTotValVenta;
				$sumPrecioVenta = $sumTotValVenta+$sumTotTributos;//$_POST["sumPrecioVenta"]; UNID * PREC. UNIT
			}
		}
			}
			}

			if($TIPO_NOTA=='04'){

				$item=$operations;

				$product  = $item[0]->getProduct();

				$cantidad = 1;
				$precio_unitario = $_POST['dscto'];

				$descripcion_producto = "DESCUENTO GLOBAL | ".$MOTIVO;
				
				$mtoValorVentaItem = $_POST['dscto'];
				//$_POST["mtoValorVentaItem"];
				$mtoPrecioVentaUnitario = $precio_unitario;
				$mtoBaseIgvItem = $mtoValorVentaItem;

				$filecontent2 .= $codUnidadMedida.'|'.
								$cantidad.'|'.
								$codProducto."|".
								$codProductoSUNAT."|".
								$descripcion_producto.'|'.
								$precio_unitario.'|'.
								$sumTotTributosItem.'|'.
								$codTriIGV.'|'.
								$mtoIgvItem.'|'.
								$mtoBaseIgvItem.'|'.
								$nomTributoIgvItem.'|'.
								$codTipTributoIgvItem.'|'.
								$tipAfeIGV.'|'.
								$porIgvItem.'|'.
								$codTriISC.'|'.
								$mtoIscItem."|".
								$mtoBaseIscItem."|".
								$nomTributoIscItem."|".
								$codTipTributoIscItem."|".
								$tipSisISC."|".
								$porIscItem."|".
								$codTriOtroItem."|".
								$mtoTriOtroItem."|".
								$mtoBaseTriOtroItem."|".
								$nomTributoIOtroItem."|".
								$codTipTributoIOtroItem."|".
								$porTriOtroItem."|-||||||".
								$mtoPrecioVentaUnitario."|".
								$mtoValorVentaItem."|".
								$mtoValorReferencialUnitario."|".PHP_EOL;

				#BASE DE DATOS ARCHIVO .DET
								
				$sql_DET = "insert into det (
				  TIPO_DOC,
				  ID_TIPO_DOC,
				  codUnidadMedida,
				  ctdUnidadItem,
				  codProducto,
				  codProductoSUNAT,
				  desItem,
				  mtoValorUnitario,
				  sumTotTributosItem,
				  codTriIGV,
				  mtoIgvItem,
				  mtoBaseIgvItem,
				  nomTributoIgvItem,
				  codTipTributoIgvItem,
				  tipAfeIGV,
				  porIgvItem,
				  codTriISC,
				  mtoIscItem,
				  mtoBaseIscItem,
				  nomTributoIscItem,
				  codTipTributoIscItem,
				  tipSisISC,
				  porIscItem,
				  codTriOtroItem,
				  mtoTriOtroItem,
				  mtoBaseTriOtroItem,
				  nomTributoIOtroItem,
				  codTipTributoIOtroItem,
				  porTriOtroItem,
				  mtoPrecioVentaUnitario,
				  mtoValorVentaItem,
				  mtoValorReferencialUnitario
				) values (
				  \"$TIPO_DOC\",
				  \"$ID_TIPO_DOC\",
				  '".$codUnidadMedida."',
				  '".$cantidad."',
				  \"$codProducto\",
				  \"$codProductoSUNAT\",
				  '".$descripcion_producto."',
				  '".$precio_unitario."',
				  \"$sumTotTributosItem\",
				  \"$codTriIGV\",
				  \"$mtoIgvItem\",
				  \"$mtoBaseIgvItem\",
				  \"$nomTributoIgvItem\",
				  \"$codTipTributoIgvItem\",
				  \"$tipAfeIGV\",
				  \"$porIgvItem\",
				  \"$codTriISC\",
				  \"$mtoIscItem\",
				  \"$mtoBaseIscItem\",
				  \"$nomTributoIscItem\",
				  \"$codTipTributoIscItem\",
				  \"$tipSisISC\",
				  \"$porIscItem\",
				  \"$codTriOtroItem\",
				  \"$mtoTriOtroItem\",
				  \"$mtoBaseTriOtroItem\",
				  \"$nomTributoIOtroItem\",
				  \"$codTipTributoIOtroItem\",
				  \"$porTriOtroItem\",
				  \"$mtoPrecioVentaUnitario\",
				  \"$mtoValorVentaItem\",
				  \"$mtoValorReferencialUnitario\"
				)";

				$conexion->query($sql_DET);

				$sumTotValVenta = $cantidad*$precio_unitario + $sumTotValVenta;
				$sumPrecioVenta = $sumTotValVenta+$sumTotTributos;//$_POST["sumPrecioVenta"]; UNID * PREC. UNIT
			

		}

		else if($TIPO_NOTA=='05'){
				$data = $_POST['arraydet'];

				foreach ($operations as $item)
			{
				$product  = $item->getProduct();

				for ($i=0; $i < count($data); $i++) {

				if($data[$i][0]==$product->name){ 

				$cantidad = $item->q;
				$precio_unitario = $data[$i][1];
				$descripcion_producto = $data[$i][0];
				$mtoValorVentaItem =  $data[$i][1];//$_POST["mtoValorVentaItem"];
				$mtoPrecioVentaUnitario = $precio_unitario;
				$mtoBaseIgvItem = $mtoValorVentaItem;

				$filecontent2 .= $codUnidadMedida.'|'.
								$cantidad.'|'.
								$codProducto."|".
								$codProductoSUNAT."|".
								$descripcion_producto.'|'.
								$precio_unitario.'|'.
								$sumTotTributosItem.'|'.
								$codTriIGV.'|'.
								$mtoIgvItem.'|'.
								$mtoBaseIgvItem.'|'.
								$nomTributoIgvItem.'|'.
								$codTipTributoIgvItem.'|'.
								$tipAfeIGV.'|'.
								$porIgvItem.'|'.
								$codTriISC.'|'.
								$mtoIscItem."|".
								$mtoBaseIscItem."|".
								$nomTributoIscItem."|".
								$codTipTributoIscItem."|".
								$tipSisISC."|".
								$porIscItem."|".
								$codTriOtroItem."|".
								$mtoTriOtroItem."|".
								$mtoBaseTriOtroItem."|".
								$nomTributoIOtroItem."|".
								$codTipTributoIOtroItem."|".
								$porTriOtroItem."|-||||||".
								$mtoPrecioVentaUnitario."|".
								$mtoValorVentaItem."|".
								$mtoValorReferencialUnitario."|".PHP_EOL;

				#BASE DE DATOS ARCHIVO .DET
								
				$sql_DET = "insert into det (
				  TIPO_DOC,
				  ID_TIPO_DOC,
				  codUnidadMedida,
				  ctdUnidadItem,
				  codProducto,
				  codProductoSUNAT,
				  desItem,
				  mtoValorUnitario,
				  sumTotTributosItem,
				  codTriIGV,
				  mtoIgvItem,
				  mtoBaseIgvItem,
				  nomTributoIgvItem,
				  codTipTributoIgvItem,
				  tipAfeIGV,
				  porIgvItem,
				  codTriISC,
				  mtoIscItem,
				  mtoBaseIscItem,
				  nomTributoIscItem,
				  codTipTributoIscItem,
				  tipSisISC,
				  porIscItem,
				  codTriOtroItem,
				  mtoTriOtroItem,
				  mtoBaseTriOtroItem,
				  nomTributoIOtroItem,
				  codTipTributoIOtroItem,
				  porTriOtroItem,
				  mtoPrecioVentaUnitario,
				  mtoValorVentaItem,
				  mtoValorReferencialUnitario
				) values (
				  \"$TIPO_DOC\",
				  \"$ID_TIPO_DOC\",
				  '".$codUnidadMedida."',
				  '".$cantidad."',
				  \"$codProducto\",
				  \"$codProductoSUNAT\",
				  '".$descripcion_producto."',
				  '".$precio_unitario."',
				  \"$sumTotTributosItem\",
				  \"$codTriIGV\",
				  \"$mtoIgvItem\",
				  \"$mtoBaseIgvItem\",
				  \"$nomTributoIgvItem\",
				  \"$codTipTributoIgvItem\",
				  \"$tipAfeIGV\",
				  \"$porIgvItem\",
				  \"$codTriISC\",
				  \"$mtoIscItem\",
				  \"$mtoBaseIscItem\",
				  \"$nomTributoIscItem\",
				  \"$codTipTributoIscItem\",
				  \"$tipSisISC\",
				  \"$porIscItem\",
				  \"$codTriOtroItem\",
				  \"$mtoTriOtroItem\",
				  \"$mtoBaseTriOtroItem\",
				  \"$nomTributoIOtroItem\",
				  \"$codTipTributoIOtroItem\",
				  \"$porTriOtroItem\",
				  \"$mtoPrecioVentaUnitario\",
				  \"$mtoValorVentaItem\",
				  \"$mtoValorReferencialUnitario\"
				)";

				$conexion->query($sql_DET);

				$sumTotValVenta = $cantidad*$precio_unitario + $sumTotValVenta;
				$sumPrecioVenta = $sumTotValVenta+$sumTotTributos;//$_POST["sumPrecioVenta"]; UNID * PREC. UNIT
			}
		}
			}
			}


	else if($TIPO_NOTA=='07'){
		$data = $_POST['arraydet'];

		foreach ($operations as $item)
			{
				$product  = $item->getProduct();

				for ($i=0; $i < count($data); $i++) {

				if($data[$i][0]==$product->name){ 
				$cantidad = $data[$i][1];
				$precio_unitario = $item->prec_alt;
				$descripcion_producto = $data[$i][0];

				$producto = new ProductData();

				$producto->name= $descripcion_producto;
				$producto->stock = $cantidad;
				$producto->sumar_stock_name();

				$mtoValorVentaItem = $cantidad * $precio_unitario;

				//$_POST["mtoValorVentaItem"];
				$mtoPrecioVentaUnitario = $precio_unitario;
				$mtoBaseIgvItem = $mtoValorVentaItem;

				$filecontent2 .= $codUnidadMedida.'|'.
								$cantidad.'|'.
								$codProducto."|".
								$codProductoSUNAT."|".
								$descripcion_producto.'|'.
								$precio_unitario.'|'.
								$sumTotTributosItem.'|'.
								$codTriIGV.'|'.
								$mtoIgvItem.'|'.
								$mtoBaseIgvItem.'|'.
								$nomTributoIgvItem.'|'.
								$codTipTributoIgvItem.'|'.
								$tipAfeIGV.'|'.
								$porIgvItem.'|'.
								$codTriISC.'|'.
								$mtoIscItem."|".
								$mtoBaseIscItem."|".
								$nomTributoIscItem."|".
								$codTipTributoIscItem."|".
								$tipSisISC."|".
								$porIscItem."|".
								$codTriOtroItem."|".
								$mtoTriOtroItem."|".
								$mtoBaseTriOtroItem."|".
								$nomTributoIOtroItem."|".
								$codTipTributoIOtroItem."|".
								$porTriOtroItem."|-||||||".
								$mtoPrecioVentaUnitario."|".
								$mtoValorVentaItem."|".
								$mtoValorReferencialUnitario."|".PHP_EOL;

				#BASE DE DATOS ARCHIVO .DET
								
				$sql_DET = "insert into det (
				  TIPO_DOC,
				  ID_TIPO_DOC,
				  codUnidadMedida,
				  ctdUnidadItem,
				  codProducto,
				  codProductoSUNAT,
				  desItem,
				  mtoValorUnitario,
				  sumTotTributosItem,
				  codTriIGV,
				  mtoIgvItem,
				  mtoBaseIgvItem,
				  nomTributoIgvItem,
				  codTipTributoIgvItem,
				  tipAfeIGV,
				  porIgvItem,
				  codTriISC,
				  mtoIscItem,
				  mtoBaseIscItem,
				  nomTributoIscItem,
				  codTipTributoIscItem,
				  tipSisISC,
				  porIscItem,
				  codTriOtroItem,
				  mtoTriOtroItem,
				  mtoBaseTriOtroItem,
				  nomTributoIOtroItem,
				  codTipTributoIOtroItem,
				  porTriOtroItem,
				  mtoPrecioVentaUnitario,
				  mtoValorVentaItem,
				  mtoValorReferencialUnitario
				) values (
				  \"$TIPO_DOC\",
				  \"$ID_TIPO_DOC\",
				  '".$codUnidadMedida."',
				  '".$cantidad."',
				  \"$codProducto\",
				  \"$codProductoSUNAT\",
				  '".$descripcion_producto."',
				  '".$precio_unitario."',
				  \"$sumTotTributosItem\",
				  \"$codTriIGV\",
				  \"$mtoIgvItem\",
				  \"$mtoBaseIgvItem\",
				  \"$nomTributoIgvItem\",
				  \"$codTipTributoIgvItem\",
				  \"$tipAfeIGV\",
				  \"$porIgvItem\",
				  \"$codTriISC\",
				  \"$mtoIscItem\",
				  \"$mtoBaseIscItem\",
				  \"$nomTributoIscItem\",
				  \"$codTipTributoIscItem\",
				  \"$tipSisISC\",
				  \"$porIscItem\",
				  \"$codTriOtroItem\",
				  \"$mtoTriOtroItem\",
				  \"$mtoBaseTriOtroItem\",
				  \"$nomTributoIOtroItem\",
				  \"$codTipTributoIOtroItem\",
				  \"$porTriOtroItem\",
				  \"$mtoPrecioVentaUnitario\",
				  \"$mtoValorVentaItem\",
				  \"$mtoValorReferencialUnitario\"
				)";

				$conexion->query($sql_DET);

				$sumTotValVenta = $cantidad*$precio_unitario + $sumTotValVenta;
				$sumPrecioVenta = $sumTotValVenta+$sumTotTributos;//$_POST["sumPrecioVenta"]; UNID * PREC. UNIT
				}
			}
		}
	}
       		$numLetra = NumeroLetras::convertir(number_format($sumPrecioVenta,2,'.',','));
			$desLeyenda = $numLetra;

			#BASE DE DATOS ARCHIVO .CAB
			$sql_CAB = "insert into nota (
				  TIPO_DOC,
				  ID_TIPO_DOC,
				  tipOperacion,
				  fecEmision,
				  horEmision,
				  codLocalEmisor,
				  tipDocUsuario,
				  numDocUsuario,
				  rznSocialUsuario,
				  tipMoneda,
				  codTipoNota,
				  descMotivo,
				  tipDocModifica,
				  serieDocModifica,
				  sumTotTributos,
				  sumTotValVenta,
				  sumPrecioVenta,
				  sumDescTotal,
				  sumOtrosCargos,
				  sumTotalAnticipos,
				  sumImpVenta,
				  ublVersionId,
				  customizationId
				) values (
				  \"$TIPO_DOC\",
				  \"$ID_TIPO_DOC\",
				  \"$tipOperacion\",
				  DATE(NOW()),
				  TIME(NOW()),
				  \"$codLocalEmisor\",
				  \"$tipDocUsuario\",
				  \"$numDocUsuario\",
				  \"$rznSocialUsuario\",
				  \"$tipMoneda\",
				  \"$codTipoNota\",
				  \"$descMotivo\",
				  \"$tipDocModifica\",
				  \"$serieDocModifica\",
				  \"$sumTotTributos\",
				  \"$sumTotValVenta\",
				  \"$sumPrecioVenta\",
				  \"$sumDescTotal\",
				  \"$sumOtrosCargos\",
				  \"$sumTotalAnticipos\",
				  \"$sumImpVenta\",
				  \"$ublVersionId\",
				  \"$customizationId\"
				)";

			$conexion->query($sql_CAB);

			$ar = fopen($downloadfile2, "a") or die("Error al crear");
			fwrite($ar, $filecontent2);

			//FIN SI EXISTE UN METODO POST "SE ESTAN RECIBIENDO DATOS"

			//########## CONTENIDO ARCHIVO CAB === 21 ITEMS #############################
			$downloadfile="../efact1.3.4/sunat_archivos/sfs/DATA/".$RUC."-".$TIPO."-".$SERIE."-".$COMPROBANTE.".not";

			$filecontent=
				$tipOperacion."|".
				$fecEmision."|".
				$horEmision."|".
				$codLocalEmisor."|".
				$tipDocUsuario."|".
				$numDocUsuario."|".
				$rznSocialUsuario."|".
				$tipMoneda."|".
				$codTipoNota."|".
				$descMotivo."|".
				$tipDocModifica."|".
				$serieDocModifica."|".
				$sumTotTributos."|".
				$sumTotValVenta."|".
				$sumPrecioVenta."|".
				$sumDescTotal."|".
				$sumOtrosCargos."|".
				$sumTotalAnticipos."|".
				$sumImpVenta."|".
				$ublVersionId."|".
				$customizationId."|";

			$ar = fopen($downloadfile, "a") or die("Error al crear");
			fwrite($ar, $filecontent);	

			//########## CONTENIDO ARCHIVO TRI === 6 ITEMS #############################
			$downloadfile3 = "../efact1.3.4/sunat_archivos/sfs/DATA/".$RUC."-".$TIPO."-".$SERIE."-".$COMPROBANTE.".tri";

			$filecontent3=
				$ideTributo."|".
				$nomTributo."|".
				$codTipTributo."|".
				$mtoBaseImponible."|".
				$mtoTributo."|";

				$ar = fopen($downloadfile3, "a") or die("Error al crear");
				fwrite($ar, $filecontent3);


				#BASE DE DATOS ARCHIVO .TRI
			$sql_TRI = "insert into tri (
				TIPO_DOC,
			  	ID_TIPO_DOC,
				ideTributo,
				nomTributo,
				codTipTributo,
				mtoBaseImponible,
				mtoTributo
				) values (
				\"$TIPO_DOC\",
				\"$ID_TIPO_DOC\",
				\"$ideTributo\",
				\"$nomTributo\",
				\"$codTipTributo\",
				\"$mtoBaseImponible\",
				\"$mtoTributo\"
				)";

			$conexion->query($sql_TRI);			

			//########## CONTENIDO ARCHIVO LEY === 2 ITEMS #############################0

			$downloadfile4 = "../efact1.3.4/sunat_archivos/sfs/DATA/".$RUC."-".$TIPO."-".$SERIE."-".$COMPROBANTE.".ley";

			$filecontent4=
				$codLeyenda."|".
				$desLeyenda."|";

			$ar=fopen($downloadfile4, "a") or die("Error al crear");
			fwrite($ar, $filecontent4);

			#BASE DE DATOS ARCHIVO .LEY
				$sql_LEY = "insert into ley (
				  TIPO_DOC,
				  ID_TIPO_DOC,
				  codLeyenda,
				  desLeyenda
				) values (
				  \"$TIPO_DOC\",
				  \"$ID_TIPO_DOC\",
		  		  \"$codLeyenda\",
		  		  \"$desLeyenda\"
				)";

			$conexion->query($sql_LEY);			

			echo "true";
			return true;

			//print "<script>window.location='./?view=onesell&id=$s[1]';</script>";		
	}	
 ?>