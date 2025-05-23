<?php
class Cab_1_2Data {
	public static $tablename = "cab";
	public $numDocUsuario;
	public $rznSocialUsuario;
	public $fecEmision;
	public $horEmision;
	public $id;
	public $sumDescTotal;
	public $tipOperacion;
	public $fecVencimiento;
	public $codLocalEmisor;
	public $tipDocUsuario;
	public $tipMoneda;
	public $sumTotTributos;
	public $sumTotValVenta;
	public $sumPrecioVenta;
	public $sumOtrosCargos;
	public $sumTotalAnticipos;
	public $sumImpVenta;
	public $ublVersionId;
	public $customizationId;

	public function Cab_1_2Data(){
		//CABECERA CAB
		$this->RUC = "";
		$this->TIPO = "";
		$this->SERIE = "";
		$this->COMPROBANTE = "";
	}

	public static function getById($id, $tipo){

		$sql = "select * from ".self::$tablename." where ID_TIPO_DOC=$id and TIPO_DOC=$tipo";

		$query = Executor::doit($sql);
		$found = null;
		$data = new Cab_1_2Data();

		while($r = $query[0]->fetch_array()){
			$data->id = $r['id'];
			$data->TIPO_DOC = $r['TIPO_DOC'];
			$data->ID_TIPO_DOC = $r['ID_TIPO_DOC'];
			$data->tipOperacion = $r['tipOperacion'];
			$data->fecEmision = $r['fecEmision'];
			$data->horEmision = $r['horEmision'];
			$data->fecVencimiento = $r['fecVencimiento'];
			$data->codLocalEmisor = $r['codLocalEmisor'];
			$data->tipDocUsuario = $r['tipDocUsuario'];
			$data->numDocUsuario = $r['numDocUsuario'];
			$data->rznSocialUsuario = $r['rznSocialUsuario'];
			$data->tipMoneda = $r['tipMoneda'];
			$data->sumTotTributos = $r['sumTotTributos'];
			$data->sumTotValVenta = $r['sumTotValVenta'];
			$data->sumPrecioVenta = $r['sumPrecioVenta'];
			$data->sumDescTotal = $r['sumDescTotal'];
			$data->sumOtrosCargos = $r['sumOtrosCargos'];
			$data->sumTotalAnticipos = $r['sumTotalAnticipos'];
			$data->sumImpVenta = $r['sumImpVenta'];
			$data->ublVersionId = $r['ublVersionId'];
			$data->customizationId = $r['customizationId'];
			$found = $data;
			break;
		}

		return $found;
	}
	public function update_fecha(){
		$sql = "update ".self::$tablename." set fecEmision='".$this->fecEmision."', horEmision = '".$this->horEmision."' where id=$this->id";
		Executor::doit($sql);
	}
}

?>