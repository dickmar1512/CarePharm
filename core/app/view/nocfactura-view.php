<?php
	
$product = Factura2Data::getByExtra($_GET["id"]);

  ####################### NUMERO DE REGISTRO DE COMPROBANTES ###############################
  $mysqli = new mysqli('localhost','milenio','armagedon','dbcarepharm',3306); //BASE DE DATOS

  $query = $mysqli->prepare("SELECT * FROM factura");//TABLA
  $query->execute();
  $query->store_result();
  $registros_factura = $query->num_rows;

  $id_comprobante_actual_f = $registros_factura + 1;

  function generaCerosComprobante($numero)
  {
    $largo_numero = strlen($numero); //OBTENGO EL LARGO DEL NUMERO
    $largo_maximo = 8; //ESPECIFICO EL LARGO MAXIMO DE LA CADENA
    $agregar = $largo_maximo - $largo_numero; //TOMO LA CANTIDAD DE 0 AGREGAR
    for($i =0; $i<$agregar; $i++)
    {
      $numero = "0".$numero;
    } //AGREGA LOS CEROS
    return $numero; //RETORNA EL NUMERO CON LOS CEROS
  }

  function generaCerosSerie($numero2)
  {
    $largo_numero = strlen($numero2);//OBTENGO EL LARGO DEL NUMERO
    $largo_maximo = 1;//ESPECIFICO EL LARGO MAXIMO DE LA CADENA
    $agregar = $largo_maximo - $largo_numero;   //TOMO LA CANTIDAD DE 0 AGREGAR
    for($i =0; $i<$agregar; $i++)
    {
    	$numero2 = "0".$numero2;
    } //AGREGA LOS CEROS
    return $numero2; //RETORNA EL NUMERO CON LOS CEROS
  }

  //COMPROBANTE PRIMERA SERIE B001
  $NUMERO_COMPROBANTE_F = generaCerosComprobante($id_comprobante_actual_f);
  //CAPTAMOS SERIE DE B001 AL B999
  $NUMERO_SERIE_F = (int)( ( $id_comprobante_actual_f / 99999999 ) + 1 );
  $SERIE_F = "FNC".generaCerosSerie($NUMERO_SERIE_F);
  //COMPROBANTE SERIE B001 AL SUPERIOR
  if ($NUMERO_SERIE_F>1) {  $NUMERO_COMPROBANTE_F = $id_comprobante_actual_f % 99999999; }
  $COMPROBANTE_F=generaCerosComprobante($NUMERO_COMPROBANTE_F);

  #####################################################################################
  $empresa = EmpresaData::getDatos();
?>

<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0" style="color: red;"><i class='fas fa-file-invoice'></i> Nota de credito factura</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
				    <li class="breadcrumb-item"><a href="#">Reportes</a></li>
					<li class="breadcrumb-item active">Nota de credito factura</li>
				</ol>
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
	<div class="container-fluid">
		<div class="card card-default">
			<div class="card-header">
				<h2 class="card-title"><b>* La nota de credito te permitirá anular una FACTURA en caso de que el proceso de venta del servicio o producto se haya anulado.</b></h2>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<fieldset class="content-group">
							<legend class="text-bold">Especificaciones: </legend>
								<div class="form-group col-md-4">
									<label><i class="icon-barcode2 position-left"></i> Serie:</label>
									<input type="text" name="serie_comprobante" id="serie_comprobante" value="<?php echo $SERIE_F; ?>" class="form-control" readonly="readonly">
								</div>
								<div class="form-group col-md-4">
									<label><i class="icon-file-text2 position-left"></i> Número:</label>
									<input type="text" name="numero_comprobante" id="numero_comprobante" value="<?php echo $COMPROBANTE_F; ?>" class="form-control" readonly="readonly">
								</div>
								<!-- <div class="form-group col-md-3">
									<label><i class="icon-calendar2 position-left"></i> Fecha.Doc:</label>
									<input type="date" name="fecha_comprobante" id="fecha_comprobante" placeholder="" class="form-control" readonly="readonly">
								</div>				 -->
								<div class="content_debito_credito" style="display: block;">
									<div class="form-group col-md-4">
										<label><i class="icon-file-text position-left"></i> N° Doc. Modificado:</label>
										<input type="text" name="num_comprobante_modificado" id="num_comprobante_modificado" class="form-control" readonly="readonly" value="<?php echo $product->SERIE."-".$product->COMPROBANTE; ?>">
									</div>
										<div class="form-group has-feedback has-feedback-left col-md-5">
											<label class="col-md-12"><i class="icon-profile position-left"></i>Tipo <span class="text-danger">*</span></label>
											<select title="Selecciona el Tipo" data-placeholder="Selecciona el Tipo" class="form-control" name="notacredito_motivo_id" id="notacredito_motivo_id" required="" tabindex="-1" aria-hidden="true">
												<option value="01">ANULACION DE LA OPERACION</option>
												<option value="02">ANULACION POR ERROR EN EL RUC</option>
												<option value="03">CORRECION POR ERROR EN LA DESCRIPCION</option>
												<option value="04">DESCUENTO GLOBAL</option>
												<option value="05">DESCUENTO POR ITEM</option>
												<option value="06">DEVOLUCION TOTAL</option>
												<option value="07">DEVOLUCION POR ITEM</option>
												<option value="08">BONIFICACION</option>
												<option value="09">DISMINUCION EN EL VALOR</option>
											</select>
									</div>
								</div>
								<div class="form-group col-md-7">
									<label><i class="icon-barcode2 position-left"></i> Motivo:</label>
									<input type="text" name="motivo" id="motivo" value="" class="form-control" required>
								</div>
								<div class="form-group col-md-4" id="dscto_global" style="display:none;">
									<label><i class="icon-barcode2 position-left"></i> Descuento:</label>
									<input type="number" name="dscto" id="dscto" value="" class="form-control" required>
								</div>
								<div class="form-group has-feedback has-feedback-left col-md-12">
								<center>
								<button type="submit" class="btn btn-primary" id="buscar" name="buscar">Continuar</button>
								</center>
								</div>
						</fieldset>

						<div id="datos" name="datos">							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>	