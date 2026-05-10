<?php
 #[AllowDynamicProperties]
class Det_kit
 {
 	public static $tablename = "detalle_paq";
 	function Det_kit()
 	{
 		$this->iddetalle = "";
 		$this->idpaquete = "";
 		$this->idprod    = "";
 		$this->precio    = "";
 		$this->descuento = "";
 		$this->cantidad  = "";
 	}

 	public static function getById($id){
		$sql = "select a.iddetalle,a.idpaquete,a.idprod,b.barcode,b.name,a.precio,a.descuento,a.cantidad from ".self::$tablename." a, product b where a.idprod = b.id and  a.idpaquete=$id";
		$query = Executor::doit($sql);
		return Model::many($query[0],new Det_kit());

	}

	public function add()
	{
		$sql = "insert into ".self::$tablename." (idpaquete,idprod,fecha_ing,precio,descuento,cantidad) ";
		$sql .= "value (\"$this->idpaquete\",\"$this->idprod\",sysdate(),\"$this->precio\",\"$this->descuento\",\"$this->cantidad\")";
		return Executor::doit($sql);
	}

	public function delId($iddet)
	{
		$sql = "update detalle_paq set estado=0 where iddetalle = $iddet";
        return Executor::doit($sql);
	}

 }
?>
