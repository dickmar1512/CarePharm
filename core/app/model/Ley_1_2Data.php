<?php
class Ley_1_2Data {
	public static $tablename = "ley";
	public $codLeyenda;
	public $desLeyenda;

	public function Ley_1_2Data(){
		//CABECERA CAB
		$this->RUC = "";
		$this->TIPO = "";
		$this->SERIE = "";
		$this->COMPROBANTE = "";
	}

	public static function getById($id,$tipo){
		$sql = "select * from ".self::$tablename." where ID_TIPO_DOC=$id and TIPO_DOC=$tipo";
		$query = Executor::doit($sql);
		$found = null;
		$data = new Ley_1_2Data();
		while($r = $query[0]->fetch_array()){
			$data->id = $r['id'];
			$data->TIPO_DOC = $r['TIPO_DOC'];
			$data->ID_TIPO_DOC= $r['ID_TIPO_DOC'];
			$data->codLeyenda = $r['codLeyenda'];
			$data->desLeyenda = $r['desLeyenda'];
			$found = $data;
			break;
		}
		return $found;
	}
}

?>