<?php
class Not_1_2Data {
	public static $tablename = "nota";
	public $serieDocModifica;
	public $fecEmision;
	public $horEmision;
	public $id;
	public $codTipoNota;
	public $numDocUsuario;
	public $rznSocialUsuario;
	public $descMotivo;

	public function Not_1_2Data(){
		//CABECERA CAB
		$this->RUC = "";
		$this->TIPO = "";
		$this->SERIE = "";
		$this->COMPROBANTE = "";
	}

	public static function get_notas_credito_factura_x_fecha($start, $end){
		$sql = "SELECT n.*,f.SERIE,f.COMPROBANTE FROM  nota as n, factura as f
		 		WHERE date(n.fecEmision) >= \"$start\" and date(n.fecEmision) <= \"$end\"
		 		AND n.TIPO_DOC = 7 AND n.tipDocModifica = 1 and n.id_tipo_doc = f.id "; 

		$query = Executor::doit($sql);

		return Model::many($query[0],new Not_1_2Data());
	}	

	public static function get_notas_credito_boleta_x_fecha($start, $end){
		$sql = "SELECT a.*, b.* FROM  nota a, boleta b  
		WHERE date(a.fecEmision) >= \"$start\" and date(a.fecEmision) <= \"$end\"
		 AND TIPO_DOC = 7 AND tipDocModifica = 3 and a.id_tipo_doc = b.id ";

		$query = Executor::doit($sql);

		return Model::many($query[0],new Not_1_2Data());
	}


	public static function get_notas_debito_factura_x_fecha($start, $end){
		$sql = "SELECT * FROM  nota  
		WHERE date(fecEmision) >= \"$start\" and date(fecEmision) <= \"$end\"
		 AND TIPO_DOC = 8 AND tipDocModifica = 1 ";

		$query = Executor::doit($sql);

		return Model::many($query[0],new Not_1_2Data());
	}	

	public static function get_notas_debito_boleta_x_fecha($start, $end){
		$sql = "SELECT * FROM  nota  
		WHERE date(fecEmision) >= \"$start\" and date(fecEmision) <= \"$end\"
		 AND TIPO_DOC = 8 AND tipDocModifica = 3 ";

		$query = Executor::doit($sql);

		return Model::many($query[0],new Not_1_2Data());
	}

	public static function getByIdComprobado($m){
		/**select NC.*, FT.SERIE, FT.COMPROBANTE FROM nota AS NC
INNER JOIN factura AS FT ON(FT.id = NC.ID_TIPO_DOC) 
where NC.serieDocModifica='F001-00000005' */
		$sql  = "SELECT NC.*, FT.SERIE, FT.COMPROBANTE FROM ".self::$tablename." AS NC ";
		$sql .= "INNER JOIN factura AS FT ON(FT.id = NC.ID_TIPO_DOC) where NC.serieDocModifica='$m'";
		$query = Executor::doit($sql);
		$found = null;
		$data = new Boleta2Data();
		while($r = $query[0]->fetch_array()){
			$data->id = $r['id'];
			$data->TIPO_DOC = $r['TIPO_DOC'];
			$data->ID_TIPO_DOC = $r['ID_TIPO_DOC'];
			$data->ESTADO = $r['ESTADO'];
			$data->tipOperacion = $r['tipOperacion'];
			$data->fecEmision = $r['fecEmision'];
			$data->horEmision = $r['horEmision'];
			$data->codLocalEmisor = $r['codLocalEmisor'];
			$data->tipDocUsuario = $r['tipDocUsuario'];
			$data->numDocUsuario = $r['numDocUsuario'];
			$data->rznSocialUsuario = $r['rznSocialUsuario'];
			$data->tipMoneda = $r['tipMoneda'];
			$data->codTipoNota = $r['codTipoNota'];
			$data->descMotivo = $r['descMotivo'];
			$data->tipDocModifica = $r['tipDocModifica'];
			$data->serieDocModifica = $r['serieDocModifica'];
			$data->sumTotTributos = $r['sumTotTributos'];
			$data->sumTotValVenta = $r['sumTotValVenta'];
			$data->sumPrecioVenta = $r['sumPrecioVenta'];
			$data->sumDescTotal = $r['sumDescTotal'];
			$data->sumOtrosCargos = $r['sumOtrosCargos'];
			$data->sumTotalAnticipos = $r['sumTotalAnticipos'];
			$data->sumImpVenta = $r['sumImpVenta'];
			$data->ublVersionId = $r['ublVersionId'];
			$data->customizationId = $r['customizationId'];
			$data->SERIE = $r['SERIE'];
			$data->COMPROBANTE = $r['COMPROBANTE'];

			$found = $data;
			break;
		}
		return $found;
	}



	public static function getById($id, $tipo){

		$sql = "select * from ".self::$tablename." where ID_TIPO_DOC=$id and TIPO_DOC=$tipo";

		// echo $sql; exit();

		$query = Executor::doit($sql);
		$found = null;
		$data = new Not_1_2Data();

		while($r = $query[0]->fetch_array()){
			$data->id = $r['id'];
			$data->TIPO_DOC = $r['TIPO_DOC'];
			$data->ID_TIPO_DOC = $r['ID_TIPO_DOC'];
			$data->ESTADO = $r['ESTADO'];
			$data->tipOperacion = $r['tipOperacion'];
			$data->fecEmision = $r['fecEmision'];
			$data->horEmision = $r['horEmision'];
			$data->codLocalEmisor = $r['codLocalEmisor'];
			$data->tipDocUsuario = $r['tipDocUsuario'];
			$data->numDocUsuario = $r['numDocUsuario'];
			$data->rznSocialUsuario = $r['rznSocialUsuario'];
			$data->tipMoneda = $r['tipMoneda'];
			$data->codTipoNota = $r['codTipoNota'];
			$data->descMotivo = $r['descMotivo'];
			$data->tipDocModifica = $r['tipDocModifica'];
			$data->serieDocModifica = $r['serieDocModifica'];
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
}

?>