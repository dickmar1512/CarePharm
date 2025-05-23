<?php
class UserData {
	public static $tablename = "user";
	public $id;
	public $name;
	public $lastname;
	public $username;
	public $email;
	public $montomax;
	public $is_admin;
	public $password;
	public $is_active;
	public $is_caja;
	public $is_dirtec;
	public $is_desc;
	public $created_at;

	public function Userdata(){
		$this->name = "";
		$this->lastname  = "";
		$this->email     = "";
		$this->montomax  = 0;
		$this->is_admin = 0;
		$this->is_caja   = 0;
	    $this->is_dirtec = 0;
		$this->is_active = 1;
		$this->is_desc   = 0;
		$this->username  = "";
		$this->image     = "";
		$this->password  = "";
		$this->created_at = 'sysdate()';
	}

	public function add(){
		$sql = "insert into user (name,lastname,username,email,is_admin,is_caja,is_dirtec,password,created_at) ";
		$sql .= "value ('".$this->name."','".$this->lastname."','".$this->username."','".$this->email."','".$this->is_admin."','".$this->is_caja."','".$this->is_dirtec."','".$this->password."','".$this->created_at."')";
		Executor::doit($sql);
	}

	public static function delById($id){
		$sql = "delete from ".self::$tablename." where id=$id";
		Executor::doit($sql);
	}
	public function del(){
		$sql = "delete from ".self::$tablename." where id=$this->id";
		Executor::doit($sql);
	}

// partiendo de que ya tenemos creado un objecto UserData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set name=\"$this->name\",email=\"$this->email\",montomax=\"$this->montomax\",username=\"$this->username\",lastname=\"$this->lastname\",is_active=\"$this->is_active\",is_admin=\"$this->is_admin\",is_caja=\"$this->is_caja\",is_dirtec=\"$this->is_dirtec\",is_desc=\"$this->is_desc\" where id=$this->id";
		Executor::doit($sql);
	}

	public function update_passwd(){
		$sql = "update ".self::$tablename." set password=\"$this->password\" where id=$this->id";
		Executor::doit($sql);
	}


	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new UserData());

	}

	public static function getByMail($mail){
		$sql = "select * from ".self::$tablename." where email=\"$mail\"";
		$query = Executor::doit($sql);
		return Model::one($query[0],new UserData());

	}


	public static function getAll(){
		$sql = "select * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserData());
	}


	public static function getLike($q){
		$sql = "select * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserData());

	}


}

?>