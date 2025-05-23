<?php
class Det_1_2Data {
	public static $tablename = "det";

	public function Det_1_2Data(){
		//CABECERA CAB
		$this->codUnidadMedida = "";
		$this->ctdUnidadItem = "";
		$this->codProducto = "";
		$this->codProductoSUNAT = "";
		$this->desItem = "";
		$this->mtoValorUnitario = "";
		$this->sumTotTributosItem = "";
		$this->codTriIGV = "";
		$this->mtoIgvItem = "";
		$this->mtoBaseIgvItem = "";
		$this->nomTributoIgvItem = "";
		$this->codTipTributoIgvItem = "";
		$this->tipAfeIGV = "";
		$this->porIgvItem = "";
		$this->codTriISC = "";
		$this->mtoIscItem = "";
		$this->mtoBaseIscItem = "";
		$this->nomTributoIscItem = "";
		$this->codTipTributoIscItem = "";
		$this->tipSisISC = "";
		$this->porIscItem = "";
		$this->codTriOtroItem = "";
		$this->mtoTriOtroItem = "";
		$this->mtoBaseTriOtroItem = "";
		$this->nomTributoIOtroItem = "";
		$this->codTipTributoIOtroItem = "";
		$this->porTriOtroItem = "";
		$this->mtoPrecioVentaUnitario = "";
		$this->mtoValorVentaItem = "";
		$this->mtoValorReferencialUnitario = "";
	}

	public static function getById($id, $tipo){

		$sql = "SELECT ctdUnidadItem, mtoValorUnitario, desItem, mtoValorVentaItem, codTriIGV, mtoIgvItem 
			FROM ".self::$tablename."
			WHERE ID_TIPO_DOC=$id and TIPO_DOC=$tipo";

			$query = Executor::doit($sql);

		return Model::many($query[0],new Det_1_2Data());

	}

	public static function getByIdNota($id, $tipo){

		$sql = "SELECT * FROM ".self::$tablename."
			WHERE ID_TIPO_DOC=$id and TIPO_DOC=$tipo";

			$query = Executor::doit($sql);

		return Model::many($query[0],new Det_1_2Data());

	}

	public static function getByNroDoc($nro){

		$sql = "SELECT * FROM ".self::$tablename."
			INNER JOIN 1_2_factura_yaqha f ON d.ID_TIPO_DOC=f.id
			WHERE CONCAT(f.SERIE,'-',f.COMPROBANTE)=$nro";

			$query = Executor::doit($sql);

		return Model::many($query[0],new Det_1_2Data());

	}

	// public static function getById($id,$tipo){
	// 	$sql = "select * from ".self::$tablename." where ID_TIPO_DOC=$id and TIPO_DOC=$tipo";
	// 	$query = Executor::doit($sql);
	// 	$found = null;
	// 	$data = new Det_1_1Data();
	// 	while($r = $query[0]->fetch_array()){
	// 		$data->id = $r['id'];
	// 		$data->TIPO_DOC = $r['TIPO_DOC'];
	// 		$data->ID_TIPO_DOC= $r['ID_TIPO_DOC'];
	// 		$data->ctdUnidadItem = $r['ctdUnidadItem'];
	// 		$data->mtoValorUnitario = $r['mtoValorUnitario'];
	// 		$data->desItem = $r['desItem'];
	// 		$data->mtoValorVentaItem = $r['mtoValorVentaItem'];
	// 		$data->codTriIGV = $r['codTriIGV'];
	// 		$data->mtoIgvItem = $r['mtoIgvItem'];
	// 		$found = $data;
	// 		break;
	// 	}
	// 	return $found;
	// }
}

?>