<?php
class Tri_1_2Data {
	public static $tablename = "tri";	
	public $ideTributo;
	public $nomTributo;
	public $codTipTributo;
	public $mtoBaseImponible;
	public $mtoTributo;

	public function Tri_1_2Data(){
	//CABECERA CAB
		$this->RUC = "";
		$this->TIPO = "";
		$this->SERIE = "";
		$this->COMPROBANTE = "";
		$this->ideTributo = "";
		$this->id = "";
		$this->nomTributo = "";
		$this->codTipTributo = "";
		$this->mtoBaseImponible = "";
		$this->mtoTributo = "";
	}

	public static function getById($id,$tipo){
		$sql = "select * from ".self::$tablename." where ID_TIPO_DOC=$id and TIPO_DOC=$tipo";
		$query = Executor::doit($sql);
		$found = null;
		$data = new Tri_1_2Data();
		while($r = $query[0]->fetch_array()){
			$data->id = $r['id'];
			$data->TIPO_DOC = $r['TIPO_DOC'];
			$data->ID_TIPO_DOC= $r['ID_TIPO_DOC'];
			$data->codTipTributo = $r['codTipTributo'];
			$data->mtoBaseImponible = $r['mtoBaseImponible'];
			$data->ideTributo= $r['ideTributo'];
			$data->nomTributo = $r['nomTributo'];
			$data->mtoBaseImponible = $r['mtoBaseImponible'];
			$data->mtoTributo = $r['mtoTributo'];
			$found = $data;
			break;
		}
		return $found;
	}
}

?>