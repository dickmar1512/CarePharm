<?php
$conexion = new mysqli('localhost', 'milenio', 'armagedon', 'dbcarepharm', 3306);

if (mysqli_connect_errno()) {
    printf("La conexión con el servidor de base de datos falló: %s\n", mysqli_connect_error());
    exit();
}

if (count($_POST) > 0) {

    $TIPO        = '07';
    $TIPO_NOTA   = $_POST["tipo"];
    $MOTIVO      = $_POST["motivo"];
    $SERIE       = $_POST["serie"];
    $COMPROBANTE = $_POST["comp"];
    $NUM         = $_POST["numDoc"];
    $RUC         = '';
    $SELLID      = 0;

    // ─────────────────────────────────────────────────────────────
    // OBTENER DATOS DEL COMPROBANTE ORIGINAL
    // ─────────────────────────────────────────────────────────────
    if ($TIPO_NOTA == '01' || $TIPO_NOTA == '02' || $TIPO_NOTA == '03' ||
        $TIPO_NOTA == '04' || $TIPO_NOTA == '05' || $TIPO_NOTA == '06' ||
        $TIPO_NOTA == '07')
    {
        $product = Factura2Data::getByNumDoc($NUM);
        $RUC     = $product->RUC;
        $SELLID  = $product->EXTRA1;

        $comp_cab = Cab_1_2Data::getById($product->id, 1);
        $detalles = Det_1_2Data::getByIdNota($product->id, 1);
        $comp_tri = Tri_1_2Data::getById($product->id, 1);
        $comp_ley = Ley_1_2Data::getById($product->id, 1);

        // ── CABECERA ──
        $tipOperacion      = $comp_cab->tipOperacion;
        $fecEmision        = date("Y-m-d");
        $horEmision        = date('H:i:s');
        $fecVencimiento    = $comp_cab->fecVencimiento;
        $codLocalEmisor    = $comp_cab->codLocalEmisor;
        $tipDocUsuario     = $comp_cab->tipDocUsuario;
        $numDocUsuario     = $comp_cab->numDocUsuario;
        $rznSocialUsuario  = $comp_cab->rznSocialUsuario;
        $tipMoneda         = $comp_cab->tipMoneda;
        $codTipoNota       = $TIPO_NOTA;
        $descMotivo        = $MOTIVO;
        $tipDocModifica    = '01';
        $serieDocModifica  = $NUM;
        $sumTotTributos    = $comp_cab->sumTotTributos;
        $sumTotValVenta    = $comp_cab->sumTotValVenta;
        $sumPrecioVenta    = $comp_cab->sumPrecioVenta;
        $sumDescTotal      = $comp_cab->sumDescTotal;
        $sumOtrosCargos    = $comp_cab->sumOtrosCargos;
        $sumTotalAnticipos = $comp_cab->sumTotalAnticipos;
        $sumImpVenta       = $comp_cab->sumImpVenta;
        $ublVersionId      = $comp_cab->ublVersionId;
        $customizationId   = $comp_cab->customizationId;

        // ── DETALLE (primer ítem como referencia de tributos) ──
        $codUnidadMedida           = $detalles[0]->codUnidadMedida;
        $codProducto               = $detalles[0]->codProducto;
        $codProductoSUNAT          = $detalles[0]->codProductoSUNAT;
        $codTriIGV                 = $detalles[0]->codTriIGV;
        $mtoIgvItem                = $detalles[0]->mtoIgvItem;
        $mtoBaseIgvItem            = $detalles[0]->mtoBaseIgvItem;
        $nomTributoIgvItem         = $detalles[0]->nomTributoIgvItem;
        $codTipTributoIgvItem      = $detalles[0]->codTipTributoIgvItem;
        $tipAfeIGV                 = $detalles[0]->tipAfeIGV;
        $porIgvItem                = $detalles[0]->porIgvItem;
        $codTriISC                 = "-";
        $mtoIscItem                = $detalles[0]->mtoIscItem;
        $mtoBaseIscItem            = 0;
        $nomTributoIscItem         = $detalles[0]->nomTributoIscItem;
        $codTipTributoIscItem      = $detalles[0]->codTipTributoIscItem;
        $tipSisISC                 = $detalles[0]->tipSisISC;
        $porIscItem                = $detalles[0]->porIscItem;
        $codTriOtroItem            = "-";
        $sumTotTributosItem        = $detalles[0]->sumTotTributosItem;
        $mtoTriOtroItem            = 0;
        $mtoBaseTriOtroItem        = 0;
        $nomTributoIOtroItem       = '';
        $codTipTributoIOtroItem    = '';
        $porTriOtroItem            = '';
        $mtoValorReferencialUnitario = $detalles[0]->mtoValorReferencialUnitario;

        // ── TRIBUTOS ──
        $ideTributo       = $comp_tri->ideTributo;
        $nomTributo       = $comp_tri->nomTributo;
        $codTipTributo    = $comp_tri->codTipTributo;
        $mtoBaseImponible = $comp_tri->mtoBaseImponible;
        $mtoTributo       = $comp_tri->mtoTributo;

        // ── LEYENDA ──
        $codLeyenda = $comp_ley->codLeyenda;
        $desLeyenda = $comp_ley->desLeyenda;

    } else {
        // Ruta manual (POST completo)
        $tipOperacion      = $_POST["tipOperacion"];
        $fecEmision        = $_POST["fecEmision"];
        $horEmision        = $_POST["horEmision"];
        $fecVencimiento    = $_POST["fecVencimiento"];
        $codLocalEmisor    = $_POST["codLocalEmisor"];
        $tipDocUsuario     = $_POST["tipDocUsuario"];
        $numDocUsuario     = $_POST["numDocUsuario"];
        $rznSocialUsuario  = $_POST["rznSocialUsuario"];
        $tipMoneda         = $_POST["tipMoneda"];
        $codTipoNota       = $_POST["codTipoNota"];
        $descMotivo        = $_POST["descMotivo"];
        $tipDocModifica    = $_POST["tipDocModifica"];
        $serieDocModifica  = $_POST["serieDocModifica"];
        $sumTotTributos    = $_POST["sumTotTributos"];
        $sumTotValVenta    = 0;
        $sumPrecioVenta    = 0;

        $codUnidadMedida        = $_POST["codUnidadMedida"];
        $codProducto            = $_POST["codProducto"];
        $codProductoSUNAT       = $_POST["codProductoSUNAT"];
        $codTriIGV              = $_POST["codTriIGV"];
        $mtoIgvItem             = $_POST["mtoIgvItem"];
        $mtoBaseIgvItem         = ($_POST['mtoIscItem'] == '') ? 0.00 : $_POST["mtoIscItem"];
        $nomTributoIgvItem      = $_POST["nomTributoIgvItem"];
        $codTipTributoIgvItem   = $_POST["codTipTributoIgvItem"];
        $tipAfeIGV              = $_POST["tipAfeIGV"];
        $porIgvItem             = $_POST["porIgvItem"];
        $codTriISC              = "-";
        $mtoIscItem             = $_POST["mtoIscItem"];
        $mtoBaseIscItem         = 0;
        $nomTributoIscItem      = $_POST["nomTributoIscItem"];
        $codTipTributoIscItem   = $_POST["codTipTributoIscItem"];
        $tipSisISC              = $_POST["tipSisISC"];
        $porIscItem             = $_POST["porIscItem"];
        $codTriOtroItem         = "-";
        $sumTotTributosItem     = $_POST["sumTotTributosItem"];
        $mtoTriOtroItem         = '';
        $mtoBaseTriOtroItem     = '';
        $nomTributoIOtroItem    = '';
        $codTipTributoIOtroItem = '';
        $porTriOtroItem         = '';
        $mtoValorReferencialUnitario = $_POST["mtoValorReferencialUnitario"];

        $ideTributo       = $_POST["ideTributo"];
        $nomTributo       = $_POST["nomTributo"];
        $codTipTributo    = $_POST["codTipTributo"];
        $mtoBaseImponible = 0;
        $mtoTributo       = $_POST["mtoTributo"];

        $codLeyenda = "2001";
        if ($codLeyenda == "2001") {
            $desLeyenda = "BIENES TRANSFERIDOS EN LA AMAZONIA REGION SELVA PARA SER CONSUMIDOS EN LA MISMA";
        }
        if ($codLeyenda == "2002") {
            $desLeyenda = "SERVICIOS PRESTADOS EN LA AMAZONIA REGION SELVA PARA SER CONSUMIDOS EN LA MISMA";
        }

        $ctaBancoNacionDetraccion = "-";
        $codBienDetraccion        = "-";
        $porDetraccion            = "-";
        $mtoDetraccion            = "-";
        $codPaisCliente           = 'PE';
        $codUbigeoCliente         = $_POST['codUbigeoCliente'];
        $desDireccionCliente      = $_POST['desDireccionCliente'];
        $codPaisEntrega           = "-";
        $codUbigeoEntrega         = "-";
        $desDireccionEntrega      = "-";
    }

    // ─────────────────────────────────────────────────────────────
    // INSERTAR REGISTRO EN TABLA factura Y OBTENER ID
    // ─────────────────────────────────────────────────────────────
    $sql_DOC = "INSERT INTO factura (RUC, TIPO, SERIE, COMPROBANTE)
                VALUES ('$RUC', '$TIPO', '$SERIE', '$COMPROBANTE')";

    $conexion->query($sql_DOC);
    $id_factura_impresa = $conexion->insert_id;
    $TIPO_DOC           = $TIPO;
    $ID_TIPO_DOC        = $id_factura_impresa;

    $downloadfile2 = "../efact1.3.4/sunat_archivos/sfs/DATA/{$RUC}-{$TIPO}-{$SERIE}-{$COMPROBANTE}.det";
    $filecontent2  = "";

    $operations = OperationData::getAllProductsBySellId($SELLID);

    // ─────────────────────────────────────────────────────────────
    // HELPER: construye línea .det e inserta en tabla det
    // FIX #2: coma de $mtoBaseIscItem corregida (estaba dentro de las comillas)
    // ─────────────────────────────────────────────────────────────
    $insertarDetalle = function (
        $cantidad, $precio_unitario, $descripcion_producto,
        $mtoValorVentaItem, $mtoPrecioVentaUnitario, $mtoBaseIgvItem
    ) use (
        &$filecontent2, $conexion,
        $TIPO_DOC, $ID_TIPO_DOC,
        $codUnidadMedida, $codProducto, $codProductoSUNAT,
        $sumTotTributosItem, $codTriIGV, $mtoIgvItem,
        $nomTributoIgvItem, $codTipTributoIgvItem, $tipAfeIGV, $porIgvItem,
        $codTriISC, $mtoIscItem, $mtoBaseIscItem,
        $nomTributoIscItem, $codTipTributoIscItem, $tipSisISC, $porIscItem,
        $codTriOtroItem, $mtoTriOtroItem, $mtoBaseTriOtroItem,
        $nomTributoIOtroItem, $codTipTributoIOtroItem, $porTriOtroItem,
        $mtoValorReferencialUnitario
    ) {
        $filecontent2 .=
            $codUnidadMedida . '|' .
            $cantidad . '|' .
            $codProducto . '|' .
            $codProductoSUNAT . '|' .
            $descripcion_producto . '|' .
            $precio_unitario . '|' .
            $sumTotTributosItem . '|' .
            $codTriIGV . '|' .
            $mtoIgvItem . '|' .
            $mtoBaseIgvItem . '|' .
            $nomTributoIgvItem . '|' .
            $codTipTributoIgvItem . '|' .
            $tipAfeIGV . '|' .
            $porIgvItem . '|' .
            $codTriISC . '|' .
            $mtoIscItem . '|' .
            $mtoBaseIscItem . '|' .
            $nomTributoIscItem . '|' .
            $codTipTributoIscItem . '|' .
            $tipSisISC . '|' .
            $porIscItem . '|' .
            $codTriOtroItem . '|' .
            $mtoTriOtroItem . '|' .
            $mtoBaseTriOtroItem . '|' .
            $nomTributoIOtroItem . '|' .
            $codTipTributoIOtroItem . '|' .
            $porTriOtroItem . '|-||||||' .
            $mtoPrecioVentaUnitario . '|' .
            $mtoValorVentaItem . '|' .
            $mtoValorReferencialUnitario . '|' . PHP_EOL;

        // FIX #2: "$mtoBaseIscItem," → "$mtoBaseIscItem", (coma fuera de comillas)
        $sql_DET = "INSERT INTO det (
            TIPO_DOC, ID_TIPO_DOC,
            codUnidadMedida, ctdUnidadItem, codProducto, codProductoSUNAT,
            desItem, mtoValorUnitario, sumTotTributosItem,
            codTriIGV, mtoIgvItem, mtoBaseIgvItem,
            nomTributoIgvItem, codTipTributoIgvItem, tipAfeIGV, porIgvItem,
            codTriISC, mtoIscItem, mtoBaseIscItem,
            nomTributoIscItem, codTipTributoIscItem, tipSisISC, porIscItem,
            codTriOtroItem, mtoTriOtroItem, mtoBaseTriOtroItem,
            nomTributoIOtroItem, codTipTributoIOtroItem, porTriOtroItem,
            mtoPrecioVentaUnitario, mtoValorVentaItem, mtoValorReferencialUnitario
        ) VALUES (
            '$TIPO_DOC', '$ID_TIPO_DOC',
            '$codUnidadMedida', '$cantidad', '$codProducto', '$codProductoSUNAT',
            '$descripcion_producto', '$precio_unitario', '$sumTotTributosItem',
            '$codTriIGV', '$mtoIgvItem', '$mtoBaseIgvItem',
            '$nomTributoIgvItem', '$codTipTributoIgvItem', '$tipAfeIGV', '$porIgvItem',
            '$codTriISC', '$mtoIscItem', '$mtoBaseIscItem',
            '$nomTributoIscItem', '$codTipTributoIscItem', '$tipSisISC', '$porIscItem',
            '$codTriOtroItem', '$mtoTriOtroItem', '$mtoBaseTriOtroItem',
            '$nomTributoIOtroItem', '$codTipTributoIOtroItem', '$porTriOtroItem',
            '$mtoPrecioVentaUnitario', '$mtoValorVentaItem', '$mtoValorReferencialUnitario'
        )";

        $conexion->query($sql_DET);
    };

    // ─────────────────────────────────────────────────────────────
    // HELPER: devuelve stock usando ID (consistente con boleta)
    // FIX #5: reemplaza sumar_stock_name() por update_stock() con ID
    // ─────────────────────────────────────────────────────────────
    $devolverStock = function ($product, $cantidad) use ($SELLID) {
        if ($product->is_stock != 0) {
            $p_sumar        = new ProductData();
            $p_sumar->id    = $product->id;
            $p_sumar->stock = $product->stock + $cantidad;
            $p_sumar->update_stock();

            $op                    = new OperationData();
            $op->product_id        = $product->id;
            $op->operation_type_id = 1;
            $op->sell_id           = $SELLID;
            $op->q                 = $cantidad;
            $op->add();
        }
    };

    // ─────────────────────────────────────────────────────────────
    // PROCESAMIENTO POR TIPO DE NOTA
    // FIX #3: todos los casos al mismo nivel con elseif encadenado
    // ─────────────────────────────────────────────────────────────

    // TIPOS 01, 02, 06 — Anulación / devolución total
    if ($TIPO_NOTA == '01' || $TIPO_NOTA == '02' || $TIPO_NOTA == '06') {

        $sumTotValVenta = 0;
        $sumPrecioVenta = 0;

        foreach ($operations as $item) {
            // FIX #1: se usa $product (consistente) en lugar de $producto
            $product  = $item->getProduct();
            $cantidad = ($product->is_stock == 0) ? 1 : $item->q;

            $precio_unitario      = $item->prec_alt;
            $descripcion_producto = $product->name; // FIX #1: antes era $product->name pero $product no estaba definido en este scope

            $devolverStock($product, $cantidad);

            $mtoValorVentaItem      = $cantidad * $precio_unitario;
            $mtoPrecioVentaUnitario = $precio_unitario;
            $mtoBaseIgvItem         = $mtoValorVentaItem;

            $insertarDetalle(
                $cantidad, $precio_unitario, $descripcion_producto,
                $mtoValorVentaItem, $mtoPrecioVentaUnitario, $mtoBaseIgvItem
            );

            $sumTotValVenta += $mtoValorVentaItem;
            $sumPrecioVenta  = $sumTotValVenta + $sumTotTributos;
        }

    // TIPO 03 — Corrección de descripción
    } elseif ($TIPO_NOTA == '03') {

        $data           = $_POST['arraydet'];
        $sumTotValVenta = 0;
        $sumPrecioVenta = 0;

        foreach ($operations as $item) {
            $product = $item->getProduct();

            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i][0] == $product->name) {

                    $cantidad             = $item->q;
                    $precio_unitario      = $item->prec_alt;
                    $descripcion_producto = $data[$i][1]; // nueva descripción

                    $devolverStock($product, $cantidad);

                    $mtoValorVentaItem      = $cantidad * $precio_unitario;
                    $mtoPrecioVentaUnitario = $precio_unitario;
                    $mtoBaseIgvItem         = $mtoValorVentaItem;

                    $insertarDetalle(
                        $cantidad, $precio_unitario, $descripcion_producto,
                        $mtoValorVentaItem, $mtoPrecioVentaUnitario, $mtoBaseIgvItem
                    );

                    $sumTotValVenta += $mtoValorVentaItem;
                    $sumPrecioVenta  = $sumTotValVenta + $sumTotTributos;
                }
            }
        }

    // TIPO 04 — Descuento global
    } elseif ($TIPO_NOTA == '04') {

        $cantidad             = 1;
        $precio_unitario      = $_POST['dscto'];
        $descripcion_producto = $MOTIVO;
        $mtoValorVentaItem    = $_POST['dscto'];
        $mtoPrecioVentaUnitario = $precio_unitario;
        $mtoBaseIgvItem       = $mtoValorVentaItem;

        $insertarDetalle(
            $cantidad, $precio_unitario, $descripcion_producto,
            $mtoValorVentaItem, $mtoPrecioVentaUnitario, $mtoBaseIgvItem
        );

        $sumTotValVenta = $cantidad * $precio_unitario;
        $sumPrecioVenta = $sumTotValVenta + $sumTotTributos;

    // TIPO 05 — Corrección de precio
    } elseif ($TIPO_NOTA == '05') {

        $data           = $_POST['arraydet'];
        $sumTotValVenta = 0;
        $sumPrecioVenta = 0;

        foreach ($operations as $item) {
            $product = $item->getProduct();

            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i][0] == $product->name) {

                    $cantidad             = $item->q;
                    $precio_unitario      = $data[$i][1]; // nuevo precio
                    $descripcion_producto = $data[$i][0];
                    $mtoValorVentaItem    = $data[$i][1];
                    $mtoPrecioVentaUnitario = $precio_unitario;
                    $mtoBaseIgvItem       = $mtoValorVentaItem;

                    $insertarDetalle(
                        $cantidad, $precio_unitario, $descripcion_producto,
                        $mtoValorVentaItem, $mtoPrecioVentaUnitario, $mtoBaseIgvItem
                    );

                    $sumTotValVenta += $cantidad * $precio_unitario;
                    $sumPrecioVenta  = $sumTotValVenta + $sumTotTributos;
                }
            }
        }

    // TIPO 07 — Devolución parcial
    // FIX #3: antes estaba como elseif de '05', ahora es independiente
    } elseif ($TIPO_NOTA == '07') {

        $data           = $_POST['arraydet'];
        $sumTotValVenta = 0;
        $sumPrecioVenta = 0;

        foreach ($operations as $item) {
            $product = $item->getProduct();

            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i][0] == $product->name) {

                    $cantidad             = $data[$i][1]; // cantidad a devolver
                    $precio_unitario      = $item->prec_alt;
                    $descripcion_producto = $data[$i][0];

                    $devolverStock($product, $cantidad);

                    $mtoValorVentaItem      = $cantidad * $precio_unitario;
                    $mtoPrecioVentaUnitario = $precio_unitario;
                    $mtoBaseIgvItem         = $mtoValorVentaItem;

                    $insertarDetalle(
                        $cantidad, $precio_unitario, $descripcion_producto,
                        $mtoValorVentaItem, $mtoPrecioVentaUnitario, $mtoBaseIgvItem
                    );

                    $sumTotValVenta += $mtoValorVentaItem;
                    $sumPrecioVenta  = $sumTotValVenta + $sumTotTributos;
                }
            }
        }
    }

    // ─────────────────────────────────────────────────────────────
    // LEYENDA — monto en letras (solo para tipos que recalculan totales)
    // ─────────────────────────────────────────────────────────────
    if ($TIPO_NOTA == '03' || $TIPO_NOTA == '04' || $TIPO_NOTA == '05' || $TIPO_NOTA == '07') {
        $numLetra   = NumeroLetras::convertir(number_format($sumPrecioVenta, 2, '.', ','));
        $desLeyenda = $numLetra;
    }

    // ─────────────────────────────────────────────────────────────
    // ESCRIBIR ARCHIVO .det
    // ─────────────────────────────────────────────────────────────
    $ar = fopen($downloadfile2, "a") or die("Error al crear .det");
    fwrite($ar, $filecontent2);
    fclose($ar);

    // ─────────────────────────────────────────────────────────────
    // INSERT tabla nota (.not / CAB)
    // ─────────────────────────────────────────────────────────────
    $sql_CAB = "INSERT INTO nota (
        TIPO_DOC, ID_TIPO_DOC,
        tipOperacion, fecEmision, horEmision,
        codLocalEmisor, tipDocUsuario, numDocUsuario, rznSocialUsuario,
        tipMoneda, codTipoNota, descMotivo, tipDocModifica, serieDocModifica,
        sumTotTributos, sumTotValVenta, sumPrecioVenta,
        sumDescTotal, sumOtrosCargos, sumTotalAnticipos,
        sumImpVenta, ublVersionId, customizationId
    ) VALUES (
        '$TIPO_DOC', '$ID_TIPO_DOC',
        '$tipOperacion', DATE(NOW()), TIME(NOW()),
        '$codLocalEmisor', '$tipDocUsuario', '$numDocUsuario', '$rznSocialUsuario',
        '$tipMoneda', '$codTipoNota', '$descMotivo', '$tipDocModifica', '$serieDocModifica',
        '$sumTotTributos', '$sumTotValVenta', '$sumPrecioVenta',
        '$sumDescTotal', '$sumOtrosCargos', '$sumTotalAnticipos',
        '$sumImpVenta', '$ublVersionId', '$customizationId'
    )";

    $conexion->query($sql_CAB);

    // ─────────────────────────────────────────────────────────────
    // ESCRIBIR ARCHIVO .not (CAB)
    // ─────────────────────────────────────────────────────────────
    $downloadfile = "../efact1.3.4/sunat_archivos/sfs/DATA/{$RUC}-{$TIPO}-{$SERIE}-{$COMPROBANTE}.not";

    $filecontent =
        $tipOperacion . '|' .
        $fecEmision . '|' .
        $horEmision . '|' .
        $codLocalEmisor . '|' .
        $tipDocUsuario . '|' .
        $numDocUsuario . '|' .
        $rznSocialUsuario . '|' .
        $tipMoneda . '|' .
        $codTipoNota . '|' .
        $descMotivo . '|' .
        $tipDocModifica . '|' .
        $serieDocModifica . '|' .
        $sumTotTributos . '|' .
        $sumTotValVenta . '|' .
        $sumPrecioVenta . '|' .
        $sumDescTotal . '|' .
        $sumOtrosCargos . '|' .
        $sumTotalAnticipos . '|' .
        $sumImpVenta . '|' .
        $ublVersionId . '|' .
        $customizationId . '|';

    $ar = fopen($downloadfile, "a") or die("Error al crear .not");
    fwrite($ar, $filecontent);
    fclose($ar);

    // ─────────────────────────────────────────────────────────────
    // INSERT tabla tri + ARCHIVO .tri
    // ─────────────────────────────────────────────────────────────
    $downloadfile3 = "../efact1.3.4/sunat_archivos/sfs/DATA/{$RUC}-{$TIPO}-{$SERIE}-{$COMPROBANTE}.tri";

    $filecontent3 =
        $ideTributo . '|' .
        $nomTributo . '|' .
        $codTipTributo . '|' .
        $mtoBaseImponible . '|' .
        $mtoTributo . '|';

    $ar = fopen($downloadfile3, "a") or die("Error al crear .tri");
    fwrite($ar, $filecontent3);
    fclose($ar);

    $sql_TRI = "INSERT INTO tri (
        TIPO_DOC, ID_TIPO_DOC,
        ideTributo, nomTributo, codTipTributo, mtoBaseImponible, mtoTributo
    ) VALUES (
        '$TIPO_DOC', '$ID_TIPO_DOC',
        '$ideTributo', '$nomTributo', '$codTipTributo',
        '$mtoBaseImponible', '$mtoTributo'
    )";

    $conexion->query($sql_TRI);

    // ─────────────────────────────────────────────────────────────
    // INSERT tabla ley + ARCHIVO .ley
    // ─────────────────────────────────────────────────────────────
    $downloadfile4 = "../efact1.3.4/sunat_archivos/sfs/DATA/{$RUC}-{$TIPO}-{$SERIE}-{$COMPROBANTE}.ley";

    $filecontent4 = $codLeyenda . '|' . $desLeyenda . '|';

    $ar = fopen($downloadfile4, "a") or die("Error al crear .ley");
    fwrite($ar, $filecontent4);
    fclose($ar);

    $sql_LEY = "INSERT INTO ley (
        TIPO_DOC, ID_TIPO_DOC, codLeyenda, desLeyenda
    ) VALUES (
        '$TIPO_DOC', '$ID_TIPO_DOC', '$codLeyenda', '$desLeyenda'
    )";

    $conexion->query($sql_LEY);

    echo "true";
    return true;
}
?>