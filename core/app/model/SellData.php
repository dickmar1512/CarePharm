<?php
class SellData {
	public static $tablename = "sell";
	public $id;
	public $person_id;
	public $user_id;
	public $total;
	public $cash;
	public $discount;
	public $created_at;
	public $tipo_comprobante;
	public $serie;
	public $comprobante;
	public $estado;
	public $tipo_pago;
	public $fecha_emi;
	public $box_id;
	public $descuento;

	public function SellData(){
		$this->created_at = "NOW()";
		$this->fecha_emi  = "";
		$this->descuento = 0;
	}

	public function getPerson(){ return PersonData::getById($this->person_id);}
	public function getUser(){ return UserData::getById($this->user_id);}

	public function add(){
		$sql = "insert into ".self::$tablename." (total, cash, discount,user_id,created_at) ";
		$sql .= "value ($this->total, $this->cash, $this->discount,$this->user_id,$this->created_at)";
		return Executor::doit($sql);
	}

	public function add2(){
		$sql = "insert into ".self::$tablename." (user_id, tipo_comprobante, serie, comprobante, total, cash, discount,created_at, estado, person_id, tipo_pago) ";
		$sql .= "value ($this->user_id, $this->tipo_comprobante, '".$this->serie."', '".$this->comprobante."', $this->total, $this->cash, $this->discount, '".$this->created_at."', 1, $this->person_id, $this->tipo_pago)";
		return Executor::doit($sql);
	}

	public function add_re(){
		$sql = "insert into ".self::$tablename." (user_id,operation_type_id,created_at) ";
		$sql .= "value ($this->user_id,1,$this->created_at)";
		return Executor::doit($sql);
	}

	public function add_re2(){
		$sql = "insert into ".self::$tablename." (person_id,tipo_comprobante, serie, comprobante,fecha_emi, user_id,operation_type_id,created_at,total,cash,discount) ";
		$sql .= "value ($this->person_id,$this->tipo_comprobante, '".$this->serie."', lpad($this->comprobante,8,0), STR_TO_DATE('".$this->fecha_emi."','%d/%m/%Y'), $this->user_id,1,$this->created_at,$this->total,$this->cash,$this->discount)";
		return Executor::doit($sql);
	}


	public function add_with_client(){
		$sql = "insert into ".self::$tablename." (tipo_comprobante, serie, comprobante,fecha_emi, total, discount, person_id, user_id, created_at, estado,tipo_pago) ";
		$sql .= "value ($this->tipo_comprobante, '".$this->serie."', '".$this->comprobante."', STR_TO_DATE('".$this->fecha_emi."','%d/%m/%Y'),$this->total,$this->discount,$this->person_id,$this->user_id,'".$this->created_at."','1','1')";
		return Executor::doit($sql);
	}

	public function add_re_with_client(){
		$sql = "insert into ".self::$tablename." (person_id,operation_type_id,user_id,created_at,tipo_comprobante,serie,comprobante,fecha_emi,total,cash,discount) ";
		$sql .= "value ($this->person_id,1,$this->user_id,NOW(),$this->tipo_comprobante,'".$this->serie."',lpad($this->comprobante,8,0), STR_TO_DATE('".$this->fecha_emi."','%d/%m/%Y'),$this->total,$this->cash,0)";
		return Executor::doit($sql);
	}

	public static function delById($id){
		$sql = "delete from ".self::$tablename." where id=$id";
		Executor::doit($sql);
	}

	public function del(){
		$sql = "delete from ".self::$tablename." where id=$this->id";
		Executor::doit($sql);
	}

	public function update_box(){
		$sql = "update ".self::$tablename." set box_id=$this->box_id where id=$this->id";
		Executor::doit($sql);
	}

	public function update_proforma_venta(){
		$sql = "update ".self::$tablename." set estado = 1 where id=$this->id";
		return Executor::doit($sql);
	}

