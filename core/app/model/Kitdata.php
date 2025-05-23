<?php
/**
 * clase para realizar acciones en tabla paquete y detalle_paq 01/11/2020
 */
class KitData
{
	public static $tablename = "paquete";
	function Kitdata()
	{
		$this->idpaquete   = "";
		$this->imagen      = "";
		$this->barcode     = "";
		$this->nombre      = "";
		$this->descripcion = "";
		$this->precio      = "";
		$this->fecha_cre   = "NOW()";
		$this->fecha_fin   = "NOW()";
		$this->user_id     = "";
		$this->estado      = "";
	}

	public function add()
	{
		$sql = "insert into ".self::$tablename." (barcode,nombre,descripcion,precio,fecha_cre,id_user) ";
		$sql .= "value (\"$this->barcode\",\"$this->nombre\",\"$this->descripcion\",\"$this->precio\",sysdate(),\"$this->user_id\")";
		return Executor::doit($sql);
	}

	public function add_with_image(){
		$sql = "insert into ".self::$tablename." (imagen,barcode,nombre,descripcion,precio,fecha_cre,id_user) ";
		$sql .= "value (\"$this->imagen\",\"$this->barcode\",\"$this->nombre\",\"$this->descripcion\",\"$this->precio\",sysdate(),\"$this->user_id\")";
		return Executor::doit($sql);
	}

	public static function getAll(){
		$sql = "select * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new KitData());
	}

	public static function getAllByPage($start_from,$limit){
		$sql = "select * from ".self::$tablename." where idpaquete>=$start_from limit $limit";
		$query = Executor::doit($sql);
		return Model::many($query[0],new kitData());
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." where idpaquete=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new KitData());

	}

	public static function getLike($str){
		$sql = "select * from ".self::$tablename." where barcode like '%$str%' or nombre like '%$str%' ";
		$query = Executor::doit($sql);
		return Model::many($query[0],new KitData());

	}

	public  static function getBarcode(){
		$sql = "select concat('P',lpad(count(barcode)+1,4,0)) barcode from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new KitData());
	}

	public function update(){
		$sql = "update ".self::$tablename." set barcode=\"$this->barcode\",nombre=\"$this->nombre\",precio=\"$this->precio\",descripcion=\"$this->descripcion\",estado=\"$this->estado\", fecha_upd=sysdate() where idpaquete=$this->idpaquete";
		Executor::doit($sql);
	}
    
    public function updateestado(){
		$sql = "update ".self::$tablename." set estado=\"$this->estado\", fecha_fin=\"$this->fecha_fin\" where idpaquete=$this->idpaquete";
		Executor::doit($sql);
	}
public function update_image(){
		$sql = "update ".self::$tablename." set imagen=\"$this->imagen\" where idpaquete=$this->idpaquete";
		Executor::doit($sql);
	}		
}
?>