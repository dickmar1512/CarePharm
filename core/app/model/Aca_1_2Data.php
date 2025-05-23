<?php
class Aca_1_2Data {
	public static $tablename = "aca";
	public $desDireccionCliente;

	public function Aca_1_2Data(){
		//CABECERA CAB
		$this->RUC = "";
		$this->TIPO = "";
		$this->SERIE = "";
		$this->COMPROBANTE = "";
		$this->desDireccionCliente = "";
	}

	public static function getById($id, $tipo){

		$sql = "select * from ".self::$tablename." where ID_TIPO_DOC=$id and TIPO_DOC=$tipo";

		$query = Executor::doit($sql);
		$found = null;
		$data = new Aca_1_2Data();

		while($r = $query[0]->fetch_array()){
			$data->id = $r['id'];
			$data->desDireccionCliente = $r['desDireccionCliente'];
			$found = $data;
			break;
		}

		return $found;
	}
}

?>