	public static function getById($id){
		 $sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new SellData());
	}

	public static function getByNroDoc($num){
		 $sql = "select * from ".self::$tablename." where CONCAT(SERIE,'-',COMPROBANTE)='$num'";
		$query = Executor::doit($sql);
		return Model::one($query[0],new SellData());
	}

	public static function getSells(){
		$sql = "select * from ".self::$tablename." where operation_type_id=2 AND tipo_comprobante!=70 AND estado != 2 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());
	}

	public static function getSellsOv(){
		$sql = "select * from ".self::$tablename." where operation_type_id=2 AND tipo_comprobante=70 AND tipo_comprobante!=70 AND estado != 2 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());
	}

	public static function getSellsXfecha($inicio,$fin){
		$sql = "select * from ".self::$tablename.
		       " where  date(created_at) >= '$inicio' ".
               "and date(created_at) <= '$fin' ".
               "and operation_type_id=2 AND tipo_comprobante!=70 AND estado != 2 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());
	}

	public static function getSellsXfechaOv($inicio,$fin){
		$sql = "select * from ".self::$tablename.
		       " where  date(created_at) >= '$inicio' ".
               "and date(created_at) <= '$fin' ".
               "and operation_type_id=2 AND tipo_comprobante =70 AND estado != 2 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());
	}

	public static function getSellsXfechaUsuario($inicio,$fin,$user_id){
		$sql = "select * from ".self::$tablename.
		       " where  date(created_at) >= '$inicio' ".
               "and date(created_at) <= '$fin' ".
               "and operation_type_id=2 AND tipo_comprobante!=70 AND estado != 2 and user_id = '$user_id' order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());
	}

	public static function getSellsXfechaUsuarioOv($inicio,$fin,$user_id){
		$sql = "select * from ".self::$tablename.
		       " where  date(created_at) >= '$inicio' ".
               "and date(created_at) <= '$fin' ".
               "and operation_type_id=2 AND tipo_comprobante=70 AND estado != 2 and user_id = '$user_id' order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());
	}

	public static function getProformas(){
		$sql = "select * from ".self::$tablename." where operation_type_id=2 AND estado = 2 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());
	}

	public static function getSellsUnBoxed(){
		$sql = "select id, serie, comprobante, created_at,GetUserName(user_id) as user from ".self::$tablename." where operation_type_id=2 and box_id is NULL order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());
	}

	public static function getByBoxId($id){
		$sql = "select id, serie, comprobante, created_at,GetFullNameUser(user_id) as user from ".self::$tablename." where operation_type_id=2 and box_id=$id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());
	}

	public static function getRes(){
		$sql = "select * from ".self::$tablename." where operation_type_id=1 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());
	}

	public static function getAllByPage($start_from,$limit){
		$sql = "select * from ".self::$tablename." where id<=$start_from limit $limit";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());

	}

	public static function get_ventas_x_id($id)
	{
		$sql = "SELECT sel.id, tipo_comprobante, serie, person_id, comprobante, total, per.numero_documento, CONCAT(per.name, ' ', per.lastname) as name, per.address1, sel.created_at
				FROM sell sel
				LEFT JOIN person per ON per.id = sel.person_id
				WHERE sel.id = $id LIMIT 1";

				// echo $sql; exit;

		$query = Executor::doit($sql);

		return Model::one($query[0],new SellData());
	}

	public static function getAllByDateOp($start,$end,$op){
  		$sql = "select * from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and operation_type_id=$op order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());

	}

	public static function getAllByDateBCOp($clientid,$start,$end,$op){
 		$sql = "select * from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and person_id=$clientid  and operation_type_id=$op order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());

	}


	public static function getAllByDateOpProductos($start,$end,$op,$user_id){
  		$sql = "select a.serie,a.comprobante,a.total, b.q, b.product_id, GetFullNameProduct(b.product_id) prod,b.prec_alt, b.created_at, a.user_id, GetFullNameUser(a.user_id) AS vendedor
         from ".self::$tablename." a, operation b 
         where date(a.created_at) >= \"$start\" 
         and date(a.created_at) <= \"$end\" 
          and a.operation_type_id=$op 
         AND a.id = b.sell_id ";

        if($user_id <> 0)
        {
        	$sql .= "and a.user_id = \"$user_id\" ";
        }    
       $sql .=  "order by a.created_at desc";

		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());

	}

	/*****Agregue esto para kardex*****/
	public static function getAllByKardexProd($idprod,$start,$end){
 $sql = "select GetFullNameProduct(a.product_id) prod, GetFullNamePerson(b.person_id) nombre, ".
        "CONCAT(b.serie,'-',b.comprobante) comp, ".
        "(CASE a.operation_type_id WHEN 2 THEN 'VENTA' ELSE 'COMPRA' END) tipo, ".
        "a.q,a.prec_alt,a.descuento,a.cu,a.created_at ".
        "from operation a, ".self::$tablename." b ".
        "where a.sell_id = b.id ".
        "and date(a.created_at) >= '$start' ".
        "and date(a.created_at) <= '$end' ".
        "and a.product_id='$idprod'  ".
        "order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());

	}

	public static function getAllByKardex($start,$end){
 $sql = "select GetFullNameProduct(a.product_id) prod, GetFullNamePerson(b.person_id) nombre, ".
        "CONCAT(b.serie,'-',b.comprobante) comp, ".
        "(CASE a.operation_type_id WHEN 2 THEN 'VENTA' ELSE 'COMPRA' END) tipo, ".
        "a.q,a.prec_alt,a.descuento,a.cu,a.created_at ".
        "from operation a, ".self::$tablename." b ".
        "where a.sell_id = b.id ".
        "and date(a.created_at) >= '$start' ".
        "and date(a.created_at) <= '$end' ".
        "order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());
	}

	/**********************************/

	public function update_created_at()
	{
		$sql = "update ".self::$tablename." set created_at=$this->created_at where id=$this->id";
		Executor::doit($sql);
	}
       
       public function updateTotalReab($sell_id)
       {
       	$sql = "update ".self::$tablename." set total = (select sum(q*prec_alt)  from operation where sell_id = ".$sell_id."), cash = (select sum(q*prec_alt) from operation where sell_id = ".$sell_id.") where id = ".$sell_id;
       	Executor::doit($sql);
       }
}

?>