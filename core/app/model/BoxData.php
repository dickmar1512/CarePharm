<?php
class BoxData {
	public static $tablename = "box";
	public $id;
	public $name;
	public $created_at;
	public $user_id;
	public $user;

	public function BoxData(){
		$this->name = "";
		$this->lastname = "";
		$this->email = "";
		$this->image = "";
		$this->password = "";
		$this->created_at = date("Y-m-d H:i:s");
	}

	public function add(){
		$sql = "insert into box (created_at,user_id) ";
		$sql .= "value ('$this->created_at',$this->user_id)";
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

// partiendo de que ya tenemos creado un objecto BoxData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set name=\"$this->name\" where id=$this->id";
		Executor::doit($sql);
	}


	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		$found = null;
		$data = new BoxData();
		while($r = $query[0]->fetch_array()){
			$data->id = $r['id'];
			$data->created_at = $r['created_at'];
			$found = $data;
			break;
		}
		return $found;
	}



	public static function getAll(){
		$sql = "select id,created_at,GetFullNameUser(user_id) user from ".self::$tablename;
		$query = Executor::doit(sql: $sql);
		$array = array();
		$cnt = 0;
		while($r = $query[0]->fetch_array()){
			$array[$cnt] = new BoxData();
			$array[$cnt]->id = $r['id'];
			$array[$cnt]->created_at = $r['created_at'];
			$array[$cnt]->user = $r['user'];
			$cnt++;
		}
		return $array;
	}


	public static function getLike($q){
		$sql = "select * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		$array = array();
		$cnt = 0;
		while($r = $query[0]->fetch_array()){
			$array[$cnt] = new BoxData();
			$array[$cnt]->id = $r['id'];
			$array[$cnt]->created_at = $r['created_at'];
			$cnt++;
		}
		return $array;
	}


}

?>